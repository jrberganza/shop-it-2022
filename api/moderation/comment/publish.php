<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer"
    ],
    "reason" => [
        "type" => "string",
        "maxLength" => 255
    ],
]);

$stmt = $req->prepareQuery("SELECT * FROM \$moderation\$comments c WHERE c.comment_id = @{i:commentId}", [
    "commentId" => $jsonBody->id,
]);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_object();

$stmt = $req->prepareQuery("INSERT INTO comments SELECT * FROM \$moderation\$comments c WHERE c.comment_id = @{i:commentId}", [
    "commentId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("DELETE FROM \$moderation\$comments WHERE comment_id = @{i:commentId}", [
    "commentId" => $jsonBody->id,
]);
$stmt->execute();

$stmt = $req->prepareQuery("INSERT INTO moderation_events (
    user_id,
    item_owner_id,
    item_id,
    item_type,
    item_name,
    item_description,
    item_created_at,
    reason,
    published
) VALUES (
    @{i:userId},
    @{i:itemOwnerId},
    @{i:itemId},
    'comment',
    '',
    @{s:itemDescription},
    @{s:itemCreatedAt},
    @{s:reason},
    TRUE
)", [
    "userId" => $req->getSession()->id,
    "itemOwnerId" => $comment->author_id,
    "itemId" => $comment->comment_id,
    "itemDescription" => $comment->content,
    "itemCreatedAt" => $comment->created_at,
    "reason" => $jsonBody->reason,
]);
$stmt->execute();

$resObj = new \stdClass();
$req->success($resObj);
