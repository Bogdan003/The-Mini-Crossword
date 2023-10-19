<?php

$filename = '2023-10-19.json';
$jsonContent = file_get_contents('../input/' . $filename);
$jsonData = json_decode($jsonContent, true);

require_once '../controller/crosswordsClass.php';
$crossWordsObj = new Crosswords($jsonData);
$tableDimensions = $crossWordsObj->getTable();
$additionalCellsData = $crossWordsObj->getAdditionalCellsData();

require_once '../views/view.php';
