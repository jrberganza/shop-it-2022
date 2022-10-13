<?php

require '../../utils/request.php';

$req->useDb();
$req->useSession();

if (!isset($_GET["id"])) {
    $req->fail("No product specified");
}
$productId = $_GET['id'];

$stmt = $req->prepareQuery("SELECT
    product_id as id,
    name as name,
    price as price,
    description as description,
    disabled as disabled
FROM products
WHERE
    product_id = @{i:productId} AND
    shop_id = @{i:shopId}", [
    "productId" => $productId,
    "shopId" => $req->session->shopId,
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
