<?php

require '../utils/request.php';

$currId = $_GET['id'];

$product = new \stdClass();

$product->id = $currId;
$product->name = "Producto " . $currId;
$product->shopName = "Tienda " . random_int(1, 100);
$product->price =  random_int(0, 9999) / 100.0;
$product->desc = "Product description " . $currId;
$product->rating = random_int(0, 10) / 2.0;
$product->photos = [];

$req->success($product);
