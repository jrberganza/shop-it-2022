<?php

require "../utils/request.php";

$req->useDb();
$req->useSession();

if (!$req->session->canManageSite()) {
    $req->fail("Not authorized", 403);
}

if (!isset($_GET["id"])) {
    $req->fail("No user specified");
}
$userId = $_GET["id"];

$stmt = $req->prepareQuery("SELECT user_id as id, email as email, display_name as displayName, role as role FROM users WHERE user_id = @{i:userId} ORDER BY created_at", [
    "userId" => $userId,
]);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

$req->success($resObj);
