<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer"
    ],
]);

$stmt = $req->prepareQuery("INSERT INTO
    shops
SELECT * FROM \$moderation\$shops s WHERE s.shop_id = @{i:shopId}
ON DUPLICATE KEY UPDATE
    name = s.name,
    zone = s.zone,
    municipality_id = s.municipality_id,
    latitude = s.latitude,
    longitude = s.longitude,
    phone_number = s.phone_number,
    description = s.description,
    disabled = s.disabled", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM shop_category WHERE shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM shop_photo WHERE shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO shop_category SELECT * FROM \$moderation\$shop_category sc WHERE sc.shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO shop_photo SELECT * FROM \$moderation\$shop_photo sp WHERE sp.shop_id = @{i:shopId}", [
    "shopId" => $jsonBody->id,
]);
$stmt->execute();

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

$resObj = new \stdClass();
$req->success($resObj);
