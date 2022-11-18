<?php

require_once '../utils/request.php';

$req->requireMethod("POST");

$req->requireLoggedIn();

$jsonBody = $req->getJsonBody([
    "itemType" => [
        "type" => "string",
        "in" => ["shop", "product"],
    ],
    "itemId" => [
        "type" => "integer",
    ],
    "content" => [
        "type" => "string",
        "maxLength" => 512,
    ],
    "parentCommentId" => [
        "type" => "integer",
        "optional" => true,
    ],
]);

$stmt = $req->prepareQuery("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'comments' AND table_schema = DATABASE()", []);
$stmt->execute();
$result = $stmt->get_result();
$nextId = $result->fetch_array()[0];

$stmt = $req->prepareQuery("SELECT max(comment_id)+1 FROM \$moderation\$comments", []);
$stmt->execute();
$result = $stmt->get_result();
$nextId = max($nextId, $result->fetch_array()[0]);

$query = "INSERT INTO \$moderation\$comments(
    comment_id,";
if ($jsonBody->itemType == "shop") {
    $query .= "shop_id,";
} else {
    $query .= "product_id,";
}
$query .= "
    author_id,
    content,
    parent_comment_id
) VALUES (
    @{i:commentId},
    @{i:itemId},
    @{i:authorId},
    @{s:content},
    @{i:parentCommentId}
)";

$stmt = $req->prepareQuery($query, [
    "commentId" => $nextId,
    "itemId" => $jsonBody->itemId,
    "authorId" => $req->getSession()->id,
    "content" => $jsonBody->content,
    "parentCommentId" => isset($jsonBody->parentCommentId) ? $jsonBody->parentCommentId : null,
]);
$stmt->execute();
$commentId = $stmt->insert_id;

$resObj = new \stdClass();
$resObj->id = $commentId;

$req->success($resObj);
