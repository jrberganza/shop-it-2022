<?php

require "../../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->requireLoggedIn();

$jsonBody = $req->getJsonBody([
    "name" => [
        "type" => "string",
        "maxLength" => 255,
    ],
    "address" => [
        "type" => "string",
        "maxLength" => 255,
    ],
    "latitude" => [
        "type" => "double",
    ],
    "longitude" => [
        "type" => "double",
    ],
    "phoneNumber" => [
        "type" => "string",
        "maxLength" => 20,
    ],
    "description" => [
        "type" => "string",
        "maxLength" => 512,
    ],
    "disabled" => [
        "type" => "boolean",
    ],
]);

if ($req->getSession()->shopId) {
    $stmt = $req->prepareQuery("UPDATE
        shops
    SET
        name = @{s:name},
        address = @{s:address},
        latitude = @{d:latitude},
        longitude = @{d:longitude},
        phone_number = @{s:phoneNumber},
        description = @{s:description},
        disabled = @{i:disabled}
    WHERE
        shop_id = @{i:shopId}", [
        "name" => $jsonBody->name,
        "address" => $jsonBody->address,
        "latitude" => $jsonBody->latitude,
        "longitude" => $jsonBody->longitude,
        "phoneNumber" => $jsonBody->phoneNumber,
        "description" => $jsonBody->description,
        "disabled" => $jsonBody->disabled,
        "shopId" => $req->getSession()->shopId,
    ]);
    $stmt->execute();
} else {
    $stmt = $req->prepareQuery("INSERT INTO shops(
        name,
        address,
        latitude,
        longitude,
        phone_number,
        description,
        disabled,
        user_id
    ) VALUES (
        @{s:name},
        @{s:address},
        @{d:latitude},
        @{d:longitude},
        @{s:phoneNumber},
        @{s:description},
        @{i:disabled},
        @{i:userId}
    )", [
        "name" => $jsonBody->name,
        "address" => $jsonBody->address,
        "latitude" => $jsonBody->latitude,
        "longitude" => $jsonBody->longitude,
        "phoneNumber" => $jsonBody->phoneNumber,
        "description" => $jsonBody->description,
        "disabled" => $jsonBody->disabled,
        "userId" => $req->getSession()->id,
    ]);
    $stmt->execute();
    $shopId = $stmt->insert_id;

    $stmt = $req->prepareQuery("UPDATE users SET shop_id = @{i:shopId} WHERE user_id = @{i:userId}", [
        "shopId" => $shopId,
        "userId" => $req->getSession()->id,
    ]);
    $stmt->execute();

    $req->getSession()->shopId = $shopId;
}

$stmt = $req->prepareQuery("DELETE FROM shop_category WHERE shop_id = @{i:shopId}", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

foreach ($jsonBody->categories as $categoryId) {
    $stmt = $req->prepareQuery("INSERT INTO shop_category(
        category_id,
        shop_id
    ) VALUES (
        @{i:categoryId},
        @{i:shopId}
    )", [
        "categoryId" => $categoryId,
        "shopId" => $req->getSession()->shopId,
    ]);
    $stmt->execute();
}

foreach ($jsonBody->photos as $photoId) {
    $stmt = $req->prepareQuery("INSERT INTO shop_photo(
        photo_id,
        shop_id
    ) VALUES (
        @{i:photoId},
        @{i:shopId}
    ) ON DUPLICATE KEY UPDATE photo_id = photo_id", [
        "photoId" => $photoId,
        "shopId" => $req->getSession()->shopId,
    ]);
    $stmt->execute();
}

$resObj = new \stdClass();
$resObj->id = $req->getSession()->shopId;

$req->success($resObj);
