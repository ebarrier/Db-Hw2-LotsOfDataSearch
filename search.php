<?php

require 'setup.php';

if (!isset($_GET['q'])) {
	die('Please set the search parameter (GET parameter "q").');
}

header('Content-type: text/json; charset=utf-8');

$searchParam = '%' . $_GET['q'] . '%';
$alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9');
$searchParamCharArray = str_split($_GET['q']);
print_r($searchParamCharArray);

$charincommon = array_unique(array_intersect($searchParamCharArray, $alphabet));
sort($charincommon);
print_r($charincommon);

$array1='.*';
$finalarray=array();
for ($i=0; $i<count($charincommon); $i++) {
   $finalarray[] = $array1;
   $finalarray[] = $charincommon[$i];
}
$finalarray[] = $array1;
var_dump($finalarray);

$alphabetcode = implode($finalarray);
var_dump($alphabetcode);

$moment = microtime(true);
$stmt0 = $db->prepare('SELECT id FROM product WHERE `alphabet` RLIKE :alphabetcode');
$stmt0->bindValue(':alphabetcode', $alphabetcode, PDO::PARAM_STR);
$stmt0->execute();
$alphabetid = $stmt0->fetchAll(PDO::FETCH_COLUMN, 0);

$products=array();
for($i = 0; $i<count($alphabetid); $i++)
{
    $stmt = $db->prepare('SELECT * FROM product WHERE id = :alphaid AND WHERE (serial_number LIKE :searchparam OR name LIKE :searchparam) ORDER BY production_date DESC LIMIT 10');
    $stmt->bindValue(':alphaid', $alphabetid[$i], PDO::PARAM_INT);
    $stmt->bindValue(':searchparam', $searchParam, PDO::PARAM_STR);
    if (!$stmt->execute()) die("Execute failed: (" . $stmt->errorCode() . ") " . $stmt->errorInfo());
    $products[] = $stmt->fetchAll(PDO::FETCH_ASSOC);              
}
$queryTime = microtime(true) - $moment;

$output = array(
	'query_time' => $queryTime,
	'products' => $products
);

die(json_encode($output));
