<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$vendorId = isset($_GET["vendorId"]) ? intval($_GET["vendorId"]) : 0;

if ($vendorId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสผู้ผลิต");
}

try {
    $db = new DbSqlSrv();

    $row = $db->fetch_one(
        "SELECT "
            . "[VID] AS VendorId, "
            . "[VCode] AS VendorCode, "
            . "[VName] AS VendorName, "
            . "CONVERT(varchar(19), [CreatedAt], 120) AS CreatedAtText "
        . "FROM [dbo].[Insp_Vendors] "
        . "WHERE [VID] = ?",
        array($vendorId)
    );

    if (!$row) {
        jsonResponseBadRequest("ไม่พบข้อมูลผู้ผลิต");
    }

    jsonResponseOk($row);
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
