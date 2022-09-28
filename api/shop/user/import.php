<?php

require "../../utils/request.php";
require "../../utils/validation.php";

$req->useDb();
$req->useSession();

if (!$req->session->isLoggedIn()) {
    $req->fail("Not logged in");
}

$xmlBody = simplexml_load_file("php://input");

if (!$xmlBody) {
    $req->fail("Malformed request body");
}

validateObj($xmlBody, [
    "name" => [
        "type" => "string",
        "maxLength" => 255,
    ],
    "name" => [
        "type" => "string",
        "maxLength" => 255,
    ],
    "address" => [
        "type" => "string",
        "maxLength" => 255,
    ],
    "latitude" => [
        "type" => "double",
    ],
    "longitude" => [
        "type" => "double",
    ],
    "phoneNumber" => [
        "type" => "string",
        "maxLength" => 20,
    ],
    "description" => [
        "type" => "string",
        "maxLength" => 512,
    ],
]);

$xmlBody->latitude = 0;
$xmlBody->longitude = 0;

$stmt = $req->prepareQuery("INSERT INTO shops(name, address, latitude, longitude, phone_number, description, disabled, user_id) VALUES (@{s:name}, @{s:address}, @{d:latitude}, @{d:longitude}, @{s:phoneNumber}, @{s:description}, @{i:disabled}, @{i:userId})", [
    "name" => $xmlBody->name,
    "address" => $xmlBody->address,
    "latitude" => $xmlBody->latitude,
    "longitude" => $xmlBody->longitude,
    "phoneNumber" => $xmlBody->phoneNumber,
    "description" => $xmlBody->description,
    "disabled" => $xmlBody->disabled,
    "userId" => $xmlBody->session->id,
]);
$stmt->execute();
$shopId = $stmt->insert_id;

$resObj = new \stdClass();
$resObj->id = $shopId;

$req->success($resObj);
