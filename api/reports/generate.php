<?php

require_once '../utils/request.php';
require_once './_validReports.php';

$req->requireAdminPrivileges();
$req->requireMethod("POST");

$generators = $validReports;

$tables = array_map(function ($child) {
    return $child["table"];
}, $generators);

$columns = array();
foreach ($generators as $child) {
    $columns[$child["table"]] = array_map(function ($child) {
        return $child["column"];
    }, $child["columns"]);
}

$body = $req->getJsonBody([
    "csv" => [
        "type" => "boolean",
        "optional" => true,
        "default" => false,
    ],
    "table" => [
        "type" => "string",
        "in" => $tables
    ],
    "fields" => [
        "type" => "array",
        "validation" => function ($arr, $name, $schema, $body) use ($columns) {
            foreach ($arr as $child) {
                if (!in_array($child, $columns[$body->table])) {
                    return "Expected values in fields to be one of " . join(", ", $columns[$body->table]);
                }
            }
        }
    ],
    "filters" => [
        "type" => "array",
        "validation" => function ($arr, $name, $schema, $body) use ($columns) {
            foreach ($arr as $child) {
                if ($error = validate($child, [
                    "column" => [
                        "type" => "object"
                    ],
                    "type" => [
                        "type" => "string",
                        "in" => ["=", "in", "<>"]
                    ],
                    "value" => [
                        "type" => "string",
                        "maxLength" => 512,
                    ],
                    "start" => [
                        "type" => "string",
                        "maxLength" => 512,
                    ],
                    "end" => [
                        "type" => "string",
                        "maxLength" => 512,
                    ],
                ])) {
                    return $error;
                }
            }
        }
    ],
    "orders" => [
        "type" => "array",
        "validation" => function ($arr, $name, $schema, $body) use ($columns) {
            foreach ($arr as $child) {
                if ($error = validate($child, [
                    "column" => [
                        "type" => "object"
                    ],
                    "type" => [
                        "type" => "string",
                        "in" => ["asc", "desc"]
                    ],
                ])) {
                    return $error;
                }
            }
        }
    ],
]);

$view = $baseQueries[$body->table];

$params = array();
$currVarI = 0;

$query = "SELECT ";
$query .= count($body->fields) == 0 ? '*' : join(", ", $body->fields);
$query .= " FROM ";
$query .= "(" . $view . ") view";

$filters = array();
foreach ($body->filters as $filter) {
    if ($filter->type == "=") {
        array_push($filters, $filter->column->column . " = @{s:var" . $currVarI . "}");
        $params["var" . $currVarI] = $filter->value;
        $currVarI += 1;
    } elseif ($filter->type == "in") {
        array_push($filters, $filter->column->column . " LIKE CONCAT('%', @{s:var" . $currVarI . "}, '%')");
        $params["var" . $currVarI] = $filter->value;
        $currVarI += 1;
    } elseif ($filter->type == "<>") {
        if ($filter->start != '') {
            array_push($filters, $filter->column->column . " >= @{i:var" . $currVarI . "}");
            $params["var" . $currVarI] = $filter->start;
            $currVarI += 1;
        }
        if ($filter->end != '') {
            array_push($filters, $filter->column->column . " <= @{i:var" . $currVarI . "}");
            $params["var" . $currVarI] = $filter->end;
            $currVarI += 1;
        }
    }
}

if (count($filters) > 0) {
    $query .= " WHERE ";
    $query .= join(", ", $filters);
}

$orders = array();
foreach ($body->orders as $order) {
    array_push($orders, $order->column->column . " " . $order->type);
}

if (count($orders) > 0) {
    $query .= " ORDER BY ";
    $query .= join(", ", $orders);
}

if (!$body->csv) {
    $query .= " LIMIT 50";
}

$stmt = $req->prepareQuery($query, $params);
$stmt->execute();
$result = $stmt->get_result();

if ($body->csv) {
    // From: https://stackoverflow.com/questions/16352591/convert-php-array-to-csv-string
    function array2csv($data, $delimiter = ',', $enclosure = '"', $escape_char = "\\")
    {
        $f = fopen('php://memory', 'r+');
        foreach ($data as $item) {
            fputcsv($f, $item, $delimiter, $enclosure, $escape_char);
        }
        rewind($f);
        return stream_get_contents($f);
    }
    $req->contentType("text/csv");
    $report = $result->fetch_all(MYSQLI_NUM);
    array_unshift($report, $body->fields);
    $req->success(array2csv($report));
} else {
    $obj = new \stdClass();
    $obj->report = $result->fetch_all(MYSQLI_ASSOC);
    $req->success($obj);
}
