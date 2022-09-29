<?php

require '../../utils/request.php';

$req->useDb();
$req->useSession();

$req->requireEmployeePrivileges();

$allProducts = array();

for ($currId = 0; $currId < 10; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . random_int(0, 100);
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    $product->photos = [];
    array_push($allProducts, $product);
}

$resObj = new \stdClass();
$resObj->pending = $allProducts;

$req->success($resObj);
