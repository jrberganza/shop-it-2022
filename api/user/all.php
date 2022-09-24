<?php

require '../private/strict.php';
require '../private/db.php';
require "../private/utils.php";

header('Content-type: application/json');

$session = getCurrentSession($db);

if (!$session) {
    resFail("Not logged in");
}

$stmt = $db->prepare("SELECT user_id as id, email as email, display_name as displayName, role as role FROM users ORDER BY created_at");
$stmt->execute();
$result = $stmt->get_result();

$resObj = new \stdClass();
$resObj->users = array();

while ($row = $result->fetch_object()) {
    array_push($resObj->users, $row);
}

resSuccess($resObj);
