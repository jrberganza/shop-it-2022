<?php

require 'utils/request.php';

$req->useDb();

$feeds = array();

$productFeed = new \stdClass();
$productFeed->name = 'Recent';
$productFeed->type = 'product';
$productFeed->content = array();

$stmt = $req->prepareQuery("SELECT
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
    p.disabled = FALSE
ORDER BY rating DESC
LIMIT 5", []);
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

    array_push($productFeed->content, $row);
}
array_push($feeds, $productFeed);

$shopFeed = new \stdClass();
$shopFeed->name = 'Top rated shops';
$shopFeed->type = 'shop';
$shopFeed->content = array();

$stmt = $req->prepareQuery("SELECT
    s.shop_id as id,
    s.name as name,
    s.address as address,
    s.phone_number as phoneNumber,
    s.description as shortDesc,
    s.disabled as disabled,
    cast(coalesce(r.rating, 0.0) as double) as rating
FROM
    shops s
LEFT JOIN
    (SELECT avg(rating) as rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
WHERE
    s.disabled = FALSE
ORDER BY rating DESC
LIMIT 5", []);
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

    array_push($shopFeed->content, $row);
}
array_push($feeds, $shopFeed);

$trendingFeed = new \stdClass();
$trendingFeed->name = 'Trending products';
$trendingFeed->type = 'product';
$trendingFeed->content = array();

$stmt = $req->prepareQuery("SELECT
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
    p.disabled = FALSE
ORDER BY rating DESC
LIMIT 5", []);
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

    array_push($trendingFeed->content, $row);
}
array_push($feeds, $trendingFeed);

$resObj = new \stdClass();
$resObj->feeds = $feeds;

$req->success($resObj);
