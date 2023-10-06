<?php

$filename = '2023-10-04.json';
$jsonContent = file_get_contents('input/' . $filename);
$jsonData = json_decode($jsonContent, true);

require_once 'crosswordsClass.php';
$crossWordsObj = new Crosswords($jsonData);
$tableDimensions = $crossWordsObj->getTable();
$additionalCellsData = $crossWordsObj->getAdditionalCellsData();

echo "<pre>";
var_dump($crossWordsObj->getCrosswords());

require_once 'view.php';
