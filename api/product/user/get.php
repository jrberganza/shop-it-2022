<?php

require_once '../../utils/request.php';

$req->requireLoggedIn();

$params = $req->getParams([
    "id" => [],
]);

$stmt = $req->prepareQuery("SELECT
    product_id as id,
    name as name,
    price as price,
    description as description,
    disabled as disabled,
    false as moderated
FROM \$moderation\$products
WHERE
    product_id = @{i:productId} AND
    shop_id = @{i:shopId}
UNION
SELECT
    product_id as id,
    name as name,
    price as price,
    description as description,
    disabled as disabled,
    true as moderated
FROM products
WHERE
    product_id = @{i:productId} AND
    shop_id = @{i:shopId}", [
    "productId" => $params["id"],
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if (!$resObj) {
    $req->fail("No product found");
}

$resObj->disabled = $resObj->disabled != 0;

$stmt = $req->prepareQuery("SELECT p.photo_id FROM \$moderation\$product_photo pp JOIN photos p USING (photo_id) WHERE pp.product_id = @{i:productId}
UNION
SELECT p.photo_id FROM product_photo pp JOIN photos p USING (photo_id) WHERE pp.product_id = @{i:productId}", [
    "productId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["photo_id"]);
}

$stmt = $req->prepareQuery("SELECT c.category_id FROM \$moderation\$product_category pc JOIN categories c USING (category_id) WHERE pc.product_id = @{i:productId}
UNION
SELECT c.category_id FROM product_category pc JOIN categories c USING (category_id) WHERE pc.product_id = @{i:productId}", [
    "productId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj->categories = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->categories, $row["category_id"]);
}

$req->success($resObj);
