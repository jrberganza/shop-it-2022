<?php

require '../utils/request.php';

$req->requireMethod("POST");

$req->requireLoggedIn();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer",
    ],
    "value" => [
        "type" => "integer",
        "min" => -1,
        "max" => 1,
    ],
]);

$stmt = $req->prepareQuery("INSERT INTO comment_votes(
    comment_id,
    user_id,
    value
) VALUES (
    @{i:commentId},
    @{i:userId},
    @{i:value}
) ON DUPLICATE KEY UPDATE
    value = @{i:value}", [
    "commentId" => $jsonBody->id,
    "userId" => $req->getSession()->id,
    "value" => $jsonBody->value,
]);
$stmt->execute();
$commentId = $stmt->insert_id;

$resObj = new \stdClass();

$req->success($resObj);
