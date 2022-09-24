<?php

require '../../private/strict.php';
require '../../private/db.php';
require "../../private/utils.php";

header('Content-type: application/json');

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

$stmt = $db->prepare("SELECT shop_id as id, name, address, phone_number as phoneNumber, substr(description, 1, 100) as shortDesc FROM shops WHERE user_id = ? ORDER BY created_at");
$stmt->bind_param("s", $session->id);
$stmt->execute();
$result = $stmt->get_result();

$resObj = new \stdClass();
$resObj->shops = array();

while ($row = $result->fetch_object()) {
    array_push($resObj->shops, $row);
}

resSuccess($resObj);
