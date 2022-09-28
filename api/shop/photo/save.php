<?php

require "../../utils/strict.php";
require "../../utils/private/db.php";
require "../../utils/utils.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    resFail("Wrong HTTP Method");
}

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

if (!isset($_GET["id"])) {
    resFail("No shop specified");
}
$shopId = $_GET["id"];

$body = file_get_contents("php://input");

if (!$body) {
    resFail("Malformed request body");
}

$stmt = $db->prepare("INSERT INTO shop_photos(shop_id, photo) VALUES (?, ?)");
$stmt->bind_param("ib", $shopId, $body);
$stmt->execute();
$shopPhotoId = $db->insert_id;

$resObj = new \stdClass();
$resObj->id = $shopPhotoId;

resSuccess($resObj);
