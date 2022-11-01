<?php

require_once '../utils/request.php';
require_once '_validReports.php';

$req->requireAdminPrivileges();

$obj = new \stdClass();
$obj->generators = $validReports;
$req->success($obj);
