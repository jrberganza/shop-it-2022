<?php

require "../utils/request.php";

$req->useDb();
$req->useSession();

if (!$req->session->canManageSite()) {
    $req->fail("Not authorized", 403);
}

$stmt = $req->prepareQuery("SELECT user_id as id, email as email, display_name as displayName, role as role FROM users ORDER BY created_at", []);
$stmt->execute();
$result = $stmt->get_result();

$resObj = new \stdClass();
$resObj->users = array();

while ($row = $result->fetch_object()) {
    array_push($resObj->users, $row);
}

$req->success($resObj);
