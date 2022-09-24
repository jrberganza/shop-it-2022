<?php

require "../../private/strict.php";
require "../../private/db.php";
require "../../private/utils.php";

header("Content-type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    resFail("Wrong HTTP Method");
}

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

$body = file_get_contents("php://input");
$jsonBody = json_decode($body);

if (!$jsonBody) {
    resFail("Malformed request body");
}

if (!isset($jsonBody->id)) {
    resFail("Invalid id");
}

if (strlen($jsonBody->name) <= 0 || strlen($jsonBody->name) > 255) {
    resFail("Invalid name");
}

if (strlen($jsonBody->address) <= 0 || strlen($jsonBody->address) > 255) {
    resFail("Invalid address");
}

$jsonBody->latitude = 0;
$jsonBody->longitude = 0;

if (strlen($jsonBody->phoneNumber) <= 0 || strlen($jsonBody->phoneNumber) > 20) {
    resFail("Invalid phone number");
}

if (strlen($jsonBody->description) <= 0 || strlen($jsonBody->description) > 512) {
    resFail("Invalid description");
}

$stmt = $db->prepare("UPDATE shops SET name = ?, address = ?, latitude = ?, longitude = ?, phone_number = ?, description = ?, disabled = ? WHERE shop_id = ? AND user_id = ?");
$stmt->bind_param("ssddssiii", $jsonBody->name, $jsonBody->address, $jsonBody->latitude, $jsonBody->longitude, $jsonBody->phoneNumber, $jsonBody->description, $jsonBody->disabled, $jsonBody->id, $session->id);
$stmt->execute();
$shopId = $db->insert_id;

$resObj = new \stdClass();

resSuccess($resObj);
