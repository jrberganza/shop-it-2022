<?php

require "../utils/request.php";

$req->requireMethod("POST");

$body = $req->getBody();

$stmt = $req->prepareQuery("INSERT INTO photos(photo) VALUES (@{s:photo})", [
    "photo" => $body,
]);
$stmt->execute();
$photoId = $stmt->insert_id;

$resObj = new \stdClass();
$resObj->id = $photoId;

$req->success($resObj);
