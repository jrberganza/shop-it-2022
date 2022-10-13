<?php

require "../../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->useDb();
$req->useSession();

$req->requireLoggedIn();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer",
        "optional" => true,
    ],
    "name" => [
        "type" => "string",
        "maxLength" => 255,
    ],
    "price" => [
        "type" => "double",
    ],
    "description" => [
        "type" => "string",
        "maxLength" => 512,
    ],
    "disabled" => [
        "type" => "boolean",
    ],
]);

if (!$req->session->shopId) {
    $req->fail("No shop has been created for this user");
}

if (isset($jsonBody->id)) {
    $stmt = $req->prepareQuery("UPDATE
        products
    SET
        name = @{s:name},
        price = @{s:price},
        description = @{s:description},
        disabled = @{i:disabled}
    WHERE
        product_id = @{i:productId} AND
        shop_id = @{i:shopId}", [
        "name" => $jsonBody->name,
        "price" => $jsonBody->price,
        "description" => $jsonBody->description,
        "disabled" => $jsonBody->disabled,
        "productId" => $jsonBody->id,
        "shopId" => $req->session->shopId,
    ]);
    $stmt->execute();
    $productId = $stmt->insert_id;

    $resObj = new \stdClass();
    $resObj->id = $productId;

    $req->success($resObj);
} else {
    $stmt = $req->prepareQuery("INSERT INTO products(
        name,
        price,
        description,
        disabled,
        shop_id
    ) VALUES (
        @{s:name},
        @{s:price},
        @{s:description},
        @{i:disabled},
        @{i:shopId}
    )", [
        "name" => $jsonBody->name,
        "price" => $jsonBody->price,
        "description" => $jsonBody->description,
        "disabled" => $jsonBody->disabled,
        "shopId" => $req->session->shopId,
    ]);
    $stmt->execute();
    $productId = $stmt->insert_id;

    $resObj = new \stdClass();
    $resObj->id = $productId;

    $req->success($resObj);
}
