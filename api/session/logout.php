<?php

require '../utils/request.php';

$req->requireLoggedIn();

$stmt = $req->prepareQuery("DELETE FROM sessions WHERE token = @{s:token}", [
    "token" => $req->getSession()->token
]);
$stmt->execute();

setcookie("session_token", "", [
    'expires' => time() - 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Strict',
]);

$req->success(new \stdClass());
