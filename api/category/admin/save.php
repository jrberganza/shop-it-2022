<?php

require '../../utils/request.php';

$req->requireMethod("POST");

$req->requireAdminPrivileges();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer",
        "optional" => true,
    ],
    "type" => [
        "type" => "string",
        "in" => ["shop", "product"]
    ],
    "name" => [
        "type" => "string",
        "maxLength" => 50,
    ],
    "disabled" => [
        "type" => "boolean",
    ],
]);

if (isset($jsonBody->id)) {
    $stmt = $req->prepareQuery("UPDATE
        categories
    SET
        type = @{s:type},
        name = @{s:name},
        disabled = @{i:disabled}
    WHERE
        category_id = @{i:categoryId}", [
        "type" => $jsonBody->type,
        "name" => $jsonBody->name,
        "disabled" => $jsonBody->disabled,
        "categoryId" => $jsonBody->id,
    ]);
    $stmt->execute();

    $resObj = new \stdClass();
    $resObj->id = $jsonBody->id;

    $req->success($resObj);
} else {
    $stmt = $req->prepareQuery("INSERT INTO categories(
        type,
        name,
        disabled
    ) VALUES (
        @{s:type},
        @{s:name},
        @{i:disabled}
    )", [
        "type" => $jsonBody->type,
        "name" => $jsonBody->name,
        "disabled" => $jsonBody->disabled
    ]);
    $stmt->execute();
    $categoryId = $stmt->insert_id;

    $resObj = new \stdClass();
    $resObj->id = $categoryId;

    $req->success($resObj);
}
