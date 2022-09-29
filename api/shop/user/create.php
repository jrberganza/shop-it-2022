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
    "phoneNumber" => [
        "type" => "string",
        "maxLength" => 20,
    ],
    "description" => [
        "type" => "string",
        "maxLength" => 512,
    ],
]);

$jsonBody->latitude = 0;
$jsonBody->longitude = 0;

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

$resObj = new \stdClass();
$resObj->id = $shopId;

$req->success($resObj);
