<?php

require "../../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->useDb();
$req->useSession();

$req->requireLoggedIn();

$jsonBody = $req->getJsonBody([
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

$stmt = $req->prepareQuery("INSERT INTO shops(name, address, latitude, longitude, phone_number, description, disabled, user_id) VALUES (@{s:name}, @{s:address}, @{d:latitude}, @{d:longitude}, @{s:phoneNumber}, @{s:description}, @{i:disabled}, @{i:userId})", [
    "name" => $jsonBody->name,
    "address" => $jsonBody->address,
    "latitude" => $jsonBody->latitude,
    "longitude" => $jsonBody->longitude,
    "phoneNumber" => $jsonBody->phoneNumber,
    "description" => $jsonBody->description,
    "disabled" => $jsonBody->disabled,
    "userId" => $req->session->id,
]);
$stmt->execute();
$shopId = $stmt->insert_id;

$stmt = $req->prepareQuery("UPDATE users SET shop_id = @{i:shopId} WHERE user_id = @{i:userId}", [
    "shopId" => $shopId,
    "userId" => $req->session->id,
]);
$stmt->execute();

$resObj = new \stdClass();
$resObj->id = $shopId;

$req->success($resObj);
