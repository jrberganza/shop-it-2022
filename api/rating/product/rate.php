<?php

require_once '../../utils/request.php';

$req->requireMethod("POST");

$req->requireLoggedIn();

$jsonBody = $req->getJsonBody([
    "id" => [
        "type" => "integer",
    ],
    "rating" => [
        "type" => "integer",
        "min" => 0,
        "max" => 5,
    ],
]);

$stmt = $req->prepareQuery("INSERT INTO product_ratings(
    product_id,
    user_id,
    rating
) VALUES (
    @{i:productId},
    @{i:userId},
    @{i:rating}
) ON DUPLICATE KEY UPDATE
    rating = @{i:rating}", [
    "productId" => $jsonBody->id,
    "userId" => $req->getSession()->id,
    "rating" => $jsonBody->rating,
]);
$stmt->execute();

$resObj = new \stdClass();

$req->success($resObj);
