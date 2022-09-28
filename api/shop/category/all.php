<?php

require '../../utils/strict.php';

header('Content-type: application/json');

// TODO: connect to database
// TODO: do it only for the current shop

$allCategories = array();

for ($currId = 0; $currId < 10; $currId++) {
    $category = new \stdClass();

    $category->id = $currId;
    $category->name = bin2hex(random_bytes(8));
    $category->disabled = random_int(0, 1) == 1;
    array_push($allCategories, $category);
}

$resJson = json_encode($allCategories);

echo $resJson;
