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

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$shops SELECT * FROM shops WHERE shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE shop_id = shops.shop_id", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_category SELECT * FROM shop_category WHERE shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE shop_id = shop_category.shop_id", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_photo SELECT * FROM shop_photo WHERE shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE shop_id = shop_photo.shop_id", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

$nextId = $req->getSession()->shopId;
if (!$req->getSession()->shopId) {
    $stmt = $req->prepareQuery("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'shops' AND table_schema = DATABASE()", []);
    $stmt->execute();
    $result = $stmt->get_result();
    $nextId = $result->fetch_column(0);

    $stmt = $req->prepareQuery("SELECT max(shop_id)+1 FROM \$moderation\$shops", []);
    $stmt->execute();
    $result = $stmt->get_result();
    $nextId = max($nextId, $result->fetch_column(0));
}

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
    "shopId" => $nextId,
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
    $req->getSession()->shopId = $nextId;
}

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$shop_category WHERE shop_id = @{i:shopId}", [
    "shopId" => $nextId,
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
        "shopId" => $nextId,
    ]);
    $stmt->execute();
}

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$shop_photo WHERE shop_id = @{i:shopId}", [
    "shopId" => $nextId,
]);
$stmt->execute();

foreach ($jsonBody->photos as $photoId) {
    $stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_photo(
        photo_id,
        shop_id
    ) VALUES (
        @{i:photoId},
        @{i:shopId}
    ) ON DUPLICATE KEY UPDATE photo_id = photo_id", [
        "photoId" => $photoId,
        "shopId" => $nextId,
    ]);
    $stmt->execute();
}

$resObj = new \stdClass();
$resObj->id = $nextId;

$req->success($resObj);
