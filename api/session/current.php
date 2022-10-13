<?php

require '../utils/request.php';

$req->useDb();
$req->useSession();

$resObj = new \stdClass();
$resObj->displayName = $req->session->displayName;
$resObj->role = $req->session->role;
$resObj->shopId = $req->session->shopId;

$req->success($resObj);
