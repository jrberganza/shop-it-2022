<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$params = $req->getParams([
    "id" => [],
]);

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
    c.comment_id = @{i:commentId}", [
    "commentId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$comment = $result->fetch_object();

$req->success($comment);
