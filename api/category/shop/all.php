<?php

require_once '../../utils/request.php';

$stmt = $req->prepareQuery("SELECT
    category_id as id,
    name as name
FROM
    categories
WHERE
    disabled = FALSE AND
    type = 'shop'
", []);
$stmt->execute();
$result = $stmt->get_result();

$allCategories = array();

while ($row = $result->fetch_object()) {
    array_push($allCategories, $row);
}

$resObj = new \stdClass();
$resObj->categories = $allCategories;

$req->success($resObj);
