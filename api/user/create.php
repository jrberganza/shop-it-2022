<?php

require "../utils/request.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

$req->useDb();
$req->useSession();

if (!$req->session->hasAdminPrivileges()) {
    $req->fail("Not authorized", 403);
}

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
    "role" => [
        "type" => "string",
    ],
]);

if ($jsonBody->role != 'user' && $jsonBody->role != 'employee' && $jsonBody->role != 'admin') {
    $req->fail("Expected role to be \"user\",  \"employee\" or  \"admin\"");
}

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

$stmt = $req->prepareQuery("INSERT INTO users(email, display_name, password_hash, role) VALUES (@{s:email}, @{s:displayName}, @{s:passwordHash}, @{s:role})", [
    "email" => $jsonBody->email,
    "displayName" => $jsonBody->displayName,
    "passwordHash" => $pHash,
    "role" => $jsonBody->role,
]);
$stmt->execute();
$userId = $stmt->insert_id;

$resObj = new \stdClass();
$resObj->id = $userId;

$req->success($resObj);
