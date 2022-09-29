<?php

require '../../utils/request.php';

$allShops = array();

for ($currId = 0; $currId < 10; $currId++) {
    $shop = new \stdClass();

    $shop->id = $currId;
    $shop->name = "Tienda " . $currId;
    $shop->address = $currId . " Calle, Guatemala";
    $shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
    $shop->shortDesc = "Shop description " . $currId;
    $shop->photos = [];
    array_push($allShops, $shop);
}

$resObj = new \stdClass();
$resObj->pending = $allShops;

$req->success($resObj);
