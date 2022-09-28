<?php

$root = realpath($_SERVER['DOCUMENT_ROOT']);

require "$root/api/utils/strict.php";
require "$root/api/utils/private/db.php";
require "$root/api/utils/session.php";

class ParsedQuery
{
    public string $query = "";
    public string $types = "";
    public array $params = [];
}

function parseSql(string $query, array $params)
{
    $parsed = new ParsedQuery();

    $currI = 0;
    $prevParamEnd = -1;
    $nextIndex = 0;
    while ($nextIndex = strpos($query, '@{', $nextIndex + 1)) {
        $paramEnd = strpos($query, '}', $nextIndex);
        $typeSep = strpos($query, ':', $nextIndex);

        $queryPart = substr($query, $prevParamEnd + 1, $nextIndex - ($prevParamEnd + 1));
        $type = substr($query, $nextIndex + 2, $typeSep - ($nextIndex + 2));
        $paramName = substr($query, $typeSep + 1, $paramEnd - ($typeSep + 1));

        $parsed->query .= $queryPart . '?';
        $parsed->types .= $type;
        $parsed->params[$currI] = &$params[$paramName];

        $currI++;
        $prevParamEnd = $paramEnd;
    }

    $queryPart = substr($query, $prevParamEnd + 1, strlen($query) - ($prevParamEnd + 1));
    $parsed->query .= $queryPart;

    return $parsed;
}

class Request
{
    public ?mysqli $db = null;
    public string $mimeType = 'application/json';
    public ?Session $session = null;

    public function useDb()
    {
        $this->db = new mysqli("localhost", "shopit", "shopit_1234", "shopit_db");
        $this->db->begin_transaction();
    }

    public function useSession()
    {
        if (!$this->db) {
            throw new Error("You need to call Request::useDb()");
        }

        if (!hasSessionToken()) {
            return false;
        }

        $token = getSessionToken();

        $stmt = $this->prepareQuery("SELECT
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
            return;
        }

        $lastTokenAccess = strtotime($row["last_access_at"]);
        if (time() - $lastTokenAccess > 86400 * 7) {
            return;
        }

        $this->session = new Session();

        $this->session->id = $row["user_id"];
        $this->session->displayName = $row["display_name"];
        $this->session->role = $row["role"];
    }

    public function contentType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getBody()
    {
        return file_get_contents("php://input");
    }

    public function getJsonBody(array $expected)
    {
        $body = $this->getBody();
        $json = json_decode($body);

        foreach ($expected as $name => $what) {
            if (gettype($json->$name) != $what["type"]) {
                $this->fail("Expected " . $name . " to be a " . $what["type"], 400);
            }

            if (isset($what["maxLength"]) && strlen($json->$name) > $what["maxLength"]) {
                $this->fail("Expected " . $name . " to be at most " . $what["maxLength"] . " characters", 400);
            }

            if (isset($what["minLength"]) && strlen($json->$name) < $what["minLength"]) {
                $this->fail("Expected " . $name . " to be at least " . $what["minLength"] . " characters", 400);
            }
        }

        return $json;
    }

    public function prepareQuery($query, $params): mysqli_stmt
    {
        if (!$this->db) {
            throw new Error("You need to call Request::useDb()");
        }

        $parsed = parseSql($query, $params);

        $stmt = $this->db->prepare($parsed->query);
        $args = array($parsed->types);
        foreach ($parsed->params as $i => $param) {
            $args[$i + 1] = &$parsed->params[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $args);
        return $stmt;
    }

    public function success($response, $responseCode = 200)
    {
        header('Content-type: ' . $this->mimeType);
        http_response_code($responseCode);

        if ($this->mimeType == 'application/json') {
            $response->success = true;

            $JSON = json_encode($response);

            echo $JSON;
        } else {
            echo $response;
        }

        if ($this->db) {
            $this->db->commit();
        }

        exit();
    }

    function fail($response, $responseCode = 400)
    {

        header('Content-type: ' . $this->mimeType);
        http_response_code($responseCode);

        if ($this->mimeType == 'application/json') {
            $resObj = new \stdClass();
            $resObj->success = false;
            $resObj->_error = $response;

            $JSON = json_encode($resObj);

            echo $JSON;
        } else {
            echo $response;
        }

        if ($this->db) {
            $this->db->rollback();
        }

        exit();
    }
}

$req = new Request();

set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
    global $req;
    $req->fail('[' . $errfile . ':' . $errline . '] ' . $errstr, 500);
});

set_exception_handler(function (\Throwable $th) {
    global $req;
    $req->fail('[' . $th->getFile() . ':' . $th->getLine() . '] ' . $th->getMessage(), 500);
});
