<?php

require_once '../../utils/request.php';

$params = $req->getParams([
    "id" => [],
]);

function getComments(Request $req, int $productId, ?int $parent = null)
{
    $stmt = $req->prepareQuery("SELECT
        c.comment_id as id,
        u.display_name as author,
        c.created_at as publishedAt,
        c.content as content,
        CAST(coalesce(cv.total_votes, 0) as signed) as totalVotes,
        CAST(coalesce(uv.value, 0) as signed) as voted
    FROM
        comments c
    JOIN
        users u ON c.author_id = u.user_id
    LEFT JOIN
        (SELECT sum(value) as total_votes, comment_id FROM comment_votes GROUP BY comment_id) cv USING (comment_id)
    LEFT JOIN
        (SELECT value, comment_id FROM comment_votes WHERE user_id = @{i:userId}) uv USING (comment_id)
    WHERE
        c.product_id = @{i:productId} AND
        (c.parent_comment_id = @{i:parentCommentId} OR (@{i:parentCommentId} IS NULL AND c.parent_comment_id IS NULL))
    ORDER BY
        coalesce(cv.total_votes, 0) DESC", [
        "productId" => $productId,
        "parentCommentId" => $parent,
        "userId" => $req->getSession()->id,
    ]);
    $stmt->execute();
    $result = $stmt->get_result();

    $comments = array();

    while ($row = $result->fetch_object()) {
        $comment = new \stdClass();
        if ($row->totalVotes <= -20) {
            $comment->data = new \stdClass();
            $comment->data->disapproved = true;
        } else {
            $comment->data = $row;
            $comment->data->disapproved = false;
            $comment->children = getComments($req, $productId, $row->id);
        }
        array_push($comments, $comment);
    }

    return $comments;
}

$allComments = getComments($req, $params['id']);

$resObj = new \stdClass();
$resObj->comments = $allComments;

$req->success($resObj);
