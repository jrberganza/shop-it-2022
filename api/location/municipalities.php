<?php

require '../utils/request.php';

$params = $req->getParams([
    "department" => [],
]);

$stmt = $req->prepareQuery("SELECT
    municipality_id as id,
    name as name
FROM
    municipalities
WHERE
    department_id = @{i:departmentId}", [
    "departmentId" => $params["department"],
]);
$stmt->execute();
$result = $stmt->get_result();

$allMunicipalities = array();

while ($row = $result->fetch_object()) {
    array_push($allMunicipalities, $row);
}

$resObj = new \stdClass();
$resObj->municipalities = $allMunicipalities;

$req->success($resObj);
