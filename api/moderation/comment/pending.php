<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

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
    users u ON c.author_id = u.user_id", []);
$stmt->execute();
$result = $stmt->get_result();

$resObj = new \stdClass();
$resObj->pending = $result->fetch_all(MYSQLI_ASSOC);

$req->success($resObj);
