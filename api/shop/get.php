<?php

require '../private/strict.php';
require '../private/db.php';
require "../private/utils.php";

header('Content-type: application/json');

$session = getCurrentSession($db);

if (!isset($_GET["id"])) {
    resFail("No shop specified");
}
$shopId = $_GET["id"];

if (!$session) {
    resFail("Not logged in");
}

$stmt = $db->prepare("SELECT shop_id as id, name, address, phone_number as phoneNumber, description, disabled FROM shops WHERE user_id = ? AND shop_id = ?");
$stmt->bind_param("ii", $session->id, $shopId);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

resSuccess($resObj);
