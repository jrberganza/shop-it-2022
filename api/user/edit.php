<?php

require "../private/strict.php";
require "../private/db.php";
require "../private/utils.php";

header("Content-type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    resFail("Wrong HTTP Method");
}

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

$body = file_get_contents("php://input");
$jsonBody = json_decode($body);

if (!$jsonBody) {
    resFail("Malformed request body");
}

if (!isset($jsonBody->id)) {
    resFail("Invalid id");
}

if (strlen($jsonBody->email) <= 0 || strlen($jsonBody->email) > 100) {
    resFail("Invalid e-mail");
}

if (strlen($jsonBody->displayName) <= 0 || strlen($jsonBody->displayName) > 100) {
    resFail("Invalid display name");
}

if ($jsonBody->role != 'user' && $jsonBody->role != 'employee' && $jsonBody->role != 'admin') {
    resFail("Invalid role");
}

$stmt = $db->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
$stmt->bind_param("si", $jsonBody->email, $jsonBody->id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();

if ($row) {
    resFail("A user with that e-mail address has already registered");
}

$stmt = $db->prepare("UPDATE users SET email = ?, display_name = ?, role = ? WHERE user_id = ?");
$stmt->bind_param("sssi", $jsonBody->email, $jsonBody->displayName, $jsonBody->role, $jsonBody->id);
$stmt->execute();

if (strlen($jsonBody->password) > 0) {
    $pHash = password_hash($jsonBody->password, PASSWORD_BCRYPT);

    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $stmt->bind_param("si", $pHash, $jsonBody->id);
    $stmt->execute();
}

$resObj = new \stdClass();

resSuccess($resObj);
