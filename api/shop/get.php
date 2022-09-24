<?php

require '../private/strict.php';
require '../private/db.php';
require "../private/utils.php";

header('Content-type: application/json');

if (!isset($_GET["id"])) {
    resFail("No shop specified");
}
$shopId = $_GET["id"];

$stmt = $db->prepare("SELECT
    s.shop_id as id,
    s.name as name,
    s.address as address,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    cast(coalesce(r.rating, 0.0) as double) as rating
FROM
    shops s
LEFT JOIN
    (SELECT avg(rating) as rating, shop_id FROM shop_ratings WHERE shop_id = ?) r USING (shop_id)
WHERE
    shop_id = ?");
$stmt->bind_param("ii", $shopId, $shopId);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

$stmt = $db->prepare("SELECT shop_photo_id FROM shop_photos WHERE shop_id = ? ");
$stmt->bind_param("i", $shopId);
$stmt->execute();
$result = $stmt->get_result();

$resObj->photos = array();
while ($row = $result->fetch_array()) {
    array_push($resObj->photos, $row["shop_photo_id"]);
}

// $stmt = $db->prepare("SELECT name, price, substr(description, 1, 100) as shortDesc FROM products WHERE shop_id = ? ORDER BY rand() LIMIT 5");
// $stmt->bind_param("i", $shopId);
// $stmt->execute();
// $result = $stmt->get_result();

// $resObj->products = array();
// while ($row = $result->fetch_object()) {
//     array_push($resObj->products, $row);
// }

// No product registering yet, autogenerate them
$resObj->products = array();
for ($currId = 0; $currId < 24; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . $shopId;
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    array_push($resObj->products, $product);
}

if ($resObj) {
    resSuccess($resObj);
} else {
    resFail("No shop found");
}
