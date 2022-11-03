<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer"
    ],
]);

$stmt = $req->prepareQuery("INSERT INTO
    products
SELECT * FROM \$moderation\$products p WHERE p.product_id = @{i:productId}
ON DUPLICATE KEY UPDATE
    name = p.name,
    price = p.price,
    description = p.description,
    disabled = p.disabled", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM product_category WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM product_photo WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO product_category SELECT * FROM \$moderation\$product_category pc WHERE pc.product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO product_photo SELECT * FROM \$moderation\$product_photo pp WHERE pp.product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$product_category WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$product_photo WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$products WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

$resObj = new \stdClass();
$req->success($resObj);
