<?php

require '../../utils/strict.php';
require '../../utils/private/db.php';
require "../../utils/utils.php";

header('Content-type: application/json');

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

$stmt = $db->prepare("SELECT shop_id as id, name, address, phone_number as phoneNumber, substr(description, 1, 100) as shortDesc FROM shops WHERE user_id = ? ORDER BY created_at");
$stmt->bind_param("i", $session->id);
$stmt->execute();
$result = $stmt->get_result();

$resObj = new \stdClass();
$resObj->shops = array();

while ($row = $result->fetch_object()) {
    $stmt2 = $db->prepare("SELECT shop_photo_id FROM shop_photos WHERE shop_id = ? ");
    $stmt2->bind_param("i", $row->id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $row->photos = array();
    while ($row2 = $result2->fetch_array()) {
        array_push($row->photos, $row2["shop_photo_id"]);
    }

    array_push($resObj->shops, $row);
}

resSuccess($resObj);
