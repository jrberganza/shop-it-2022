<?php

require_once '../utils/request.php';

$params = $req->getParams([
    "id" => [],
]);

$stmt = $req->prepareQuery("SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled,
    cast(coalesce(r.rating, 0.0) as double) as rating,
    s.shop_id as shopId,
    s.name as shopName
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as rating, product_id FROM product_ratings WHERE product_id = @{i:productId}) r USING (product_id)
WHERE
    p.disabled = FALSE AND
    s.disabled = FALSE AND
    p.product_id = @{i:productId}", [
    "productId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No product found");
}

$stmt = $req->prepareQuery("SELECT p.photo_id FROM product_photo pp JOIN photos p USING (photo_id) WHERE pp.product_id = @{i:productId}", [
    "productId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["photo_id"]);
}

$stmt = $req->prepareQuery("SELECT c.category_id as id, c.name as name FROM product_category pc JOIN categories c USING (category_id) WHERE pc.product_id = @{i:productId}", [
    "productId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->categories = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->categories, $row);
}

$req->success($resObj);
