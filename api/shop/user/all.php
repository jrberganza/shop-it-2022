<?php

require '../../private/strict.php';
require '../../private/db.php';

header('Content-type: application/json');

// TODO: connect to database
// TODO: do it only for the current user

$allShops = array();

for ($currId = 0; $currId < 10; $currId++) {
    $shop = new \stdClass();

    $shop->id = $currId;
    $shop->name = "Tienda " . $currId;
    $shop->address = $currId . " Calle, Guatemala";
    $shop->phoneNumber =  ($currId % 9 + 1) . "123456" . ($currId % 9 + 1);
    $shop->shortDesc = "Shop description " . $currId;
    array_push($allShops, $shop);
}

$resJson = json_encode($allShops);

echo $resJson;
