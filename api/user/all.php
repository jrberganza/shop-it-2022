<?php

require_once "../utils/request.php";

$req->requireAdminPrivileges();

$stmt = $req->prepareQuery("SELECT user_id as id, email as email, display_name as displayName, role as role FROM users WHERE user_id != @{i:userId} ORDER BY created_at", [
    "userId" => $req->getSession()->id,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = new \stdClass();
$resObj->users = array();

while ($row = $result->fetch_object()) {
    array_push($resObj->users, $row);
}

$req->success($resObj);
