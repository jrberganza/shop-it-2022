<?php

require '../utils/request.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->useDb();

$jsonBody = $req->getJsonBody([
    "email" => [
        "type" => "string",
        "maxLength" => 100,
    ],
    "password" => [
        "type" => "string",
    ]
]);

$stmt = $req->prepareQuery("SELECT user_id, email, display_name, password_hash, role FROM users WHERE email = @{s:email}", [
    "email" => $jsonBody->email
]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_array();

if (!$user) {
    $req->fail("Incorrect e-mail or password");
}

if (!password_verify($jsonBody->password, $user["password_hash"])) {
    $req->fail("Incorrect e-mail or password");
}

$token = generateSessionToken();

$stmt = $req->prepareQuery("INSERT INTO sessions(user_id, token) VALUES (@{i:user_id}, @{s:token})", [
    "user_id" => $user["user_id"],
    "token" => $token
]);
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

$req->success($resObj);
