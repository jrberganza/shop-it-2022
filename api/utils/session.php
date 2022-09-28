<?php

class Session
{
    public int $id;
    public string $displayName;
    public string $role;
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
