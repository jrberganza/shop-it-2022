<?php

require "strict.php";
require "db.php";
require "session.php";

class Request
{
    public ?DbWrapper $db = null;
    public string $mimeType = 'application/json';
    public ?Session $session = null;

    public function useDb()
    {
        $this->db = new DbWrapper();
        $this->db->connectDb();
        $this->db->startTransaction();
    }

    public function useSession()
    {
        if (!$this->db) {
            throw new Error("You need to call Request::useDb()");
        }

        $this->session = getCurrentSession($this->db);
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
