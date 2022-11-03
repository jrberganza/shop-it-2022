<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer"
    ],
    "reason" => [
        "type" => "string",
        "maxLength" => 255
    ],
]);

$stmt = $req->prepareQuery("SELECT
    s.user_id,
    p.product_id,
    p.name,
    p.description,
    p.created_at,
    p.updated_at
FROM \$moderation\$products p JOIN shops s USING (shop_id) WHERE p.product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_object();

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

$stmt = $req->prepareQuery("INSERT INTO moderation_events (
    user_id,
    item_owner_id,
    item_id,
    item_type,
    item_name,
    item_description,
    item_created_at,
    item_updated_at,
    reason,
    published
) VALUES (
    @{i:userId},
    @{i:itemOwnerId},
    @{i:itemId},
    'product',
    @{s:itemName},
    @{s:itemDescription},
    @{s:itemCreatedAt},
    @{s:itemUpdatedAt},
    @{s:reason},
    TRUE
)", [
    "userId" => $req->getSession()->id,
    "itemOwnerId" => $product->user_id,
    "itemId" => $product->product_id,
    "itemName" => $product->name,
    "itemDescription" => $product->description,
    "itemCreatedAt" => $product->created_at,
    "itemUpdatedAt" => $product->updated_at,
    "reason" => $jsonBody->reason,
]);
$stmt->execute();

$resObj = new \stdClass();
$req->success($resObj);
