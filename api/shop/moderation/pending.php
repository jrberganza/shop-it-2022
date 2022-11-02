<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$allShops = array();

for ($currId = 0; $currId < 10; $currId++) {
    $shop = new \stdClass();

    $shop->id = $currId;
    $shop->name = "Tienda " . $currId;
    $shop->zone = 1;
    $shop->municipality = "Guatemala";
    $shop->department = "Guatemala";
    $shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
    $shop->description = "Shop description " . $currId;
    $shop->photos = [];
    array_push($allShops, $shop);
}

$resObj = new \stdClass();
$resObj->pending = $allShops;

$req->success($resObj);
