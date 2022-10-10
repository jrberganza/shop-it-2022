<?php

require '../../utils/request.php';

$req->useDb();
$req->useSession();

$stmt = $req->prepareQuery("SELECT
    s.shop_id as id,
    s.name as name,
    s.address as address,
    s.latitude as latitude,
    s.longitude as longitude,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled
FROM
    shops s
WHERE
    user_id = @{i:userId}", [
    // "shopId" => $shopId,
    "userId" => $req->session->id,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No shop found");
}

$stmt = $req->prepareQuery("SELECT shop_photo_id FROM shop_photos WHERE shop_id = @{i:shopId}", [
    "shopId" => $resObj->id,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["shop_photo_id"]);
}

$req->success($resObj);
