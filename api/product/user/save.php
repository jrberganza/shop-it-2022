<?php

require_once "../../utils/request.php";

$req->requireMethod("POST");

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
    "categories" => [
        "type" => "array"
    ],
    "photos" => [
        "type" => "array"
    ],
]);

if (!$req->getSession()->shopId) {
    $req->fail("No shop has been created for this user");
}

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$shops SELECT * FROM shops WHERE shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE shop_id = shops.shop_id", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$products SELECT * FROM products WHERE product_id = @{i:productId} AND shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE product_id = products.product_id", [
    "productId" => isset($jsonBody->id) ? $jsonBody->id : null,
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

if (isset($jsonBody->id)) {
    $stmt = $req->prepareQuery("UPDATE
        \$moderation\$products
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
    $stmt = $req->prepareQuery("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'products' AND table_schema = DATABASE()", []);
    $stmt->execute();
    $result = $stmt->get_result();
    $nextId = $result->fetch_column(0);

    $stmt = $req->prepareQuery("SELECT max(product_id)+1 FROM \$moderation\$products", []);
    $stmt->execute();
    $result = $stmt->get_result();
    $nextId = max($nextId, $result->fetch_column(0));

    $stmt = $req->prepareQuery("INSERT INTO \$moderation\$products(
        product_id,
        name,
        price,
        description,
        disabled,
        shop_id
    ) VALUES (
        @{i:productId},
        @{s:name},
        @{s:price},
        @{s:description},
        @{i:disabled},
        @{i:shopId}
    )", [
        "productId" => $nextId,
        "name" => $jsonBody->name,
        "price" => $jsonBody->price,
        "description" => $jsonBody->description,
        "disabled" => $jsonBody->disabled,
        "shopId" => $req->getSession()->shopId,
    ]);
    $stmt->execute();
    $jsonBody->id = $stmt->insert_id;
}

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$product_category WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

foreach ($jsonBody->categories as $categoryId) {
    $stmt = $req->prepareQuery("INSERT INTO \$moderation\$product_category(
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

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$product_photo WHERE product_id = @{i:productId}", [
    "productId" => $jsonBody->id,
]);
$stmt->execute();

foreach ($jsonBody->photos as $photoId) {
    $stmt = $req->prepareQuery("INSERT INTO \$moderation\$product_photo(
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
