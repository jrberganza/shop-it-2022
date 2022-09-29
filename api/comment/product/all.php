<?php

require '../../utils/request.php';

function generateComments($level, $first = true)
{
    if ($level == 0) return array();

    $arr = array();

    for ($currId = 0; $currId < random_int($first ? 1 : 0, 4); $currId++) {
        $comment = new \stdClass();

        $comment->data = new \stdClass();
        $comment->data->id = random_int(0, 1000) * $currId;
        $comment->data->author = "Usuario " . random_int(1000, 9999);
        $comment->data->publishedAt = "31/12/2022";
        $comment->data->content = "Hey! " . random_int(1000, 9999);

        $comment->children = generateComments($level - 1, false);

        array_push($arr, $comment);
    }

    return $arr;
}


$allComments = generateComments(5);

$resObj = new \stdClass();
$resObj->comments = $allComments;

$req->success($resObj);
