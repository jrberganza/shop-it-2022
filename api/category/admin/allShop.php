<?php

require '../../utils/request.php';

$req->useDb();
$req->useSession();

$req->requireAdminPrivileges();

$stmt = $req->prepareQuery("SELECT
    category_id as id,
    name as name,
    type as type,
    disabled as disabled
FROM
    categories
WHERE
    type = 'shop'
", []);
$stmt->execute();
$result = $stmt->get_result();

$allCategories = array();

while ($row = $result->fetch_object()) {
    $row->disabled = $row->disabled != 0;
    array_push($allCategories, $row);
}

$resObj = new \stdClass();
$resObj->categories = $allCategories;

$req->success($resObj);
