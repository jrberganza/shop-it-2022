<?php

require '../utils/request.php';

$req->useDb();
$req->useSession();

if (!isset($_GET["id"])) {
    $req->fail("No shop specified");
}
$shopId = $_GET["id"];

$stmt = $req->prepareQuery("SELECT
    s.shop_id as id,
    s.name as name,
    s.address as address,
    s.latitude as latitude,
    s.longitude as longitude,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    cast(coalesce(r.rating, 0.0) as double) as rating
FROM
    shops s
LEFT JOIN
    (SELECT avg(rating) as rating, shop_id FROM shop_ratings WHERE shop_id = @{i:shopId}) r USING (shop_id)
WHERE
    disabled = FALSE AND
    shop_id = @{i:shopId}", [
    "shopId" => $shopId,
    "userId" => $req->session->id,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No shop found");
}

$stmt = $req->prepareQuery("SELECT photo_id FROM shop_photo JOIN photos USING (photo_id) WHERE shop_id = @{i:shopId}", [
    "shopId" => $row->id,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["shop_photo_id"]);
}

$stmt = $req->prepareQuery("SELECT
    product_id as id,
    name as name,
    price as price,
    substr(description, 1, 100) as shortDesc
FROM
    products
WHERE
    disabled = FALSE AND
    shop_id = @{i:shopId}
ORDER BY rand() LIMIT 5", [
    "shopId" => $shopId
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->products = array();
while ($row = $result->fetch_object()) {
    array_push($resObj->products, $row);
}

$req->success($resObj);
