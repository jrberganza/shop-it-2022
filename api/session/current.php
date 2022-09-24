<?php

require "../private/strict.php";
require "../private/db.php";
require "../private/utils.php";

header("Content-type: application/json");

$session = getCurrentSession($db);

if ($session) {
    $resObj = new \stdClass();
    $resObj->displayName = $session->displayName;
    $resObj->role = $session->role;

    resSuccess($resObj);
} else {
    resSuccess(new \stdClass());
}
