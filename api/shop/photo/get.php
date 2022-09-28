<?php

require "../../utils/request.php";

if (!isset($_GET["id"])) {
    $req->fail("No shop photo specified");
}
$photoId = $_GET["id"];

$stmt = $req->prepareQuery("SELECT
    photo
FROM
    shop_photos
WHERE
    shop_photo_id = @{i:photoId}", [
    "photoId" => $photoId,
]);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_array();

if (!$row) {
    $req->fail("No shop photo found");
}

$req->contentType('Content-type: image/png');
echo $row["photo"];
