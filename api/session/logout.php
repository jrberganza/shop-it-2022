<?php

require "../private/strict.php";
require "../private/db.php";
require "../private/utils.php";

header("Content-type: application/json");

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

$stmt = $db->prepare("DELETE FROM sessions WHERE token = ?");
$stmt->bind_param("s", $session->token);
$stmt->execute();

setcookie("session_token", "", [
    'expires' => time() - 3600,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Strict',
]);

resSuccess(new \stdClass());
