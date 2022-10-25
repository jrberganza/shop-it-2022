<?php

require "strict.php";
require "db.php";
require "session.php";
require "validation.php";


// TODO: handle GET and POST payloads better
class Request
{
    public string $mimeType = 'application/json';
    private ?DbWrapper $db = null;
    private ?Session $session = null;

    public function useDb()
    {
        if (!$this->db) {
            $this->db = new DbWrapper();
            $this->db->connectDb();
            $this->db->startTransaction();
        }
    }

    public function getDbInstance()
    {
        $this->useDb();
        return $this->db;
    }

    public function useSession()
    {
        if (!$this->session) {
            $this->useDb();

            $this->session = getCurrentSession($this->db);
        }
    }

    public function getSession()
    {
        $this->useSession();
        return $this->session;
    }

    public function requireLoggedIn()
    {
        $this->useSession();

        if (!$this->session->isLoggedIn()) {
            $this->fail("Not logged in", 401);
        }
    }

    public function requireEmployeePrivileges()
    {
        $this->useSession();

        if (!$this->session->hasEmployeePrivileges()) {
            $this->fail("Not authorized", 403);
        }
    }

    public function requireAdminPrivileges()
    {
        $this->useSession();

        if (!$this->session->hasAdminPrivileges()) {
            $this->fail("Not authorized", 403);
        }
    }

    public function contentType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    public function getBody()
    {
        $body = file_get_contents("php://input");
        if (!$body) {
            $this->fail("Malformed request body", 400);
        }
        return $body;
    }

    public function getJsonBody(array $expected)
    {
        $body = $this->getBody();
        $json = json_decode($body);

        if (!$json) {
            $this->fail("Malformed request body", 400);
        }

        if ($error = validateObj($json, $expected)) {
            $this->fail($error, 400);
        }

        return $json;
    }

    public function prepareQuery($query, $params): mysqli_stmt
    {
        $this->useDb();

        return $this->db->prepareQuery($query, $params);
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
        } elseif ($this->mimeType == 'text/xml') {
            $xml = new DOMDocument("1.0");

            $errorEl = $xml->createElement("error", $response);
            $xml->appendChild($errorEl);

            echo "" . $xml->saveXML() . "";
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
