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

$stmt = $req->prepareQuery("SELECT * FROM \$moderation\$shops s WHERE s.shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_object();

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$shop_category WHERE shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$shop_photo WHERE shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$shops WHERE shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
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
    "userId" => $shop->user_id,
    "itemName" => $shop->name,
    "itemDescription" => $shop->description,
    "itemCreatedAt" => $shop->created_at,
    "itemUpdatedAt" => $shop->updated_at,
    "reason" => $jsonBody->reason,
]);
$stmt->execute();

$resObj = new \stdClass();
$req->success($resObj);
