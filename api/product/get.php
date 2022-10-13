<?php

require '../utils/request.php';

$req->useDb();
$req->useSession();

if (!isset($_GET["id"])) {
    $req->fail("No product specified");
}
$productId = $_GET['id'];

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
    "productId" => $productId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No product found");
}

$stmt = $req->prepareQuery("SELECT product_photo_id FROM product_photos WHERE product_id = @{i:productId}", [
    "productId" => $productId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["product_photo_id"]);
}

$req->success($resObj);

$req->success($product);
