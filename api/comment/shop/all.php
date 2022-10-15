<?php

require '../../utils/request.php';

$req->useDb();

if (!isset($_GET["id"])) {
    $req->fail("No product specified");
}
$shopId = $_GET['id'];

function getComments(Request $req, int $shopId, ?int $parent = null)
{
    $stmt = $req->prepareQuery("SELECT
        c.comment_id as id,
        u.display_name as author,
        c.created_at as publishedAt,
        c.content as content
    FROM
        comments c
    JOIN
        users u ON c.author_id = u.user_id
    WHERE
        c.shop_id = @{i:shopId} AND
        (c.parent_comment_id = @{i:parentCommentId} OR (@{i:parentCommentId} IS NULL AND c.parent_comment_id IS NULL))", [
        "shopId" => $shopId,
        "parentCommentId" => $parent,
    ]);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = array();

    while ($row = $result->fetch_object()) {
        $comment = new \stdClass();
        $comment->data = $row;
        $comment->children = getComments($req, $shopId, $row->id);
        array_push($comments, $comment);
    }

    return $comments;
}


$allComments = getComments($req, $shopId);

$resObj = new \stdClass();
$resObj->comments = $allComments;

$req->success($resObj);
