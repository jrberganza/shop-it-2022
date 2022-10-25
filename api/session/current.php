<?php

require '../utils/request.php';

$resObj = new \stdClass();
$resObj->displayName = $req->getSession()->displayName;
$resObj->role = $req->getSession()->role;
$resObj->shopId = $req->getSession()->shopId;

$req->success($resObj);
