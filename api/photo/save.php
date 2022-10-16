<?php

require "../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->useDb();
$req->useSession();

$body = $req->getBody();

$stmt = $req->prepareQuery("INSERT INTO photos(photo) VALUES (@{s:photo})", [
    "photo" => $body,
]);
$stmt->execute();
$photoId = $stmt->insert_id;

$resObj = new \stdClass();
$resObj->id = $photoId;

$req->success($resObj);
