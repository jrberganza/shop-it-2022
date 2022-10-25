<?php

require "../../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

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

if (!$req->getSession()->shopId) {
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
        "shopId" => $req->getSession()->shopId,
    ]);
    $stmt->execute();
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
        "shopId" => $req->getSession()->shopId,
    ]);
    $stmt->execute();
    $jsonBody->id = $stmt->insert_id;
}

$stmt = $req->prepareQuery("DELETE FROM product_category WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

foreach ($jsonBody->categories as $categoryId) {
    $stmt = $req->prepareQuery("INSERT INTO product_category(
        category_id,
        product_id
    ) VALUES (
        @{i:categoryId},
        @{i:productId}
    )", [
        "categoryId" => $categoryId,
        "productId" => $jsonBody->id,
    ]);
    $stmt->execute();
}

foreach ($jsonBody->photos as $photoId) {
    $stmt = $req->prepareQuery("INSERT INTO product_photo(
        photo_id,
        product_id
    ) VALUES (
        @{i:photoId},
        @{i:productId}
    ) ON DUPLICATE KEY UPDATE photo_id = photo_id", [
        "photoId" => $photoId,
        "productId" => $jsonBody->id,
    ]);
    $stmt->execute();
}

$resObj = new \stdClass();
$resObj->id = $jsonBody->id;

$req->success($resObj);
