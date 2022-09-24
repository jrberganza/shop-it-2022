<?php

require './private/strict.php';
require './private/db.php';

header('Content-type: application/json');

// TODO: connect to database
// TODO: do it only for the current shop

$feeds = array();

$productFeed = new \stdClass();
$productFeed->name = 'Recent';
$productFeed->type = 'product';
$productFeed->content = array();
for ($currId = 0; $currId < 5; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . random_int(1, 100);
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    array_push($productFeed->content, $product);
}
array_push($feeds, $productFeed);

$shopFeed = new \stdClass();
$shopFeed->name = 'Top rated shops';
$shopFeed->type = 'shop';
$shopFeed->content = array();
for ($currId = 0; $currId < 5; $currId++) {
    $shop = new \stdClass();

    $shop->id = $currId;
    $shop->name = "Tienda " . $currId;
    $shop->address = $currId . " Calle, Guatemala";
    $shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
    $shop->shortDesc = "Shop description " . $currId;
    $shop->photos = [1];
    array_push($shopFeed->content, $shop);
}
array_push($feeds, $shopFeed);

$trendingFeed = new \stdClass();
$trendingFeed->name = 'Trending products';
$trendingFeed->type = 'product';
$trendingFeed->content = array();
for ($currId = 0; $currId < 5; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . random_int(1, 100);
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    array_push($trendingFeed->content, $product);
}
array_push($feeds, $trendingFeed);

$resJson = json_encode($feeds);

echo $resJson;
