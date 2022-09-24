<?php
require '../../private/strict.php';
require '../../private/db.php';
require "../../private/utils.php";

header('Content-type: text/xml');

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

$xmlBody = simplexml_load_file("php://input");

if (!$xmlBody) {
    resFail("Malformed request body");
}

if (strlen($xmlBody->name) <= 0 || strlen($xmlBody->name) > 255) {
    resFail("Invalid name");
}

if (strlen($xmlBody->address) <= 0 || strlen($xmlBody->address) > 255) {
    resFail("Invalid address");
}

if (strlen($xmlBody->phonenumber) <= 0 || strlen($xmlBody->phonenumber) > 20) {
    resFail("Invalid phone number");
}

if (strlen($xmlBody->description) <= 0 || strlen($xmlBody->description) > 512) {
    resFail("Invalid description");
}

$stmt = $db->prepare("INSERT INTO shops(name, address, latitude, longitude, phone_number, description, disabled, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssddssii", $xmlBody->name, $xmlBody->address, $xmlBody->latitude, $xmlBody->longitude, $xmlBody->phonenumber, $xmlBody->description, $xmlBody->disabled, $session->id);
$stmt->execute();
$shopId = $db->insert_id;

$resObj = new \stdClass();
$resObj->id = $shopId;

resSuccess($resObj);
