<?php

require "../utils/request.php";

$req->requireMethod("POST");

$req->requireAdminPrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer",
    ],
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
        "optional" => true,
    ],
    "role" => [
        "type" => "string",
    ],
]);

if ($jsonBody->role != 'user' && $jsonBody->role != 'employee' && $jsonBody->role != 'admin') {
    $req->fail("Expected role to be \"user\",  \"employee\" or  \"admin\"");
}

$stmt = $req->prepareQuery("SELECT user_id FROM users WHERE email = @{s:email} AND user_id != @{i:userId}", [
    "email" => $jsonBody->email,
    "userId" => $jsonBody->id,
]);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_array();

if ($row) {
    $req->fail("A user with that e-mail address has already registered");
}

$query = "UPDATE users SET email = @{s:email}, display_name = @{s:displayName}, role = @{s:role}";
$pHash = null;
if (isset($jsonBody->password) && strlen($jsonBody->password) > 0) {
    $pHash = password_hash($jsonBody->password, PASSWORD_BCRYPT);
    $query .= ", password_hash = @{s:passwordHash}";
}
$query .= " WHERE user_id = @{i:userId}";

$stmt = $req->prepareQuery($query, [
    "email" => $jsonBody->email,
    "displayName" => $jsonBody->displayName,
    "role" => $jsonBody->role,
    "passwordHash" => $pHash,
    "userId" => $jsonBody->id,
]);
$stmt->execute();

$resObj = new \stdClass();

$req->success($resObj);
