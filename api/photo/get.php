<?php

require "../utils/request.php";

$params = $req->getParams([
    "id" => [],
]);

$stmt = $req->prepareQuery("SELECT
    photo
FROM
    photos
WHERE
    photo_id = @{i:photoId}", [
    "photoId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_array();

if (!$row) {
    $req->fail("No photo found");
}

$req->contentType('image/png');
$req->success($row["photo"]);
