<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer"
    ],
]);

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$comments WHERE comment_id = @{i:commentId}", [
    "commentId" => $jsonBody->id,
]);
$stmt->execute();

$resObj = new \stdClass();
$req->success($resObj);
