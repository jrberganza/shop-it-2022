<?php

require "../../utils/request.php";

$req->useDb();
$req->useSession();

$req->requireLoggedIn();

$stmt = $req->prepareQuery("SELECT shop_id as id, name, address, phone_number as phoneNumber, substr(description, 1, 100) as shortDesc FROM shops WHERE user_id = @{i:userId} ORDER BY created_at", [
    "userId" => $req->session->id
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = new \stdClass();
$resObj->shops = array();

while ($row = $result->fetch_object()) {
    $stmt2 = $req->prepareQuery("SELECT shop_photo_id FROM shop_photos WHERE shop_id = @{i:shopId}", [
        "shopId" => $row->id,
    ]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $row->photos = array();
    while ($row2 = $result2->fetch_array()) {
        array_push($row->photos, $row2["shop_photo_id"]);
    }

    array_push($resObj->shops, $row);
}

$req->success($resObj);
