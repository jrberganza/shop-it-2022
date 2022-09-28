<?php

require "../utils/strict.php";
require "../utils/private/db.php";
require "../utils/utils.php";

header("Content-type: application/json");

$session = getCurrentSession($db);

if ($session) {
    $resObj = new \stdClass();
    $resObj->displayName = $session->displayName;
    $resObj->role = $session->role;

    resSuccess($resObj);
} else {
    resFail("Not logged in", 200);
}
