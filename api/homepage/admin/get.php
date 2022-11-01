<?php

require '../../utils/request.php';

$req->requireAdminPrivileges();

$stmt = $req->prepareQuery("SELECT
    hb.type as blockType,
    hb.size as size,
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
            $stmt2 = $req->prepareQuery("SELECT
                s.shop_id as id,
                s.name as name,
                ad.zone as zone,
                mn.name as municipality,
                dp.name as department,
                s.phone_number as phoneNumber,
                s.description as description,
                s.disabled as disabled,
                cast(coalesce(r.rating, 0.0) as double) as rating
            FROM
                shops s
            JOIN
                addresses ad
            JOIN
                municipalities mn USING (municipality_id)
            JOIN
                departments dp USING (department_id)
            LEFT JOIN
                (SELECT avg(rating) as rating, shop_id FROM shop_ratings GROUP BY shop_id) r USING (shop_id)
            WHERE
                s.disabled = FALSE
            ORDER BY rating DESC
            LIMIT 5", []);
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
            $stmt2 = $req->prepareQuery("SELECT
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
