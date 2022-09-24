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

$stmt = $db->prepare("SELECT user_id, email, display_name, password_hash, role FROM users WHERE email = ?");
$stmt->bind_param("s", $jsonBody->email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_array();

if (!$user) {
    resFail("Incorrect e-mail or password");
}

if (!password_verify($jsonBody->password, $user["password_hash"])) {
    resFail("Incorrect e-mail or password");
}

$token = generateSessionToken();

$stmt = $db->prepare("INSERT INTO sessions(user_id, token) VALUES (?, ?)");
$stmt->bind_param("is", $user["user_id"], $token);
$stmt->execute();

$resObj = new \stdClass();
$resObj->displayName = $user["display_name"];
$resObj->role = $user["role"];

setcookie("session_token", $token, [
    'expires' => time() + 86400 * 7,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Strict',
]);

resSuccess($resObj);
