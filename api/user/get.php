<?php

require '../utils/strict.php';
require '../utils/private/db.php';
require "../utils/utils.php";

header('Content-type: application/json');

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

if (!isset($_GET["id"])) {
    resFail("No user specified");
}
$userId = $_GET["id"];

$stmt = $db->prepare("SELECT user_id as id, email as email, display_name as displayName, role as role FROM users WHERE user_id = ? ORDER BY created_at");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

resSuccess($resObj);
