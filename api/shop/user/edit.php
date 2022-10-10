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

$stmt = $req->prepareQuery("UPDATE
    shops
SET
    name = @{s:name},
    address = @{s:address},
    latitude = @{d:latitude},
    longitude = @{d:longitude},
    phone_number = @{s:phoneNumber},
    description = @{s:description},
    disabled = @{i:disabled}
WHERE
    user_id = @{i:userId}", [
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

$req->success($resObj);
