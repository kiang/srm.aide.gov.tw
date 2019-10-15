<?php
$reportPath = __DIR__ . '/report/03';
if(!file_exists($reportPath)) {
    mkdir($reportPath, 0777, true);
}
$fh = fopen(__DIR__ . '/report/report1.csv', 'r');
$header = fgetcsv($fh, 2048);
$result = array();
for($i = 2; $i <= 33; $i++) {
    if(!isset($result[$header[$i]])) {
        $result[$header[$i]] = array();
    }
}
$yearLine = array();
while($line = fgetcsv($fh, 2048)) {
    $data = array_combine($header, $line);
    foreach($result AS $k => $v) {
        if(!isset($result[$k][$data['縣市']])) {
            $result[$k][$data['縣市']] = array();
        }
        $result[$k][$data['縣市']][$data['年度']] = $data[$k];
        if(!isset($yearLine[$data['年度']])) {
            $yearLine[$data['年度']] = 0;
        }
    }
}
foreach($result AS $report => $lv1) {
    $fh = fopen($reportPath . '/' . $report . '.csv', 'w');
    $headerDone = false;
    foreach($lv1 AS $city => $lv2) {
        if(false === $headerDone) {
            fputcsv($fh, array_merge(array('縣市'), array_keys($lv2)));
            $headerDone = true;
        }
        $theLine = $yearLine;
        foreach($lv2 AS $year => $val) {
            $theLine[$year] = $val;
        }
        fputcsv($fh, array_merge(array($city), $theLine));
    }
}