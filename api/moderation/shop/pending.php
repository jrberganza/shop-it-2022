<?php

require_once '../../utils/request.php';

$req->requireEmployeePrivileges();

$stmt = $req->prepareQuery("SELECT
    s.shop_id as id,
    s.name as name,
    s.zone as zone,
    mn.municipality_id as municipality,
    dp.department_id as department,
    s.latitude as latitude,
    s.longitude as longitude,
    s.phone_number as phoneNumber,
    s.description as description,
    s.disabled as disabled
FROM
    \$moderation\$shops s
JOIN
    municipalities mn USING (municipality_id)
JOIN
    departments dp USING (department_id)", []);
$stmt->execute();
$result = $stmt->get_result();

$allShops = $result->fetch_all(MYSQLI_ASSOC);

foreach ($allShops as &$shop) {
    $stmt2 = $req->prepareQuery("SELECT p.photo_id FROM \$moderation\$shop_photo sp JOIN photos p USING (photo_id) WHERE sp.shop_id = @{i:shopId}", [
        "shopId" => $shop["id"],
    ]);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    $shop["photos"] = [];
    while ($row = $result2->fetch_array()) {
        array_push($shop["photos"], $row["photo_id"]);
    }
}

$resObj = new \stdClass();
$resObj->pending = $allShops;

$req->success($resObj);
