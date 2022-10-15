<?php

require '../../utils/request.php';

$req->useDb();
$req->useSession();

if (!isset($_GET["id"])) {
    $req->fail("No product specified");
}
$productId = $_GET['id'];

function getComments(Request $req, int $productId, ?int $parent = null)
{
    $stmt = $req->prepareQuery("SELECT
        c.comment_id as id,
        u.display_name as author,
        c.created_at as publishedAt,
        c.content as content,
        CAST(coalesce(cv.value, 0) as signed) as voted
    FROM
        comments c
    JOIN
        users u ON c.author_id = u.user_id
    LEFT JOIN
        (SELECT value, comment_id FROM comment_votes WHERE user_id = @{i:userId}) cv USING (comment_id)
    WHERE
        c.product_id = @{i:productId} AND
        (c.parent_comment_id = @{i:parentCommentId} OR (@{i:parentCommentId} IS NULL AND c.parent_comment_id IS NULL))", [
        "productId" => $productId,
        "parentCommentId" => $parent,
        "userId" => $req->session->id,
    ]);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = array();

    while ($row = $result->fetch_object()) {
        $comment = new \stdClass();
        $comment->data = $row;
        $comment->children = getComments($req, $productId, $row->id);
        array_push($comments, $comment);
    }

    return $comments;
}

$allComments = getComments($req, $productId);

$resObj = new \stdClass();
$resObj->comments = $allComments;

$req->success($resObj);
