<?php

require 'utils/request.php';

$req->useDb();

$feeds = array();

$productFeed = new \stdClass();
$productFeed->name = 'Recent';
$productFeed->type = 'product';
$productFeed->content = array();
for ($currId = 0; $currId < 5; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . random_int(1, 100);
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    $product->rating = random_int(0, 10) / 2.0;
    $product->photos = [];
    array_push($productFeed->content, $product);
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
    disabled = FALSE
ORDER BY rating DESC
LIMIT 5", []);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_object()) {
    $stmt2 = $req->prepareQuery("SELECT photo_id FROM shop_photo JOIN photos USING (photo_id) WHERE shop_id = @{i:shopId}", [
        "shopId" => $row->id,
    ]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $row->photos = array();
    while ($row2 = $result2->fetch_array()) {
        array_push($row->photos, $row2["shop_photo_id"]);
    }

    array_push($shopFeed->content, $row);
}
array_push($feeds, $shopFeed);

$trendingFeed = new \stdClass();
$trendingFeed->name = 'Trending products';
$trendingFeed->type = 'product';
$trendingFeed->content = array();
for ($currId = 0; $currId < 5; $currId++) {
    $product = new \stdClass();

    $product->id = $currId;
    $product->name = "Producto " . $currId;
    $product->shopName = "Tienda " . random_int(1, 100);
    $product->price =  random_int(0, 9999) / 100.0;
    $product->shortDesc = "Product description " . $currId;
    $product->rating = random_int(0, 10) / 2.0;
    $product->photos = [];
    array_push($trendingFeed->content, $product);
}
array_push($feeds, $trendingFeed);

$resObj = new \stdClass();
$resObj->feeds = $feeds;

$req->success($resObj);
