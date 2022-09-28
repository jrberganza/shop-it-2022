<?php

require "../../utils/request.php";

$req->contentType("text/xml");

$req->useDb();
$req->useSession();

if (!$req->session->isLoggedIn()) {
    $req->fail("Not logged in");
}

if (!isset($_GET["id"])) {
    $req->fail("No shop specified");
}
$shopId = $_GET["id"];

$stmt = $req->prepareQuery("SELECT
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
    shop_id = @{i:shopId} AND
    user_id = @{i:userId}", [
    "shopId" => $shopId,
    "userId" => $session->id,
]);
$stmt->execute();
$result = $stmt->get_result();
$shop = $result->fetch_array();
if (!$shop) {
    throw new Error("Unknown shop");
}

$xml = new DOMDocument("1.0");
$xml->formatOutput = true;

$shopEl = $xml->createElement("shop");
$xml->appendChild($shopEl);

$nameEl = $xml->createElement("name", $shop["name"]);
$shopEl->appendChild($nameEl);

$addressEl = $xml->createElement("address", $shop["address"]);
$shopEl->appendChild($addressEl);

$latitudeEl = $xml->createElement("latitude", $shop["latitude"]);
$shopEl->appendChild($latitudeEl);

$longitudeEl = $xml->createElement("longitude", $shop["longitude"]);
$shopEl->appendChild($longitudeEl);

$phoneNumberEl = $xml->createElement("phonenumber", $shop["phone_number"]);
$shopEl->appendChild($phoneNumberEl);

$descriptionEl = $xml->createElement("description", $shop["description"]);
$shopEl->appendChild($descriptionEl);

$disabledEl = $xml->createElement("disabled", $shop["disabled"]);
$shopEl->appendChild($disabledEl);

$stmt = $req->prepareQuery("SELECT
    name,
    price,
    description,
    disabled
FROM
    products p
WHERE
    shop_id = @{i:shopId}", [
    "shopId" => $shopId
]);
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

$req->success($xml->saveXML());
