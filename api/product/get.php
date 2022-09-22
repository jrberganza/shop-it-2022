<?php

require '../private/strict.php';
require '../private/db.php';

header('Content-type: application/json');

// TODO: connect to database

$currId = $_GET['id'];

$product = new \stdClass();

$product->id = $currId;
$product->name = "Producto " . $currId;
$product->shopName = "Tienda " . random_int(1, 100);
$product->price =  random_int(0, 9999) / 100.0;
$product->desc = "Product description " . $currId;

$resJson = json_encode($product);

echo $resJson;
