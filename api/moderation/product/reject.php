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
    item_type,
    item_name,
    item_description,
    item_created_at,
    item_updated_at,
    reason,
    published
) VALUES (
    @{i:userId},
    'product',
    @{s:itemName},
    @{s:itemDescription},
    @{s:itemCreatedAt},
    @{s:itemUpdatedAt},
    @{s:reason},
    FALSE
)", [
    "userId" => $product->user_id,
    "itemName" => $product->name,
    "itemDescription" => $product->description,
    "itemCreatedAt" => $product->created_at,
    "itemUpdatedAt" => $product->updated_at,
    "reason" => $jsonBody->reason,
]);
$stmt->execute();

$resObj = new \stdClass();
$req->success($resObj);
