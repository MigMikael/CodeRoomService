<?php
/**
 * Created by PhpStorm.
 * User: Mig
 * Date: 8/14/2017
 * Time: 22:47
 */
$myfile = fopen("../routes/web.php", "r") or die("Unable to open file!");
$api_list = [];
$prefix = '';
while(!feof($myfile)) {
    $line = fgets($myfile);
    if(strpos($line, '});') == True){
        $prefix = '';
    }

    if (strpos($line, '::') == True) {
        if(strpos($line, 'prefix') == True){
            $temp = explode('\'', $line);
            $prefix = $temp[7];
        }

        if(strpos($line, 'function()') == false && strpos($line, 'group') == false && strpos($line, 'test') == false && strpos($line, '/R') == false && strpos($line, 'function') == false){
            $line = str_replace(' ','',$line);
            $line = explode('\'', $line);

            $api = [];
            $method = str_replace('Route::', '', $line[0]);
            $method = str_replace('(', '', $method);

            $controller = str_replace('@', ':', $line[3]);

            $api['method'] = $method;
            $api['path'] = $prefix.'/'.$line[1];
            $api['controller'] = $controller;

            if(strpos($api['path'], '/') != 0){
                $api['path'] = '/'.$api['path'];
            }

            array_push($api_list, $api);
        }
        //echo $prefix;
    }
}


$text = '';
$count = 0;
foreach ($api_list as $item){
    $count++;
    $panel = '
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h2>
                    HHH
                </h2>
            </div>
            <div class="panel-body">
                CCC
            </div>
        </div>
    </div>
    <br>
';
    $item['method'] = strtoupper($item['method']);

    if($item['method'] == 'GET'){
        $method_badge = '<span class="label label-success">MMM</span>';
    }elseif ($item['method'] == 'POST'){
        $method_badge = '<span class="label label-warning">MMM</span>';
    }elseif ($item['method'] == 'DELETE'){
        $method_badge = '<span class="label label-danger">MMM</span>';
    }else{
        $method_badge = '<span class="label label-info">MMM</span>';
    }
    $method_badge = str_replace('MMM', $item['method'], $method_badge);

    $count_str = '<span class="label label-default"><b>#'. $count .'</b></span>';
    $header_content = $count_str. '  '. $item['path'] . '';

    $panel = str_replace('HHH', $header_content, $panel);
    $panel = str_replace('CCC', $method_badge . ' ' . $item['controller'], $panel);
    $text .= $panel;
    //print_r($item);
}
//echo $text;
fclose($myfile);


$doc_file = fopen("original.txt", "r") or die("Unable to open file!");
$doc_file_text = '';
while(!feof($doc_file)) {
    $line = fgets($doc_file);
    $doc_file_text .= $line;
}
//echo $doc_file_text;
fclose($doc_file);


$doc_file_text = str_replace('###', $text, $doc_file_text);


$doc_file = fopen("../resources/views/doc.blade.php", "w") or die("Unable to open file!");
fwrite($doc_file, $doc_file_text);
fclose($doc_file);


echo 'success';