<?php

require "../../utils/request.php";

$req->requireLoggedIn();

$xmlBody = simplexml_load_file("php://input");

if (!$xmlBody) {
    $req->fail("Malformed request body");
}

$body = new \stdClass();
$body->name = strval($xmlBody->name);
$body->address = strval($xmlBody->address);
$body->latitude = doubleval($xmlBody->latitude);
$body->longitude = doubleval($xmlBody->longitude);
$body->phonenumber = strval($xmlBody->phonenumber);
$body->description = strval($xmlBody->description);
$body->disabled = boolval($xmlBody->disabled);
$body->categories = [...$xmlBody->categories]; // TODO: fix
$body->products = [...$xmlBody->product]; // TODO: fix

$req->fail($body);

if ($error = validate($body, [
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
    "phonenumber" => [
        "type" => "string",
        "maxLength" => 20,
    ],
    "description" => [
        "type" => "string",
        "maxLength" => 512,
    ],
    // TODO: Add missing props
])) {
    $req->fail($error);
};

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
        "name" => $xmlBody->name,
        "address" => $xmlBody->address,
        "latitude" => $xmlBody->latitude,
        "longitude" => $xmlBody->longitude,
        "phoneNumber" => $xmlBody->phonenumber,
        "description" => $xmlBody->description,
        "disabled" => $xmlBody->disabled,
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
        "name" => $xmlBody->name,
        "address" => $xmlBody->address,
        "latitude" => $xmlBody->latitude,
        "longitude" => $xmlBody->longitude,
        "phoneNumber" => $xmlBody->phonenumber,
        "description" => $xmlBody->description,
        "disabled" => $xmlBody->disabled,
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

if (isset($xmlBody->product)) {
    foreach ($xmlBody->product as $product) {
        $stmt = $req->prepareQuery("SELECT
            product_id
        FROM
            products
        WHERE
            product_id = @{i:productId} AND
            shop_id = @{i:shopId}", [
            "productId" => $product->id,
            "shopId" => $req->getSession()->shopId,
        ]);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_object();

        if ($row) {
            $stmt = $req->prepareQuery("UPDATE products SET
                name = @{s:name},
                price = @{s:price},
                description = @{s:description},
                disabled = @{i:disabled}
            WHERE
                product_id = @{i:productId}", [
                "name" => $product->name,
                "price" => $product->price,
                "description" => $product->description,
                "disabled" => $product->disabled,
                "productId" => $product->id,
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
                "productId" => $product->id,
                "name" => $product->name,
                "price" => $product->price,
                "description" => $product->description,
                "disabled" => $product->disabled,
                "shopId" => $req->getSession()->shopId,
            ]);
            $stmt->execute();
        }
    }
}

$resObj = new \stdClass();
$resObj->id = $req->getSession()->shopId;

$req->success($resObj);
