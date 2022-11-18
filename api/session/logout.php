<?php

require_once '../utils/request.php';

$req->requireLoggedIn();

$stmt = $req->prepareQuery("DELETE FROM sessions WHERE token = @{s:token}", [
    "token" => $req->getSession()->token
]);
$stmt->execute();

// setcookie("session_token", "", [
//     'expires' => time() - 3600,
//     'path' => '/',
//     'httponly' => true,
//     'samesite' => 'Strict',
// ]);
setcookie("session_token", "", time() - 3600, '/', '', false, true);

$req->success(new \stdClass());
