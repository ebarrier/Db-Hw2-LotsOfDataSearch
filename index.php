<?php

require 'setup.php';

define('PRODUCT_COUNT', 10000000);


header('Content-type: text/html; charset=utf-8');

try {
	$stmt = $db->prepare('SELECT COUNT(*) FROM product;');
	$stmt->execute();
	$productCount = $stmt->fetchColumn();
	if ($productCount < PRODUCT_COUNT) {
		if (0 == $productCount) {
			$db->exec('CREATE INDEX product_date ON product(production_date DESC);');
		}
		$newProducts = PRODUCT_COUNT - $productCount;
		echo '<p>Generating <strong>' . $newProducts . '</strong> new products. This takes &quot;some&quot; time... (will print a dot for every thousand)</p>';
		ob_flush(); flush();

		$productNames = array('Rubber duck', 'Washing machine', 'Oven', 'Light bulb', 'Regular car', 'Tractor',
			'Book', 'Bike', 'Motorbike', 'Fridge', 'Computer', 'Laptop', 'Door', 'Bag', 'Box');
		$db->beginTransaction();
		echo '<p>';
		for ($i = 0; $i < $newProducts; $i++) {
			$stmt = $db->prepare('INSERT INTO product(serial_number, name, production_date) VALUES(:serial_number, :name, :production_date);');
			$serial = sha1(time() . rand());
			$name =  $productNames[array_rand($productNames)] . ' ' . rand(1, 10000);
			$date = date("Y-m-d", rand(0, time()));
			$stmt->bindParam(':serial_number', $serial);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':production_date', $date);
			$stmt->execute();
			if (0 == $i % 1000) {
				echo '. ';
				ob_flush(); flush();
				$db->commit();
				$db->beginTransaction();
			}
		}
		$db->commit();
		echo '<p>Done.</p>';
	}
}
catch (Exception $e) {
	die($e->getMessage());
}

?>
<html>
<head>
	<title>The search box</title>

</head>
</html>
