<?php

require '../utils/request.php';

$req->useDb();
$req->useSession();

if (!$req->session->isLoggedIn()) {
    $req->fail("Not logged in", 200);
}

$resObj = new \stdClass();
$resObj->displayName = $req->session->displayName;
$resObj->role = $req->session->role;

$req->success($resObj);
