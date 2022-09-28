<?php

require "../../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->useDb();
$req->useSession();

if (!$req->session->isLoggedIn()) {
    $req->fail("Not logged in");
}

if (!isset($_GET["id"])) {
    $req->fail("No shop specified");
}
$shopId = $_GET["id"];

$body = $req->getBody();

$stmt = $req->prepareQuery("INSERT INTO shop_photos(shop_id, photo) VALUES (@{i:shopId}, @{b:photo})", [
    "shopId" => $shopId,
    "photo" => $body,
]);
$stmt->execute();
$shopPhotoId = $stmt->insert_id;

$resObj = new \stdClass();
$resObj->id = $shopPhotoId;

$req->success($resObj);
