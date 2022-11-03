<?php

require_once '../utils/request.php';

$stmt = $req->prepareQuery("SELECT
    hb.type as blockType,
    hb.size as size,
    fb.feed_block_id as feedBlockId,
    fb.title as feedTitle,
    fb.type as feedType,
    fb.item_type as feedItemType,
    fb.max_size as feedMaxSize,
    bb.title as bannerTitle,
    bb.text as bannerText,
    bb.photo_id as bannerPhotoId
FROM
    homepage_blocks hb
LEFT JOIN
    feed_blocks fb USING (block_id)
LEFT JOIN
    banner_blocks bb USING (block_id)
ORDER BY
    hb.position", []);
$stmt->execute();
$result = $stmt->get_result();

$blocks = array();
while ($block = $result->fetch_object()) {
    if ($block->blockType == "feed") {
        $block->feedContent = array();
        if ($block->feedItemType == "shop") {
            if ($block->feedType == "auto_top_rated") {
                $stmt2 = $req->prepareQuery("SELECT * FROM top_rated_shops LIMIT 5", []);
            } elseif ($block->feedType == "auto_trending") {
                $stmt2 = $req->prepareQuery("SELECT * FROM trending_shops LIMIT 5", []);
            } elseif ($block->feedType == "auto_recent") {
                $stmt2 = $req->prepareQuery("SELECT * FROM recent_shops LIMIT 5", []);
            } elseif ($block->feedType == "manual") {
                $stmt2 = $req->prepareQuery("SELECT
                    s.shop_id as id,
                    s.name as name,
                    s.zone as zone,
                    mn.name as municipality,
                    dp.name as department,
                    s.phone_number as phoneNumber,
                    s.description as description,
                    s.disabled as disabled,
                    COALESCE(r.average_rating, 0) as average_rating
                FROM
                    feed_block_items fbi
                JOIN
                    shops s USING (shop_id)
                JOIN
                    municipalities mn USING (municipality_id)
                JOIN
                    departments dp USING (department_id)
                LEFT JOIN
                    (SELECT avg(rating) as average_rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
                WHERE
                    fbi.feed_block_id = @{i:feedBlockId}
                LIMIT 5", [
                    "feedBlockId" => $block->feedBlockId,
                ]);
            }
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            while ($shop = $result2->fetch_object()) {
                $stmt3 = $req->prepareQuery("SELECT p.photo_id FROM shop_photo sp JOIN photos p USING (photo_id) WHERE sp.shop_id = @{i:shopId}", [
                    "shopId" => $shop->id,
                ]);
                $stmt3->execute();
                $result3 = $stmt3->get_result();

                $shop->photos = array();
                while ($photo = $result3->fetch_array()) {
                    array_push($shop->photos, $photo["photo_id"]);
                }

                array_push($block->feedContent, $shop);
            }
        } elseif ($block->feedItemType == "product") {
            if ($block->feedType == "auto_top_rated") {
                $stmt2 = $req->prepareQuery("SELECT * FROM top_rated_products LIMIT 5", []);
            } elseif ($block->feedType == "auto_trending") {
                $stmt2 = $req->prepareQuery("SELECT * FROM trending_products LIMIT 5", []);
            } elseif ($block->feedType == "auto_recent") {
                $stmt2 = $req->prepareQuery("SELECT * FROM recent_products LIMIT 5", []);
            } elseif ($block->feedType == "manual") {
                $stmt2 = $req->prepareQuery("SELECT
                    p.product_id as id,
                    p.name as name,
                    p.price as price,
                    p.description as description,
                    p.disabled as disabled,
                    s.name as shopName,
                    COALESCE(r.average_rating, 0) as average_rating
                FROM
                    feed_block_items fbi
                JOIN
                    products p USING (product_id)
                JOIN
                    shops s USING (shop_id)
                LEFT JOIN
                    (SELECT avg(rating) as average_rating, product_id FROM product_ratings GROUP BY product_id) r USING (product_id)
                WHERE
                    fbi.feed_block_id = @{i:feedBlockId}
                LIMIT 5", [
                    "feedBlockId" => $block->feedBlockId,
                ]);
            }
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            while ($product = $result2->fetch_object()) {
                $stmt3 = $req->prepareQuery("SELECT p.photo_id FROM product_photo pp JOIN photos p USING (photo_id) WHERE pp.product_id = @{i:productId}", [
                    "productId" => $product->id,
                ]);
                $stmt3->execute();
                $result3 = $stmt3->get_result();

                $product->photos = array();
                while ($photo = $result3->fetch_array()) {
                    array_push($product->photos, $photo["photo_id"]);
                }

                array_push($block->feedContent, $product);
            }
        }
    }

    array_push($blocks, $block);
}

$resObj = new \stdClass();
$resObj->blocks = $blocks;

$req->success($resObj);
