<?php

require '../../private/strict.php';
require '../../private/db.php';
require "../../private/utils.php";

header('Content-type: text/xml');

set_error_handler(function () {
    $xml = new DOMDocument("1.0");
    $xml->formatOutput = true;

    $errorEl = $xml->createElement("shop", "Internal error");
    $xml->appendChild($errorEl);

    http_response_code(500);

    echo "" . $xml->saveXML() . "";
    exit();
});

set_exception_handler(function () {
    $xml = new DOMDocument("1.0");
    $xml->formatOutput = true;

    $errorEl = $xml->createElement("error", "Internal error");
    $xml->appendChild($errorEl);

    http_response_code(500);

    echo "" . $xml->saveXML() . "";
    exit();
});

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

if (!isset($_GET["id"])) {
    resFail("No shop specified");
}
$shopId = $_GET["id"];

$stmt = $db->prepare("SELECT
    name,
    address,
    latitude,
    longitude,
    phone_number,
    description,
    disabled
FROM
    shops s
WHERE
    shop_id = ? AND
    user_id = ?");
$stmt->bind_param("ii", $shopId, $session->id);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_array();

$xml = new DOMDocument("1.0");
$xml->formatOutput = true;

$shopEl = $xml->createElement("shop");
$xml->appendChild($shopEl);
if (!$shop) {
    echo "" . $xml->saveXML() . "";
    exit();
}

$nameEl = $xml->createElement("name", $shop["name"]);
$shopEl->appendChild($nameEl);

$addressEl = $xml->createElement("address", $shop["address"]);
$shopEl->appendChild($addressEl);

$latitudeEl = $xml->createElement("latitude", $shop["latitude"]);
$shopEl->appendChild($latitudeEl);

$longitudeEl = $xml->createElement("longitude", $shop["longitude"]);
$shopEl->appendChild($longitudeEl);

$phone_numberEl = $xml->createElement("phone_number", $shop["phone_number"]);
$shopEl->appendChild($phone_numberEl);

$descriptionEl = $xml->createElement("description", $shop["description"]);
$shopEl->appendChild($descriptionEl);

$disabledEl = $xml->createElement("disabled", $shop["disabled"]);
$shopEl->appendChild($disabledEl);

$stmt = $db->prepare("SELECT
    name,
    price,
    description,
    disabled
FROM
    products p
WHERE
    shop_id = ?");
$stmt->bind_param("i", $shopId);
$stmt->execute();
$result = $stmt->get_result();

$productsEl = $xml->createElement("products");
$shopEl->appendChild($productsEl);
while ($product = $result->fetch_array()) {
    $nameEl = $xml->createElement("name", $product["name"]);
    $productsEl->appendChild($nameEl);

    $priceEl = $xml->createElement("price", $product["price"]);
    $productsEl->appendChild($priceEl);

    $descriptionEl = $xml->createElement("description", $product["description"]);
    $productsEl->appendChild($descriptionEl);

    $disabledEl = $xml->createElement("disabled", $product["disabled"]);
    $productsEl->appendChild($disabledEl);
}

echo "" . $xml->saveXML() . "";
exit();
