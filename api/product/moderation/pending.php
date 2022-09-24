<?php

require '../../private/strict.php';
require '../../private/db.php';

header('Content-type: application/json');

// TODO: connect to database
// TODO: do it only for the current shop

$allProducts = array();

for ($currId = 0; $currId < 10; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . random_int(0, 100);
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    array_push($allProducts, $product);
}

$resJson = json_encode($allProducts);

echo $resJson;
