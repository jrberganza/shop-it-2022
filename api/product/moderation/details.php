<?php

require '../../utils/request.php';

$product = new \stdClass();

$currId = $_GET["id"];

$product->id = $currId;
$product->name = "Producto " . $currId;
$product->shopName = "Tienda " . random_int(0, 100);
$product->price =  random_int(0, 9999) / 100.0;
$product->description = "Product description " . $currId;
$product->photos = [];

$req->success($product);
