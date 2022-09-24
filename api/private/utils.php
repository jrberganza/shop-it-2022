<?php

function resSuccess($obj)
{
    $obj->success = true;

    $JSON = json_encode($obj);

    echo $JSON;

    exit();
}

function resFail($message, $responseCode = 400)
{
    http_response_code($responseCode);

    $errorObj = new \stdClass();
    $errorObj->success = false;
    $errorObj->_error = $message;

    $JSON = json_encode($errorObj);

    echo $JSON;

    exit();
}

function generateSessionToken()
{
    return sprintf('%016x', time()) . bin2hex(random_bytes(56));
}

function hasSessionToken()
{
    return isset($_COOKIE["session_token"]);
}

function getSessionToken()
{
    if (hasSessionToken()) {
        return $_COOKIE["session_token"];
    }
}

function getCurrentSession($db)
{
    if (!hasSessionToken()) {
        return false;
    }

    $token = getSessionToken();

    $stmt = $db->prepare("SELECT
        u.user_id,
        u.email,
        u.display_name,
        u.role,
        s.token,
        s.last_access_at
    FROM
        users u
    JOIN
        sessions s USING (user_id)
    WHERE
        s.token = ?;");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();

    if (!$row) {
        return false;
    }

    $lastTokenAccess = strtotime($row["last_access_at"]);
    if (time() - $lastTokenAccess > 86400 * 7) {
        return false;
    }

    $user = new \stdClass();

    $user->id = $row["user_id"];
    $user->displayName = $row["display_name"];
    $user->role = $row["role"];

    return $user;
}

set_error_handler(function () {
    resFail("Internal error", 500);
});

set_exception_handler(function () {
    resFail("Internal error", 500);
});
