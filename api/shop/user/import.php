<?php

require_once "../../utils/request.php";

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
                "zone" => ["type" => "integer"],
                "municipality" => ["type" => "integer"],
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

// $stmt = $req->prepareQuery("DELETE FROM \$moderation\$shop_category WHERE shop_id = @{i:shopId}", [
//     "shopId" => $req->getSession()->shopId,
// ]);
// $stmt->execute();

// foreach ($jsonBody->categories as $categoryId) {
//     $stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_category(
//         category_id,
//         shop_id
//     ) VALUES (
//         @{i:categoryId},
//         @{i:shopId}
//     )", [
//         "categoryId" => $categoryId,
//         "shopId" => $req->getSession()->shopId,
//     ]);
//     $stmt->execute();
// }

// $stmt = $req->prepareQuery("DELETE FROM \$moderation\$shop_photo WHERE shop_id = @{i:shopId}", [
//     "shopId" => $req->getSession()->shopId,
// ]);
// $stmt->execute();

// foreach ($jsonBody->photos as $photoId) {
//     $stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_photo(
//         photo_id,
//         shop_id
//     ) VALUES (
//         @{i:photoId},
//         @{i:shopId}
//     ) ON DUPLICATE KEY UPDATE photo_id = photo_id", [
//         "photoId" => $photoId,
//         "shopId" => $req->getSession()->shopId,
//     ]);
//     $stmt->execute();
// }

// $resObj = new \stdClass();
// $resObj->id = $req->getSession()->shopId;

// $req->success($resObj);

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
