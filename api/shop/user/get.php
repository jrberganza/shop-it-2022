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
    shop_id = @{i:shopId}", [
    "shopId" => $req->session->shopId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No shop found");
}

$resObj->disabled = $resObj->disabled != 0;

$stmt = $req->prepareQuery("SELECT p.photo_id FROM shop_photo sp JOIN photos p USING (photo_id) WHERE sp.shop_id = @{i:shopId}", [
    "shopId" => $req->session->shopId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["photo_id"]);
}

$req->success($resObj);
