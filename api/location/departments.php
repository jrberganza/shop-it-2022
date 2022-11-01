<?php

require '../utils/request.php';

$stmt = $req->prepareQuery("SELECT
    department_id as id,
    name as name
FROM
    departments", []);
$stmt->execute();
$result = $stmt->get_result();

$allDepartments = array();

while ($row = $result->fetch_object()) {
    array_push($allDepartments, $row);
}

$resObj = new \stdClass();
$resObj->departments = $allDepartments;

$req->success($resObj);
