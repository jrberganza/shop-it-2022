<?php

require '../../utils/request.php';

$shop = new \stdClass();

$currId = $_GET["id"];

$shop->id = $currId;
$shop->name = "Tienda " . $currId;
$shop->address = $currId . " Calle, Guatemala";
$shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
$shop->description = "Shop description " . $currId;
$shop->photos = [];

$req->success($shop);
