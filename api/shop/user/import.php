<?php

require "../../utils/request.php";

$req->requireLoggedIn();

$dom = new DOMDocument();
if (!$dom->load("php://input")) {
    $req->fail("Malformed request body");
}

$processed = domToJson([$dom], [
    "type" => "object",
    "children" => [
        "shop" => [
            "type" => "object",
            "children" => [
                "name" => ["type" => "string"],
                "address" => ["type" => "string"],
                "latitude" => ["type" => "double"],
                "longitude" => ["type" => "double"],
                "phonenumber" => ["type" => "string"],
                "description" => ["type" => "string"],
                "disabled" => ["type" => "boolean"],
                "product" => [
                    "type" => "array",
                    "child" => [
                        "type" => "object",
                        "children" => [
                            "id" => ["type" => "integer"],
                            "name" => ["type" => "string"],
                            "price" => ["type" => "double"],
                            "description" => ["type" => "string"],
                            "disabled" => ["type" => "boolean"],
                        ]
                    ]
                ],
            ],
        ]
    ],
]);

if (!isset($processed->shop)) {
    $req->fail("Malformed request body");
}

$body = $processed->shop;

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
    "disabled" => [
        "type" => "boolean",
    ],
    "product" => [
        "type" => "array",
        "validation" => function ($arr) use ($req) {
            foreach ($arr as $child) {
                if ($error = validate($child, [
                    "id" => [
                        "type" => "integer",
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
                    // TODO: Add missing props
                ])) {
                    $req->fail($error);
                }
            }
        }
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
        "name" => $body->name,
        "address" => $body->address,
        "latitude" => $body->latitude,
        "longitude" => $body->longitude,
        "phoneNumber" => $body->phonenumber,
        "description" => $body->description,
        "disabled" => $body->disabled,
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
        "name" => $body->name,
        "address" => $body->address,
        "latitude" => $body->latitude,
        "longitude" => $body->longitude,
        "phoneNumber" => $body->phonenumber,
        "description" => $body->description,
        "disabled" => $body->disabled,
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

if (isset($body->product)) {
    foreach ($body->product as $product) {
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
