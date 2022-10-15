<?php

require "../utils/request.php";

$req->useDb();

if (!isset($_GET["id"])) {
    $req->fail("No photo specified");
}
$photoId = $_GET["id"];

$stmt = $req->prepareQuery("SELECT
    photo
FROM
    photos
WHERE
    photo_id = @{i:photoId}", [
    "photoId" => $photoId,
]);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_array();

if (!$row) {
    $req->fail("No photo found");
}

$req->contentType('image/png');
$req->success($row["photo"]);
