<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer"
    ],
]);

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
