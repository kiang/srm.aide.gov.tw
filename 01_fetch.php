<?php
require_once 'vendor/autoload.php';
use Goutte\Client;

$rawPath = __DIR__ . '/raw/A10';
if(!file_exists($rawPath)) {
    mkdir($rawPath, 0777, true);
}
$csvPath = __DIR__ . '/csv/A10';
if(!file_exists($csvPath)) {
    mkdir($csvPath, 0777, true);
}
$client = false;
$options1 = array('特教班', '資源班', '資源教室', '數理班', '語文班', '舞蹈班', '音樂班', '美術班');
$options2 = array('特教班', '資源班', '數理班', '語文班', '舞蹈班', '音樂班', '美術班');
$options3 = array('特教', '一般', '代理', '小計');
$header1 = $header2 = array('縣市');
foreach($options1 AS $option1) {
    foreach($options3 AS $option2) {
        $header1[] = $option1 . $option2;
    }
}
foreach($options2 AS $option1) {
    foreach($options3 AS $option2) {
        $header2[] = $option1 . $option2;
    }
}
$header1[] = '總計';
$header1[] = '';
$header2[] = '總計';
$header2[] = '';

for($i = 100; $i <= 107; $i++) {
    $rawHtml = $rawPath . '/' . $i . '.html';
    if(!file_exists($rawHtml)) {
        if(false === $client) {
            $client = new Client();
            $crawler = $client->request('GET', 'http://srm.aide.gov.tw/AideRegister/Anonymous/ProfileX.aspx?sid=11');
            $form = $crawler->selectButton('下載HTML')->form();
        }
        $crawler = $client->submit($form, array('ctl00$ContentPlaceHolder1$learnYearDD' => $i));
        file_put_contents($rawHtml, $crawler->html());
    }
    $html = file_get_contents($rawHtml);
    $targetCsv = $csvPath . '/' . $i . '.csv';
    $fh = fopen($targetCsv, 'w');
    if($i > 103) {
        fputcsv($fh, $header2);
    } else {
        fputcsv($fh, $header1);
    }
    $lines = explode('</tr>', $html);
    foreach($lines AS $line) {
        $cols = explode('</td>', $line);
        if($i > 103) {
            if(count($cols) === 31) {
                foreach($cols AS $k => $v) {
                    $cols[$k] = trim(strip_tags($v));
                }
                fputcsv($fh, $cols);
            }
        } else {
            if(count($cols) === 35) {
                foreach($cols AS $k => $v) {
                    $cols[$k] = trim(strip_tags($v));
                }
                fputcsv($fh, $cols);
            }
        }
    }
}
