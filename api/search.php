<?php

require 'utils/request.php';

$searchQuery = $_GET["q"];

$searchResults = array();

$productResults = new \stdClass();
$productResults->name = 'Products';
$productResults->type = 'product';
$productResults->content = array();

for ($currId = 0; $currId < 9; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $searchQuery . " " . $currId;
    $product->shopName = "Tienda " . random_int(1, 100);
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    $product->rating = random_int(0, 10) / 2.0;
    $product->photos = [];
    array_push($productResults->content, $product);
}

array_push($searchResults, $productResults);

$shopResults = new \stdClass();
$shopResults->name = 'Shops';
$shopResults->type = 'shop';
$shopResults->content = array();

for ($currId = 0; $currId < 9; $currId++) {
    $shop = new \stdClass();

    $shop->id = $currId;
    $shop->name = "Tienda " . $searchQuery . " " . $currId;
    $shop->address = $currId . " Calle, Guatemala";
    $shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
    $shop->shortDesc = "Shop description " . $currId;
    $shop->photos = [];
    array_push($shopResults->content, $shop);
}

array_push($searchResults, $shopResults);

$resObj = new \stdClass();
$resObj->results = $searchResults;

$req->success($resObj);
