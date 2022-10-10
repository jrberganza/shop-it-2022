<?php

require '../../utils/request.php';

$currId = $_GET['id'];

$product = new \stdClass();

$product->id = $currId;
$product->name = "Producto " . $currId;
$product->shopName = "Tu Tienda";
$product->price =  random_int(0, 9999) / 100.0;
$product->desc = "Product description " . $currId;
$product->photos = [];

$req->success($product);
