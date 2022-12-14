<?php

require_once '../../utils/request.php';

$params = $req->getParams([
    "id" => [],
]);

function getComments(Request $req, int $shopId, ?int $parent = null)
{
    $stmt = $req->prepareQuery("SELECT
        c.comment_id as id,
        u.display_name as author,
        c.created_at as publishedAt,
        c.content as content,
        0 as totalVotes,
        0 as voted,
        false as moderated
    FROM
        \$moderation\$comments c
    JOIN
        users u ON c.author_id = u.user_id
    WHERE
        c.shop_id = @{i:shopId} AND
        (c.parent_comment_id = @{i:parentCommentId} OR (@{i:parentCommentId} IS NULL AND c.parent_comment_id IS NULL)) AND
        c.author_id = @{i:userId}
    UNION
    SELECT
        c.comment_id as id,
        u.display_name as author,
        c.created_at as publishedAt,
        c.content as content,
        coalesce(cv.total_votes, 0) as totalVotes,
        CAST(coalesce(uv.value, 0) as signed) as voted,
        true as moderated
    FROM
        comments c
    JOIN
        users u ON c.author_id = u.user_id
    LEFT JOIN
        (SELECT sum(value) as total_votes, comment_id FROM comment_votes GROUP BY comment_id) cv USING (comment_id)
    LEFT JOIN
        (SELECT value, comment_id FROM comment_votes WHERE user_id = @{i:userId}) uv USING (comment_id)
    WHERE
        c.shop_id = @{i:shopId} AND
        (c.parent_comment_id = @{i:parentCommentId} OR (@{i:parentCommentId} IS NULL AND c.parent_comment_id IS NULL))
    ORDER BY
        moderated ASC, totalVotes DESC, publishedAt DESC", [
        "shopId" => $shopId,
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
            $comment->children = getComments($req, $shopId, $row->id);
        }
        array_push($comments, $comment);
    }

    return $comments;
}


$allComments = getComments($req, $params["id"]);

$resObj = new \stdClass();
$resObj->comments = $allComments;

$req->success($resObj);
