<?php

require '../../utils/request.php';

$req->requireEmployeePrivileges();

$comment = new \stdClass();

$params = $req->getParams([
    "id" => [],
]);

$comment->id = random_int(0, 1000) * $params["id"];
$comment->author = "Usuario " . random_int(1000, 9999);
$comment->publishedAt = "31/12/2022";
$comment->content = "Hey! " . random_int(1000, 9999);

$req->success($comment);
