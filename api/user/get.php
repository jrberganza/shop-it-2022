<?php

require_once "../utils/request.php";

$req->requireAdminPrivileges();

$params = $req->getParams([
    "id" => [],
]);

$stmt = $req->prepareQuery("SELECT user_id as id, email as email, display_name as displayName, role as role FROM users WHERE user_id = @{i:userId} ORDER BY created_at", [
    "userId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

$req->success($resObj);
