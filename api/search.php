<?php

require 'utils/request.php';

$searchQuery = isset($_GET["q"]) ? $_GET["q"] : null;
$productCategories = isset($_GET["productCategories"]) ? (strlen($_GET["productCategories"]) == 0 ? [] : explode(",", $_GET["productCategories"])) : [];
$shopCategories = isset($_GET["shopCategories"]) ? (strlen($_GET["shopCategories"]) == 0 ? [] : explode(",", $_GET["shopCategories"])) : [];

$searchResults = new \stdClass;

$searchResults->products = array();

$query = "SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled,
    cast(coalesce(r.rating, 0.0) as double) as rating,
    s.shop_id as shopId,
    s.name as shopName
FROM
    products p
JOIN
    shops s USING (shop_id)
LEFT JOIN
    (SELECT avg(rating) as rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
WHERE
    p.disabled = FALSE";

$conditions = array();
$params = array();

if ($_GET["q"] != null) {
    array_push($conditions, "(
        p.name LIKE CONCAT('%', @{s:searchQuery}, '%') OR
        p.description LIKE CONCAT('%', @{s:searchQuery}, '%') OR
        s.name LIKE CONCAT('%', @{s:searchQuery}, '%')
    )");
    $params["searchQuery"] = $searchQuery;
}

$prodCategoryConditions = array();

foreach ($productCategories as $i => $category) {
    array_push($prodCategoryConditions, "SELECT product_id FROM product_category WHERE category_id = @{s:category" . $i . "}");
    $params["category" . $i] = $category;
}

$shopCategoryConditions = array();

foreach ($shopCategories as $i => $category) {
    $j = count($prodCategoryConditions) + $i;
    array_push($shopCategoryConditions, "SELECT shop_id FROM shop_category WHERE category_id = @{s:category" . $j . "}");
    $params["category" . $j] = $category;
}

if (count($conditions) > 0) {
    $query .= " AND ";
    $query .= join(" AND ", $conditions);
}

if (count($prodCategoryConditions) > 0) {
    $query .= " AND p.product_id IN (";
    $query .= join(" INTERSECT ", $prodCategoryConditions);
    $query .= ")";
}

if (count($shopCategoryConditions) > 0) {
    $query .= " AND s.shop_id IN (";
    $query .= join(" INTERSECT ", $shopCategoryConditions);
    $query .= ")";
}

$stmt = $req->prepareQuery($query, $params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_object()) {
    $stmt2 = $req->prepareQuery("SELECT p.photo_id FROM product_photo pp JOIN photos p USING (photo_id) WHERE pp.product_id = @{i:productId}", [
        "productId" => $row->id,
    ]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $row->photos = array();
    while ($row2 = $result2->fetch_array()) {
        array_push($row->photos, $row2["photo_id"]);
    }

    array_push($searchResults->products, $row);
}

$searchResults->shops = array();

$query = "SELECT
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
    s.disabled = FALSE";

$conditions = array();
$params = array();

if ($_GET["q"] != null) {
    array_push($conditions, "(
        s.name LIKE CONCAT('%', @{s:searchQuery}, '%') OR
        s.description LIKE CONCAT('%', @{s:searchQuery}, '%')
    )");
    $params["searchQuery"] = $searchQuery;
}

$shopCategoryConditions = array();

foreach ($shopCategories as $i => $category) {
    array_push($shopCategoryConditions, "SELECT shop_id FROM shop_category WHERE category_id = @{s:category" . $i . "}");
    $params["category" . $i] = $category;
}

if (count($conditions) > 0) {
    $query .= " AND ";
    $query .= join(" AND ", $conditions);
}

if (count($shopCategoryConditions) > 0) {
    $query .= " AND s.shop_id IN (";
    $query .= join(" INTERSECT ", $shopCategoryConditions);
    $query .= ")";
}

$stmt = $req->prepareQuery($query, $params);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_object()) {
    $stmt2 = $req->prepareQuery("SELECT p.photo_id FROM shop_photo sp JOIN photos p USING (photo_id) WHERE sp.shop_id = @{i:shopId}", [
        "shopId" => $row->id,
    ]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $row->photos = array();
    while ($row2 = $result2->fetch_array()) {
        array_push($row->photos, $row2["photo_id"]);
    }

    array_push($searchResults->shops, $row);
}

$req->success($searchResults);
