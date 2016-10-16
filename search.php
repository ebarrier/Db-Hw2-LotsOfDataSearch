<?php

require 'setup.php';

if (!isset($_GET['q'])) {
	die('Please set the search parameter (GET parameter "q").');
}

header('Content-type: text/json; charset=utf-8');

$searchParam = $_GET['q'];
$moment = microtime(true);
$stmt = $db->prepare('SELECT * FROM product WHERE MATCH(name,serial_number) AGAINST (? IN NATURAL LANGUAGE MODE) ORDER BY production_date DESC LIMIT 10');
$stmt->execute(array($searchParam));
if (!$stmt->execute()) die("Execute failed: (" . $stmt->errorCode() . ") " . $stmt->errorInfo());
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$queryTime = microtime(true) - $moment;

$output = array(
	'query_time' => $queryTime,
	'products' => $products
);

die(json_encode($output));
