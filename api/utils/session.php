<?php

class Session
{
    public int $id;
    public string $displayName;
    public string $role;
    public string $token;
    public string $lastAccessAt;

    public function isLoggedIn()
    {
        return $this->role != 'visitor';
    }

    public function canManageContent()
    {
        return $this->role == 'employee' || $this->role == 'admin';
    }

    public function canManageSite()
    {
        return $this->role == 'admin';
    }
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
function getCurrentSession(DbWrapper $db)
{
    $session = new Session();
    $session->role = "visitor";

    if (!hasSessionToken()) {
        return $session;
    }

    $token = getSessionToken();

    $stmt = $db->prepareQuery("SELECT
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
        s.token = @{s:token};", [
        "token" => $token
    ]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();

    if (!$row) {
        return $session;
    }

    $lastAccessAt = strtotime($row["last_access_at"]);
    if (time() - $lastAccessAt > 86400 * 7) {
        return $session;
    }

    $session->id = $row["user_id"];
    $session->displayName = $row["display_name"];
    $session->role = $row["role"];
    $session->token = $row["token"];
    $session->lastAccessAt = $row["last_access_at"];

    return $session;
}
