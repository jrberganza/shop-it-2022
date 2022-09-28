<?php

require '../../utils/strict.php';
require '../../utils/private/db.php';

header('Content-type: application/json');

// TODO: connect to database
// TODO: do it only for the current user

$shop = new \stdClass();

$currId = $_GET["id"];

$shop->id = $currId;
$shop->name = "Tienda " . $currId;
$shop->address = $currId . " Calle, Guatemala";
$shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
$shop->description = "Shop description " . $currId;
$shop->photos = [];

$resJson = json_encode($shop);

echo $resJson;
