<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$params = $req->getParams([
    "id" => [],
]);

$stmt = $req->prepareQuery("SELECT
    p.product_id as id,
    p.name as name,
    p.price as price,
    p.description as description,
    p.disabled as disabled
FROM \$moderation\$products p
WHERE
    p.product_id = @{i:productId}", [
    "productId" => $params["id"],
]);
$stmt->execute();
$result = $stmt->get_result();

$product = $result->fetch_object();

$stmt2 = $req->prepareQuery("SELECT p.photo_id FROM \$moderation\$product_photo pp JOIN photos p USING (photo_id) WHERE pp.product_id = @{i:productId}", [
    "productId" => $product->id,
]);
$stmt2->execute();
$result2 = $stmt2->get_result();

$product->photos = [];
while ($row = $result2->fetch_array()) {
    array_push($product->photos, $row["photo_id"]);
}

$req->success($product);
