<?php

require '../../private/strict.php';
require '../../private/db.php';

header('Content-type: application/json');

$product = new \stdClass();

$currId = $_GET["id"];

$product->id = $currId;
$product->name = "Producto " . $currId;
$product->shopName = "Tienda " . random_int(0, 100);
$product->price =  random_int(0, 9999) / 100.0;
$product->shortDesc = "Product description " . $currId;

$resJson = json_encode($product);

echo $resJson;
