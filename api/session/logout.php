<?php

require '../utils/request.php';

$req->useDb();
$req->useSession();

$req->requireLoggedIn();

$stmt = $req->prepareQuery("DELETE FROM sessions WHERE token = @{s:token}", [
    "token" => $req->session->token
]);
$stmt->execute();

setcookie("session_token", "", [
    'expires' => time() - 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Strict',
]);

$req->success(new \stdClass());
