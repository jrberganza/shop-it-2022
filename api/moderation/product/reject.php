<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer"
    ],
]);

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
