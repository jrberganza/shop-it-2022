<?php

require '../private/strict.php';
require '../private/db.php';

header('Content-type: application/json');

// TODO: connect to database
// TODO: do it only for the current user

$currId = $_GET['id'];

$shop = new \stdClass();

$shop->id = $currId;
$shop->name = "Tienda " . $currId;
$shop->address = $currId . " Calle, Guatemala";
$shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
$shop->desc = "Shop description " . $currId;

$resJson = json_encode($shop);

echo $resJson;
