<?php

require '../../utils/request.php';

$req->useDb();
$req->useSession();

$req->requireLoggedIn();

$allProducts = array();

$stmt = $req->prepareQuery("SELECT
    product_id as id,
    name as name,
    price as price,
    substr(description, 1, 100) as shortDesc,
    disabled as disabled
FROM products
WHERE shop_id = @{i:shopId}", [
    "shopId" => $req->session->shopId
]);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_object()) {
    $stmt2 = $req->prepareQuery("SELECT product_photo_id FROM product_photos WHERE product_id = @{i:productId}", [
        "productId" => $row->id,
    ]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $row->photos = array();
    while ($row2 = $result2->fetch_array()) {
        array_push($row->photos, $row2["product_photo_id"]);
    }

    array_push($allProducts, $row);
}

$resObj = new \stdClass();
$resObj->products = $allProducts;

$req->success($resObj);
