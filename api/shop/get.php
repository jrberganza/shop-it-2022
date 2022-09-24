<?php

require '../private/strict.php';
require '../private/db.php';
require "../private/utils.php";

header('Content-type: application/json');

if (!isset($_GET["id"])) {
    resFail("No shop specified");
}
$shopId = $_GET["id"];

$stmt = $db->prepare("SELECT shop_id as id, name, address, phone_number as phoneNumber, description, disabled FROM shops WHERE shop_id = ?");
$stmt->bind_param("i", $shopId);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

resSuccess($resObj);
