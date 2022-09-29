<?php

require '../../utils/request.php';

$allComments = array();

for ($currId = 0; $currId < 10; $currId++) {
    $comment = new \stdClass();

    $comment->id = random_int(0, 1000) * $currId;
    $comment->author = "Usuario " . random_int(1000, 9999);
    $comment->publishedAt = "31/12/2022";
    $comment->content = "Hey! " . random_int(1000, 9999);

    array_push($allComments, $comment);
}

$resObj = new \stdClass();
$resObj->pending = $allComments;

$req->success($resObj);
