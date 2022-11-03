<?php

require_once "../../utils/request.php";

$req->contentType("text/xml");

$req->requireLoggedIn();

$stmt = $req->prepareQuery("SELECT
    s.name,
    s.zone,
    s.municipality_id as municipality,
    s.latitude,
    s.longitude,
    s.phone_number,
    s.description,
    s.disabled
FROM
    shops s
WHERE
    shop_id = @{i:shopId}", [
    "shopId" => $req->getSession()->shopId,
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

$zoneEl = $xml->createElement("zone", $shop["zone"]);
$shopEl->appendChild($zoneEl);

$municipalityEl = $xml->createElement("municipality", $shop["municipality"]);
$shopEl->appendChild($municipalityEl);

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
    product_id,
    name,
    price,
    description,
    disabled
FROM
    products p
WHERE
    shop_id = @{i:shopId}", [
    "shopId" => $req->getSession()->shopId
]);
$stmt->execute();
$result = $stmt->get_result();

while ($product = $result->fetch_array()) {
    $productEl = $xml->createElement("product");
    $shopEl->appendChild($productEl);

    $idEl = $xml->createElement("id", $product["product_id"]);
    $productEl->appendChild($idEl);

    $nameEl = $xml->createElement("name", $product["name"]);
    $productEl->appendChild($nameEl);

    $priceEl = $xml->createElement("price", $product["price"]);
    $productEl->appendChild($priceEl);

    $descriptionEl = $xml->createElement("description", $product["description"]);
    $productEl->appendChild($descriptionEl);

    $disabledEl = $xml->createElement("disabled", $product["disabled"]);
    $productEl->appendChild($disabledEl);
}

$req->success($xml->saveXML());
