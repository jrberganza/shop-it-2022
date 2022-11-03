<?php

class Session
{
    public ?int $id = null;
    public ?string $displayName = null;
    public string $role;
    public ?string $token = null;
    public ?string $lastAccessAt = null;
    public ?int $shopId = null;

    public function isLoggedIn()
    {
        return $this->role != 'visitor';
    }

    public function hasEmployeePrivileges()
    {
        return $this->role == 'employee' || $this->role == 'admin';
    }

    public function hasAdminPrivileges()
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
        ss.token,
        unix_timestamp(ss.last_access_at) as last_access_at,
        s.shop_id
    FROM
        users u
    JOIN
        sessions ss USING (user_id)
    LEFT JOIN
        (SELECT * FROM \$moderation\$shops UNION SELECT * FROM shops) s USING (user_id)
    WHERE
        ss.token = @{s:token};", [
        "token" => $token
    ]);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_array();

    if (!$row) {
        return $session;
    }

    $lastAccessAt = $row["last_access_at"];
    if (time() - $lastAccessAt > 86400 * 7) {
        return $session;
    }

    $session->id = $row["user_id"];
    $session->displayName = $row["display_name"];
    $session->role = $row["role"];
    $session->token = $row["token"];
    $session->lastAccessAt = $row["last_access_at"];
    $session->shopId = $row["shop_id"];

    return $session;
}
