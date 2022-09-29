<?php

require '../utils/request.php';

$req->useDb();
$req->useSession();

$req->requireLoggedIn();

$resObj = new \stdClass();
$resObj->displayName = $req->session->displayName;
$resObj->role = $req->session->role;

$req->success($resObj);
