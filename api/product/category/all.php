<?php

require '../../utils/request.php';

$allCategories = array();

for ($currId = 0; $currId < 10; $currId++) {
    $category = new \stdClass();

    $category->id = $currId;
    $category->name = bin2hex(random_bytes(8));
    $category->disabled = random_int(0, 1) == 1;
    array_push($allCategories, $category);
}

$resObj = new \stdClass();
$resObj->categories = $allCategories;

$req->success($resObj);
