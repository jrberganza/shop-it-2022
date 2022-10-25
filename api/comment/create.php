<?php

require '../utils/request.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $req->fail("Wrong HTTP Method");
}

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

$query = "INSERT INTO comments(";
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
    @{i:itemId},
    @{i:authorId},
    @{s:content},
    @{i:parentCommentId}
)";

$stmt = $req->prepareQuery($query, [
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
