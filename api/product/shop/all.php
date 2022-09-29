<?php

require '../../utils/request.php';

$allProducts = array();

$shopId = $_GET['shopId'];

for ($currId = 0; $currId < 10; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . $shopId;
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    $product->rating = random_int(0, 10) / 2.0;
    $product->photos = [];
    array_push($allProducts, $product);
}

$resObj = new \stdClass();
$resObj->products = $allProducts;

$req->success($resObj);
