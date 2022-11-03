<?php

require_once '../utils/request.php';

$params = $req->getParams([
    "id" => [],
]);

$stmt = $req->prepareQuery("SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.name as municipality,
    dp.name as department,
    s.latitude as latitude,
    s.longitude as longitude,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    cast(coalesce(r.rating, 0.0) as double) as rating
FROM
    shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)
LEFT JOIN
    (SELECT avg(rating) as rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
WHERE
    disabled = FALSE AND
    shop_id = @{i:shopId}", [
    "shopId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No shop found");
}

$stmt = $req->prepareQuery("SELECT p.photo_id FROM shop_photo sp JOIN photos p USING (photo_id) WHERE sp.shop_id = @{i:shopId}", [
    "shopId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["photo_id"]);
}

$stmt = $req->prepareQuery("SELECT
    product_id as id,
    name as name,
    price as price,
    substr(description, 1, 100) as description
FROM
    products
WHERE
    disabled = FALSE AND
    shop_id = @{i:shopId}
ORDER BY rand() LIMIT 5", [
    "shopId" => $params["id"]
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->products = array();
while ($row = $result->fetch_object()) {
    array_push($resObj->products, $row);
}

$stmt = $req->prepareQuery("SELECT c.category_id as id, c.name as name FROM shop_category sc JOIN categories c USING (category_id) WHERE sc.shop_id = @{i:shopId}", [
    "shopId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->categories = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->categories, $row);
}

$req->success($resObj);
