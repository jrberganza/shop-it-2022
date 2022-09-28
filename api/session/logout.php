<?php

require '../utils/request.php';

$req->useDb();
$req->useSession();

if (!$req->session->isLoggedIn()) {
    $req->fail("Not logged in");
}

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
