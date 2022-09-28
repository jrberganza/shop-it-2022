<?php

require "query.php";

class DbWrapper
{
    public ?mysqli $db = null;

    public function connectDb()
    {
        $this->db = new mysqli("localhost", "shopit", "shopit_1234", "shopit_db");
    }

    public function startTransaction()
    {
        if (!$this->db) $this->connectDb();
        $this->db->begin_transaction();
    }

    public function prepareQuery($query, $params): mysqli_stmt
    {
        if (!$this->db) $this->connectDb();

        $parsed = parseSql($query, $params);

        $stmt = $this->db->prepare($parsed->query);

        $args = array($parsed->types);
        foreach ($parsed->params as $i => $param) {
            $args[$i + 1] = &$parsed->params[$i];
        }
        call_user_func_array(array($stmt, 'bind_param'), $args);

        return $stmt;
    }

    public function commit()
    {
        if (!$this->db) $this->connectDb();
        $this->db->commit();
    }

    public function rollback()
    {
        if (!$this->db) $this->connectDb();
        $this->db->rollback();
    }
}
