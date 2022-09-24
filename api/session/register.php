<?php

require "../private/strict.php";
require "../private/db.php";
require "../private/utils.php";

header("Content-type: application/json");


if ($_SERVER["REQUEST_METHOD"] != "POST") {
    resFail("Wrong HTTP Method");
}

$body = file_get_contents("php://input");
$jsonBody = json_decode($body);

if (!$jsonBody) {
    resFail("Malformed request body");
}

if (strlen($jsonBody->email) <= 0 || strlen($jsonBody->email) > 100) {
    resFail("Invalid e-mail");
}

if (strlen($jsonBody->displayName) <= 0 || strlen($jsonBody->displayName) > 100) {
    resFail("Invalid display name");
}

if (strlen($jsonBody->password) <= 0) {
    resFail("Invalid password");
}

$stmt = $db->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $jsonBody->email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();

if ($row) {
    resFail("A user with that e-mail address has already registered");
}

$pHash = password_hash($jsonBody->password, PASSWORD_BCRYPT);

$stmt = $db->prepare("INSERT INTO users(email, display_name, password_hash, role) VALUES (?, ?, ?, 'user')");
$stmt->bind_param("sss", $jsonBody->email, $jsonBody->displayName, $pHash);
$stmt->execute();

resSuccess(new \stdClass());
