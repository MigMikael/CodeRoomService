<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 6/19/2017
 * Time: 8:28
 */
namespace App\Traits;

use GuzzleHttp\Client;

trait EvaluatorTrait
{
    public function getSubjectName($problem)
    {
        $subjectName = $problem->lesson->course->name;
        $subjectName = str_replace(' ', '', $subjectName);
        $subjectName = strtolower($subjectName);

        return $subjectName;
    }

    public function checkInputVersion($problem)
    {
        $evaluator_ip = env('EVALUATOR_IP');
        $subjectName = self::getSubjectName($problem);

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/check_in?subject='.$subjectName.'&problem='.$problem->name;
        //$url = 'http://posttestserver.com/post.php?subject='.$subjectName.'&problem='.$problem->name;

        $response = $client->request('GET', $url);
        $result = $response->getBody();
        //Log::info('#### checkInputVersion '. $response->getBody());
        $json = json_decode($result, true);

        return $json;
    }

    public function checkOutputVersion($problem)
    {
        $evaluator_ip = env('EVALUATOR_IP');
        $subjectName = self::getSubjectName($problem);

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/check_sol?subject='.$subjectName.'&problem='.$problem->name;
        //$url = 'http://posttestserver.com/post.php?subject='.$subjectName.'&problem='.$problem->name;

        $response = $client->request('GET', $url);
        $result = $response->getBody();
        //Log::info('#### checkOutputVersion '. $response->getBody());
        $json = json_decode($result, true);

        return $json;
    }

    public function sendNewInput($problem)
    {
        $evaluator_ip = env('EVALUATOR_IP');
        $subjectName = self::getSubjectName($problem);

        $inputs = [];
        $inputs['subject'] = $subjectName;
        $inputs['problem'] = $problem->name;
        $inputs['in'] = [];

        foreach ($problem->problemFiles as $problemFile){
            foreach ($problemFile->inputs as $input){
                $realInput = [
                    'version' => $input->version,
                    'filename' => $input->filename,
                    'content' => $input->content,
                ];
                array_push($inputs['in'], $realInput);
            }
        }

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/send_in';
        //$url = 'http://www.posttestserver.com/post.php';

        $res = $client->request('POST', $url, [
            'json' => [
                'subject' => $inputs['subject'],
                'problem' => $inputs['problem'],
                'in' => $inputs['in'],
            ]
        ]);

        $result = $res->getBody();
        return $result;
    }

    public function sendNewOutput($problem)
    {
        $evaluator_ip = env('EVALUATOR_IP');
        $subjectName = self::getSubjectName($problem);

        $outputs = [];
        $outputs['subject'] = $subjectName;
        $outputs['problem'] = $problem->name;
        $outputs['sol'] = [];

        foreach ($problem->problemFiles as $problemFile){
            foreach ($problemFile->outputs as $output){
                $realOutput = [
                    'version' => $output->version,
                    'filename' => $output->filename,
                    'content' => $output->content,
                ];
                array_push($outputs['sol'], $realOutput);
            }
        }

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/send_sol';
        //$url = 'http://www.posttestserver.com/post.php';

        $res = $client->request('POST', $url, ['json' => [
            'subject' => $outputs['subject'],
            'problem' => $outputs['problem'],
            'sol' => $outputs['sol'],
        ]
        ]);

        $result = $res->getBody();
        return $result;
    }

    public function evaluateFile($submission)
    {
        $problem = $submission->problem;
        $evaluator_ip = env('EVALUATOR_IP');
        $subjectName = self::getSubjectName($problem);

        $data = [];

        $problemFile = $problem->problemFiles->first();
        $data['number'] = $problemFile->inputs->count();
        $data['file'] = [];

        foreach ($submission->submissionFiles as $submissionFile){
            $code = $submissionFile->code;
            $is_main = false;
            $main = strpos($code, 'main');
            if($main != false){
                $args = strpos($code, '(', $main);
                $args1 = strpos($code, 'String', $args);
                $args2 = strpos($code, '[]', $args1);

                if($args != false && $args1 != false && $args2 != false){
                    $is_main = true;
                }
            }

            $package = $submissionFile->package;
            if($package == 'default package'){
                $package = '';
            } else {
                $package = str_replace('.','/', $package);
                $package .= '/';
            }

            $temps = explode('.', $submissionFile->filename);
            $fileName = $temps[0];

            $dataFile = [
                'package' => $package,
                'filename' => $fileName,
                'code' => $submissionFile->code,
                'is_main' => $is_main
            ];
            array_push($data['file'], $dataFile);
        }

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/evaluate';
        //$url = 'http://www.posttestserver.com/post.php';

        $res = $client->request('POST', $url, [
            'json' => [
                'time_out' => strval($problem->timelimit),
                'mem_size' => strval($problem->memorylimit),
                'number' => $data['number'],
                'subject' => $subjectName,
                'problem' => $problem->name,
                'file' => $data['file'],
            ]
        ]);

        $result = $res->getBody();
        $json = json_decode($result, true);
        return $json;
    }
}