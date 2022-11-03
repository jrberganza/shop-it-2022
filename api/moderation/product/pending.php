<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$stmt = $req->prepareQuery("SELECT
    product_id as id,
    name as name,
    price as price,
    description as description,
    disabled as disabled
FROM \$moderation\$products", []);
$stmt->execute();
$result = $stmt->get_result();

$allProducts = $result->fetch_all(MYSQLI_ASSOC);

foreach ($allProducts as &$product) {
    $stmt2 = $req->prepareQuery("SELECT p.photo_id FROM \$moderation\$product_photo pp JOIN photos p USING (photo_id) WHERE pp.product_id = @{i:productId}", [
        "productId" => $product["id"],
    ]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $product["photos"] = [];
    while ($row = $result2->fetch_array()) {
        array_push($product["photos"], $row["photo_id"]);
    }
}

$resObj = new \stdClass();
$resObj->pending = $allProducts;

$req->success($resObj);
