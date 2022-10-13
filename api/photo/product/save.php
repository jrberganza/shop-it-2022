<?php

require "../../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->useDb();
$req->useSession();

$req->requireLoggedIn();

if (!isset($_GET["id"])) {
    $req->fail("No product specified");
}
$productId = $_GET["id"];

$body = $req->getBody();

$stmt = $req->prepareQuery("INSERT INTO photos(photo) VALUES (@{b:photo})", [
    "photo" => $body,
]);
$stmt->execute();
$photoId = $stmt->insert_id;

$stmt = $req->prepareQuery("INSERT INTO product_photo(product_id, photo_id) VALUES (@{i:productId}, @{b:photoId})", [
    "productId" => $productId,
    "photoId" => $photoId,
]);
$stmt->execute();

$resObj = new \stdClass();
$resObj->id = $photoId;

$req->success($resObj);
