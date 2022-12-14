<?php

require_once '../../utils/request.php';

$stmt = $req->prepareQuery("SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.municipality_id as municipality,
    dp.department_id as department,
    s.latitude as latitude,
    s.longitude as longitude,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    false as moderated
FROM
    \$moderation\$shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
WHERE
    s.shop_id = @{i:shopId}
UNION
SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.municipality_id as municipality,
    dp.department_id as department,
    s.latitude as latitude,
    s.longitude as longitude,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    true as moderated
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
WHERE
    s.shop_id = @{i:shopId}", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No shop found");
}

$resObj->disabled = $resObj->disabled != 0;
$shopId = $resObj->id;

$stmt = $req->prepareQuery("SELECT p.photo_id FROM \$moderation\$shop_photo sp JOIN photos p USING (photo_id) WHERE sp.shop_id = @{i:shopId}
UNION
SELECT p.photo_id FROM shop_photo sp JOIN photos p USING (photo_id) WHERE sp.shop_id = @{i:shopId}", [
    "shopId" => $shopId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["photo_id"]);
}

$stmt = $req->prepareQuery("SELECT c.category_id FROM \$moderation\$shop_category sc JOIN categories c USING (category_id) WHERE sc.shop_id = @{i:shopId}
UNION
SELECT c.category_id FROM shop_category sc JOIN categories c USING (category_id) WHERE sc.shop_id = @{i:shopId}", [
    "shopId" => $shopId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->categories = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->categories, $row["category_id"]);
}

$req->success($resObj);
