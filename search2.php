<?php
require 'setup.php';
if (!isset($_GET['q'])) {
	die('Please set the search parameter (GET parameter "q").');
}
header('Content-type: text/json; charset=utf-8');
$searchParam = '%' . $_GET['q'] . '%';
$products = array();
$remaining = 10;
$datesearchmax = '2017-01-01';
$moment=microtime(true);

$stmt0 = $db->prepare('SELECT COUNT(id) FROM product WHERE (serial_number LIKE :searchParam1 OR name LIKE :searchParam2)');
$stmt0->bindValue(':searchParam1', $searchParam, PDO::PARAM_STR);
$stmt0->bindValue(':searchParam2', $searchParam, PDO::PARAM_STR);
$stmt0->execute();
$result0 = $stmt0->fetchColumn();
var_dump($result0);
$moment2=microtime(true);
$intermediarytime=$moment2 - $moment;
print "Time to count: ". $intermediarytime . PHP_EOL;

if($result0 > 0) {
    for($j=2016; $j > 1969; $j--) {
        for($i=12; $i > 0; $i--) {
            $datesearchmin = $j . "-" . $i . "-00";
            //Make the search between two dates otherwise we always get the same result
            $stmt1 = $db->prepare('SELECT * FROM product WHERE (serial_number LIKE :searchParam1 OR name LIKE :searchParam2) AND (production_date BETWEEN :datemin AND :datemax) LIMIT :limit');
            $stmt1->bindValue(':searchParam1', $searchParam, PDO::PARAM_STR);
            $stmt1->bindValue(':searchParam2', $searchParam, PDO::PARAM_STR);
            $stmt1->bindValue(':datemin', $datesearchmin, PDO::PARAM_STR);
            $stmt1->bindValue(':datemax', $datesearchmax, PDO::PARAM_STR);
            $stmt1->bindValue(':limit', $remaining, PDO::PARAM_INT);
            $stmt1->execute();
            $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
            $countsearch = count($result);
            //print 'month: ' . $i;
            //print ' year: ' . $j;
            //print' number of results: ' . $countsearch . PHP_EOL;
            if ($countsearch == 0) {
                continue;
            }
                $stmt2 = $db->prepare('SELECT * FROM product WHERE (serial_number LIKE :searchParam1 OR name LIKE :searchParam2) AND (production_date BETWEEN :datemin AND :datemax) LIMIT :limit');
                $stmt2->bindValue(':searchParam1', $searchParam, PDO::PARAM_STR);
                $stmt2->bindValue(':searchParam2', $searchParam, PDO::PARAM_STR);
                $stmt2->bindValue(':datemin', $datesearchmin, PDO::PARAM_STR);
                $stmt2->bindValue(':datemax', $datesearchmax, PDO::PARAM_STR);
            if ($countsearch < $remaining) {
                $stmt2->bindValue(':limit', $countsearch, PDO::PARAM_INT);
                $stmt2->execute();
                $products = array_merge($stmt2->fetchAll(PDO::FETCH_ASSOC));
                $remaining = 10 - $countsearch;
            } else {
                $stmt2->bindValue(':limit', $remaining, PDO::PARAM_INT);
                $stmt2->execute();
                $products = array_merge($stmt2->fetchAll(PDO::FETCH_ASSOC));
                $j = 1969;
                break;
            }
        }
    }
}

//var_dump($products);
$queryTime = microtime(true) - $moment;
$output = array(
	'query_time' => $queryTime,
	'products' => $products
);
//$returnval = json_encode($output);
//var_dump($returnval);
die(json_encode($output));
