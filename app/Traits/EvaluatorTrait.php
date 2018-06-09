<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 6/19/2017
 * Time: 8:28
 */
namespace App\Traits;

use GuzzleHttp\Client;
use App\ProblemInput;
use Log;

trait EvaluatorTrait
{
    protected $evaluator_IP = "172.27.229.201:3000";
    public function getSubjectName($problem)
    {
        $subjectName = $problem->lesson->course->name;
        $subjectName = str_replace(' ', '', $subjectName);
        $subjectName = strtolower($subjectName);

        return $subjectName;
    }

    public function checkInputVersion($problem, $hasDriver)
    {
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($problem);

        $client = new Client();
        if($hasDriver){
            $url = $evaluator_ip.'/api/teacher/check_in_driver?subject='.$subjectName.'&problem='.$problem->name;
            //$url = 'http://posttestserver.com/post.php?subject='.$subjectName.'&problem='.$problem->name;

        }else{
            $url = $evaluator_ip.'/api/teacher/check_in?subject='.$subjectName.'&problem='.$problem->name;
            //$url = 'http://posttestserver.com/post.php?subject='.$subjectName.'&problem='.$problem->name;
        }

        $response = $client->request('GET', $url);
        $result = $response->getBody();
        Log::info('#### checkInputVersion '. $response->getBody());

        Log::info('status code : '. $response->getStatusCode());
        Log::info('content-type : '. $response->getHeader('content-type')[0]);

        $json = json_decode($result, true);

        return $json;
    }

    public function checkOutputVersion($problem, $hasDriver)
    {
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($problem);

        $client = new Client();
        if($hasDriver){
            $url = $evaluator_ip.'/api/teacher/check_sol_driver?subject='.$subjectName.'&problem='.$problem->name;
            //$url = 'http://posttestserver.com/post.php?subject='.$subjectName.'&problem='.$problem->name;

        }else{
            $url = $evaluator_ip.'/api/teacher/check_sol?subject='.$subjectName.'&problem='.$problem->name;
            //$url = 'http://posttestserver.com/post.php?subject='.$subjectName.'&problem='.$problem->name;
        }

        $response = $client->request('GET', $url);
        $result = $response->getBody();
        Log::info('#### checkOutputVersion '. $response->getBody());
        $json = json_decode($result, true);

        return $json;
    }

