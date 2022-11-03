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
    "name" => $body->name,
    "zone" => $body->zone,
    "municipalityId" => $body->municipality,
    "latitude" => $body->latitude,
    "longitude" => $body->longitude,
    "phoneNumber" => $body->phonenumber,
    "description" => $body->description,
    "disabled" => $body->disabled,
    "userId" => $req->getSession()->id,
]);
$stmt->execute();
if (!$req->getSession()->shopId) {
    $req->getSession()->shopId = $stmt->insert_id;
}

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_category SELECT * FROM shop_category WHERE shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE shop_id = shop_category.shop_id", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO \$moderation\$shop_photo SELECT * FROM shop_photo WHERE shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE shop_id = shop_photo.shop_id", [
    "shopId" => $req->getSession()->shopId,
]);
$stmt->execute();

// if (isset($body->product)) {
//     foreach ($body->product as $product) {
//         $stmt = $req->prepareQuery("INSERT INTO \$moderation\$products SELECT * FROM products WHERE product_id = @{i:productId} AND shop_id = @{i:shopId} ON DUPLICATE KEY UPDATE product_id = products.product_id", [
//             "productId" => $product->id,
//             "shopId" => $req->getSession()->shopId,
//         ]);
//         $stmt->execute();

//         if (isset($product->id)) {
//             $stmt = $req->prepareQuery("UPDATE
//                 \$moderation\$products
//             SET
//                 name = @{s:name},
//                 price = @{s:price},
//                 description = @{s:description},
//                 disabled = @{i:disabled}
//             WHERE
//                 product_id = @{i:productId} AND
//                 shop_id = @{i:shopId}", [
//                 "name" => $body->name,
//                 "price" => $body->price,
//                 "description" => $body->description,
//                 "disabled" => $body->disabled,
//                 "productId" => $body->id,
//                 "shopId" => $req->getSession()->shopId,
//             ]);
//             $stmt->execute();
//         } else {
//             $stmt = $req->prepareQuery("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'products' AND table_schema = DATABASE()", []);
//             $stmt->execute();
//             $result = $stmt->get_result();
//             $nextId = $result->fetch_column(0);

//             $stmt = $req->prepareQuery("SELECT max(product_id)+1 FROM \$moderation\$products", []);
//             $stmt->execute();
//             $result = $stmt->get_result();
//             $nextId = max($nextId, $result->fetch_column(0));

//             $stmt = $req->prepareQuery("INSERT INTO \$moderation\$products(
//                 product_id,
//                 name,
//                 price,
//                 description,
//                 disabled,
//                 shop_id
//             ) VALUES (
//                 @{i:productId},
//                 @{s:name},
//                 @{s:price},
//                 @{s:description},
//                 @{i:disabled},
//                 @{i:shopId}
//             )", [
//                 "productId" => $nextId,
//                 "name" => $body->name,
//                 "price" => $body->price,
//                 "description" => $body->description,
//                 "disabled" => $body->disabled,
//                 "shopId" => $req->getSession()->shopId,
//             ]);
//             $stmt->execute();
//             $body->id = $stmt->insert_id;
//         }
//     }
// }

$resObj = new \stdClass();
$resObj->id = $req->getSession()->shopId;

$req->success($resObj);
