<?php

require_once '../../utils/request.php';

$req->requireAdminPrivileges();

$jsonBody = $req->getJsonBody([
    "blocks" => [
        "type" => "array"
    ],
]);

$stmt = $req->prepareQuery("DELETE FROM homepage_blocks", []);
$stmt->execute();

foreach ($jsonBody->blocks as $i => $block) {
    $stmt = $req->prepareQuery("INSERT INTO homepage_blocks(
        type,
        position,
        size
    ) VALUES (
        @{s:blockType},
        @{i:position},
        @{s:size}
    )", [
        "blockType" => $block->blockType,
        "position" => $i,
        "size" => $block->size,
    ]);
    $stmt->execute();
    $result = $stmt->get_result();
    $blockId = $stmt->insert_id;

    if ($block->blockType == "feed") {
        $stmt2 = $req->prepareQuery("INSERT INTO feed_blocks(
            block_id,
            title,
            type,
            item_type,
            max_size
        ) VALUES (
            @{i:blockId},
            @{s:feedTitle},
            @{s:feedType},
            @{s:feedItemType},
            @{i:feedMaxSize}
        )", [
            "blockId" => $blockId,
            "feedTitle" => $block->feedTitle,
            "feedType" => $block->feedType,
            "feedItemType" => $block->feedItemType,
            "feedMaxSize" => $block->feedMaxSize,
        ]);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
    } elseif ($block->blockType == "banner") {
        $stmt2 = $req->prepareQuery("INSERT INTO banner_blocks(
            block_id,
            title,
            text,
            photo_id
        ) VALUES (
            @{i:blockId},
            @{s:bannerTitle},
            @{s:bannerText},
            @{i:bannerPhotoId}
        )", [
            "blockId" => $blockId,
            "bannerTitle" => $block->bannerTitle,
            "bannerText" => $block->bannerText,
            "bannerPhotoId" => $block->bannerPhotoId
        ]);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
    }
}

$resObj = new \stdClass();
$req->success($resObj);
