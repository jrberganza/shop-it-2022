<?php

require '../../utils/strict.php';
require '../../utils/private/db.php';
require "../../utils/utils.php";

if (!isset($_GET["id"])) {
    resFail("No shop photo specified");
}
$photoId = $_GET["id"];

$stmt = $db->prepare("SELECT
    photo
FROM
    shop_photos
WHERE
    shop_photo_id = ?");
$stmt->bind_param("i", $photoId);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_array();

if (!$row) {
    resFail("No shop photo found");
}

header('Content-type: image/png');
echo $row["photo"];
