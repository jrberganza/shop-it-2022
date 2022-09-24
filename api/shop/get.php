<?php

require '../private/strict.php';
require '../private/db.php';
require "../private/utils.php";

header('Content-type: application/json');

if (!isset($_GET["id"])) {
    resFail("No shop specified");
}
$shopId = $_GET["id"];

$stmt = $db->prepare("SELECT
    s.shop_id as id,
    s.name as name,
    s.address as address,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled,
    cast(coalesce(r.rating, 0.0) as double) as rating
FROM
    shops s
LEFT JOIN
    (SELECT avg(rating) as rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
WHERE
    shop_id = ?");
$stmt->bind_param("i", $shopId);
$stmt->execute();
$result = $stmt->get_result();

$resObj = $result->fetch_object();

if ($resObj) {
    resSuccess($resObj);
} else {
    resFail("No shop found");
}
