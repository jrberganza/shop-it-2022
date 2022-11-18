<?php

require_once '../utils/request.php';

$req->requireMethod("POST");

$jsonBody = $req->getJsonBody([
    "email" => [
        "type" => "string",
        "maxLength" => 100,
    ],
    "displayName" => [
        "type" => "string",
        "maxLength" => 100,
    ],
    "password" => [
        "type" => "string",
    ],
]);

$stmt = $req->prepareQuery("SELECT user_id FROM users WHERE email = @{s:email}", [
    "email" => $jsonBody->email,
]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();

if ($row) {
    $req->fail("A user with that e-mail address has already registered");
}

$pHash = password_hash($jsonBody->password, PASSWORD_BCRYPT);

$stmt = $req->prepareQuery("INSERT INTO users(email, display_name, password_hash, role) VALUES (@{s:email}, @{s:displayName}, @{s:passwordHash}, 'user')", [
    "email" => $jsonBody->email,
    "displayName" => $jsonBody->displayName,
    "passwordHash" => $pHash,
]);
$stmt->execute();
$userId = $stmt->insert_id;

$token = generateSessionToken();

$stmt = $req->prepareQuery("INSERT INTO sessions(user_id, token) VALUES (@{i:userId}, @{s:token})", [
    "userId" => $userId,
    "token" => $token,
]);
$stmt->execute();

$resObj = new \stdClass();
$resObj->displayName = $jsonBody->displayName;
$resObj->role = 'user';

// setcookie("session_token", $token, [
//     'expires' => time() + 86400 * 7,
//     'path' => '/',
//     'httponly' => true,
//     'samesite' => 'Strict',
// ]);
setcookie("session_token", $token, time() + 86400 * 7, '/', '', false, true);

$req->success($resObj);
