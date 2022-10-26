<?php

require '../../utils/request.php';

$req->requireEmployeePrivileges();

$product = new \stdClass();

$params = $req->getParams([
    "id" => [],
]);

$product->id = $params["id"];
$product->name = "Producto " . $params["id"];
$product->shopName = "Tienda " . random_int(0, 100);
$product->price =  random_int(0, 9999) / 100.0;
$product->description = "Product description " . $currId;
$product->photos = [];

$req->success($product);
