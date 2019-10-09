<?php
$reportPath = __DIR__ . '/report';
if(!file_exists($reportPath)) {
    mkdir($reportPath, 0777, true);
}
$keyPool = false;
$oFh = fopen($reportPath . '/report1.csv', 'w');
foreach(glob(__DIR__ . '/csv/A10/*.csv') AS $csvFile) {
    $p = pathinfo($csvFile);
    $fh = fopen($csvFile, 'r');
    $header = fgetcsv($fh, 2048);
    if(false === $keyPool) {
        $keyPool = array('年度');
        foreach($header AS $key) {
            $keyPool[] = $key;
        }
        fputcsv($oFh, $keyPool);
    }
    while($line = fgetcsv($fh, 2048)) {
        $data = array_combine($header, $line);
        $data['年度'] = $p['filename'];
        $result = array();
        foreach($keyPool AS $key) {
            if(isset($data[$key])) {
                $result[] = $data[$key];
            } else {
                $result[] = '';
            }
            
        }
        fputcsv($oFh, $result);
    }
}