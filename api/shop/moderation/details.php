<?php

require '../../utils/request.php';

$req->requireEmployeePrivileges();

$shop = new \stdClass();

$params = $req->getParams([
    "id" => [],
]);

$shop->id = $params["id"];
$shop->name = "Tienda " . $params["id"];
$shop->zone = 1;
$shop->municipality = "Guatemala";
$shop->department = "Guatemala";
$shop->phoneNumber =  ($params["id"] % 9 + 1) . "123456" . ($params["id"] % 9 + 1);
$shop->description = "Shop description " . $params["id"];
$shop->photos = [];

$req->success($shop);
