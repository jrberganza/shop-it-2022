<?php

require '../../utils/request.php';

$comment = new \stdClass();

$currId = $_GET["id"];

$comment->id = random_int(0, 1000) * $currId;
$comment->author = "Usuario " . random_int(1000, 9999);
$comment->publishedAt = "31/12/2022";
$comment->content = "Hey! " . random_int(1000, 9999);

$req->success($comment);
