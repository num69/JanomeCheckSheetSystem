<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$vendorCode = isset($_POST["vendorCode"]) ? trim($_POST["vendorCode"]) : "";
$vendorName = isset($_POST["vendorName"]) ? trim($_POST["vendorName"]) : "";

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

    $exists = $db->fetch_one(
        "SELECT TOP 1 [VID] "
        . "FROM [dbo].[Insp_Vendors] "
        . "WHERE [VCode] = ?",
        array($vendorCode)
    );

    if ($exists) {
        jsonResponseBadRequest("รหัสผู้ผลิตซ้ำกับข้อมูลเดิม");
    }

    $insertedId = $db->insert_id(
        "Insp_Vendors",
        array(
            "VCode" => $vendorCode,
            "VName" => $vendorName
        ),
        "VID"
    );

    jsonResponseOk(array(
        "vendorId" => intval($insertedId)
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