    public function sendNewInput($problem)
    {
        $evaluator_ip = $this->evaluator_IP;
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

    public function sendNewInput2($problem)
    {
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($problem);

        $inputs = [];
        $inputs['subject'] = $subjectName;
        $inputs['problem'] = $problem->name;
        $inputs['in'] = [];

        foreach ($problem->problemFiles as $problemFile){
            $temps = explode('.', $problemFile->filename);
            $package = $temps[0];
            foreach ($problemFile->inputs as $input){
                $realInput = [
                    'version' => $input->version,
                    'filename' => $input->filename,
                    'content' => $input->content,
                    'package' => $package
                ];
                array_push($inputs['in'], $realInput);
            }
        }

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/send_in_driver';
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
        $evaluator_ip = $this->evaluator_IP;
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

        $res = $client->request('POST', $url, [
            'json' => [
                'subject' => $outputs['subject'],
                'problem' => $outputs['problem'],
                'sol' => $outputs['sol'],
            ]
        ]);

        $result = $res->getBody();
        return $result;
    }

    public function sendNewOutput2($problem)
    {
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($problem);

        $outputs = [];
        $outputs['subject'] = $subjectName;
        $outputs['problem'] = $problem->name;
        $outputs['sol'] = [];

        foreach ($problem->problemFiles as $problemFile){
            $temps = explode('.', $problemFile->filename);
            $package = $temps[0];
            foreach ($problemFile->outputs as $output){
                $realOutput = [
                    'version' => $output->version,
                    'filename' => $output->filename,
                    'content' => $output->content,
                    'package' => $package
                ];
                array_push($outputs['sol'], $realOutput);
            }
        }

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/send_sol_driver';
        //$url = 'http://www.posttestserver.com/post.php';

        $res = $client->request('POST', $url, [
            'json' => [
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
        $evaluator_ip = $this->evaluator_IP;
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

    public function evaluateFile2($submission)
    {
        $problem = $submission->problem;
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($problem);

        $data = [];
        $data['file'] = [];
        foreach ($submission->submissionFiles as $submissionFile){
            $package = $submissionFile->package;
            if($package == 'default package'){
                $package = '';
            }

            $temps = explode('.', $submissionFile->filename);
            $fileName = $temps[0];

            $dataFile = [
                'package' => $package,
                'filename' => $fileName,
                'code' => $submissionFile->code,
            ];
            array_push($data['file'], $dataFile);
        }

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/evaluate_driver';
        //$url = 'http://www.posttestserver.com/post.php';

        $res = $client->request('POST', $url, [
            'json' => [
                'time_out' => strval($problem->timelimit),
                'mem_size' => strval($problem->memorylimit),
                'subject' => $subjectName,
                'problem' => $problem->name,
                'file' => $data['file'],
            ]
        ]);

        $result = $res->getBody();
        $json = json_decode($result, true);
        return $json;
    }

    public function sendDriver($problem)
    {
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($problem);

        $drivers = [];
        $drivers['subject'] = $subjectName;
        $drivers['problem'] = $problem->name;
        $drivers['driver'] = [];

        foreach ($problem->problemFiles as $problemFile){
            $temps = explode('.', $problemFile->filename);
            $filename = $temps[0];
            if($problemFile->package == 'driver'){
                $numInSol = ProblemInput::where('problem_file_id', '=', $problemFile->id)->count();
                $driver = [
                    'package' => $problemFile->package,
                    'filename' => $filename,
                    'code' => $problemFile->code,
                    'number' => $numInSol
                ];
                array_push($drivers['driver'], $driver);
            }
        }

        $client = new Client();
        $url = $evaluator_ip.'/api/teacher/send_driver';
        //$url = 'http://www.posttestserver.com/post.php';

        $res = $client->request('POST', $url, [
            'json' => [
                'subject' => $drivers['subject'],
                'problem' => $drivers['problem'],
                'driver' => $drivers['driver'],
            ]
        ]);

        $result = $res->getBody();
        $json = json_decode($result, true);
        return $json;
    }

    public function analyzeSubmitFile($submissionFile)
    {
        $evaluator_ip = $this->evaluator_IP;
        $codes = [];
        array_push($codes, $submissionFile->code);

        $client = new Client();
        $res = $client->request('POST', $evaluator_ip.'/api/student/code', [
            'json' => [
                'code' => $codes,
            ]
        ]);

        $result = $res->getBody();
        $json = json_decode($result, true);
        //Log::info('#### Data From Evaluator : '. $res->getBody());

        return $json;
    }

    public function analyzeSubmitFile2($submission)
    {
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($submission->problem);
        $data = [];
        foreach ($submission->submissionFiles as $submissionFile){
            /*$code = $submissionFile->code;
            $is_main = false;
            $main = strpos($code, 'main');
            if($main != false){
                $args = strpos($code, '(', $main);
                $args1 = strpos($code, 'String', $args);
                $args2 = strpos($code, '[]', $args1);

                if($args != false && $args1 != false && $args2 != false){
                    $is_main = true;
                }
            }*/

            $package = $submissionFile->package;
            if($package == 'default package'){
                $package = '';
            }

            $temps = explode('.', $submissionFile->filename);
            $fileName = $temps[0];

            $dataFile = [
                'package' => $package,
                'filename' => $fileName,
                'code' => $submissionFile->code,
            ];
            array_push($data, $dataFile);
        }

        $client = new Client();
        $res = $client->request('POST', $evaluator_ip.'/api/student/analysis', [
            'json' => [
                'subject' => $subjectName,
                'problem' => $submission->problem->name,
                'files' => $data,
            ]
        ]);

        $result = $res->getBody();
        $json = json_decode($result, true);
        Log::info('#### Data From Evaluator : '. $res->getBody());

        return $json;
    }

    public function analyzeProblemFile($problem)
    {
        $evaluator_ip = $this->evaluator_IP;
        $subjectName = self::getSubjectName($problem);
        $data = [];

        foreach ($problem->problemFiles as $problemFile){
            $package = $problemFile->package;
            if($package != 'driver'){
                if($package == 'default package'){
                    $package = '';
                }

                $temps = explode('.', $problemFile->filename);
                $fileName = $temps[0];

                $dataFile = [
                    'package' => $package,
                    'filename' => $fileName,
                    'code' => $problemFile->code,
                ];
                array_push($data, $dataFile);
            }
        }

        $client = new Client();
        $res = $client->request('POST', $evaluator_ip.'/api/teacher/analysis', [
            'json' => [
                'subject' => $subjectName,
                'problem' => $problem->name,
                'files' => $data,
            ]
        ]);

        $result = $res->getBody();
        $json = json_decode($result, true);
        Log::info('#### Data From Evaluator : '. $res->getBody());

        return $json;
    }
}