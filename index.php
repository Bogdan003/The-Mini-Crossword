<?php

$filename = '2022-08-22.json';
$jsonContent = file_get_contents('input/' . $filename);
$jsonData = json_decode($jsonContent, true);

require_once 'crosswordsClass.php';
$crossWordsObj = new Crosswords($jsonData);
$tableDimensions = $crossWordsObj->setTable();
$resolve = $crossWordsObj->fillCrosswords();

echo "<pre>";
var_dump($resolve);
