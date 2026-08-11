<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$vendorId = isset($_POST["vendorId"]) ? intval($_POST["vendorId"]) : 0;
$vendorCode = isset($_POST["vendorCode"]) ? trim($_POST["vendorCode"]) : "";
$vendorName = isset($_POST["vendorName"]) ? trim($_POST["vendorName"]) : "";

if ($vendorId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสผู้ผลิต");
}

if ($vendorCode === "") {
    jsonResponseBadRequest("กรุณาระบุรหัสผู้ผลิต");
}

if ($vendorName === "") {
    jsonResponseBadRequest("กรุณาระบุชื่อผู้ผลิต");
}

if (mb_strlen($vendorCode, "UTF-8") > 20) {
    jsonResponseBadRequest("รหัสผู้ผลิตยาวเกิน 20 ตัวอักษร");
}

if (mb_strlen($vendorName, "UTF-8") > 255) {
    jsonResponseBadRequest("ชื่อผู้ผลิตยาวเกิน 255 ตัวอักษร");
}

try {
    $db = new DbSqlSrv();

    $current = $db->fetch_one(
        "SELECT TOP 1 [VID] "
        . "FROM [dbo].[Insp_Vendors] "
        . "WHERE [VID] = ?",
        array($vendorId)
    );

    if (!$current) {
        jsonResponseBadRequest("ไม่พบข้อมูลผู้ผลิต");
    }

    $exists = $db->fetch_one(
        "SELECT TOP 1 [VID] "
        . "FROM [dbo].[Insp_Vendors] "
        . "WHERE [VCode] = ? AND [VID] <> ?",
        array($vendorCode, $vendorId)
    );

    if ($exists) {
        jsonResponseBadRequest("รหัสผู้ผลิตซ้ำกับข้อมูลเดิม");
    }

    $affectedRows = $db->update(
        "Insp_Vendors",
        array(
            "VCode" => $vendorCode,
            "VName" => $vendorName
        ),
        "[VID] = ?",
        array($vendorId)
    );

    jsonResponseOk(array(
        "updatedRows" => intval($affectedRows)
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
