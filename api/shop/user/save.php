<?php

require_once "../../utils/request.php";

$req->requireMethod("POST");

$req->requireLoggedIn();

$jsonBody = $req->getJsonBody([
    "name" => [
        "type" => "string",
        "maxLength" => 255,
    ],
    "zone" => [
        "type" => "integer",
    ],
    "municipality" => [
        "type" => "integer",
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
    "categories" => [
        "type" => "array"
    ],
    "photos" => [
        "type" => "array"
    ]
]);

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$shops(
    shop_id,
    name,
    zone,
    municipality_id,
    latitude,
    longitude,
    phone_number,
    description,
    disabled,
    user_id
) VALUES (
    @{i:shopId},
    @{s:name},
    @{i:zone},
    @{i:municipalityId},
    @{d:latitude},
    @{d:longitude},
    @{s:phoneNumber},
    @{s:description},
    @{i:disabled},
    @{i:userId}
) ON DUPLICATE KEY UPDATE
    name = @{s:name},
    zone = @{i:zone},
    municipality_id = @{i:municipalityId},
    latitude = @{d:latitude},
    longitude = @{d:longitude},
    phone_number = @{s:phoneNumber},
    description = @{s:description},
    disabled = @{i:disabled}", [
    "shopId" => $req->getSession()->shopId,
    "name" => $jsonBody->name,
    "zone" => $jsonBody->zone,
    "municipalityId" => $jsonBody->municipality,
    "latitude" => $jsonBody->latitude,
    "longitude" => $jsonBody->longitude,
    "phoneNumber" => $jsonBody->phoneNumber,
    "description" => $jsonBody->description,
    "disabled" => $jsonBody->disabled,
    "userId" => $req->getSession()->id,
]);
$stmt->execute();
if (!$req->getSession()->shopId) {
    $req->getSession()->shopId = $stmt->insert_id;
}

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$shop_category WHERE shop_id = @{i:shopId}", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

foreach ($jsonBody->categories as $categoryId) {
    $stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_category(
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
    $stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_photo(
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
