<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$vendorId = isset($_POST["vendorId"]) ? intval($_POST["vendorId"]) : 0;

if ($vendorId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสผู้ผลิต");
}

try {
    $db = new DbSqlSrv();

    $current = $db->fetch_one(
        "SELECT TOP 1 [VID] FROM [dbo].[Insp_Vendors] WHERE [VID] = ?",
        array($vendorId)
    );

    if (!$current) {
        jsonResponseBadRequest("ไม่พบข้อมูลผู้ผลิต");
    }

    $partExists = $db->fetch_one(
        "SELECT TOP 1 [PID] FROM [dbo].[insp_Parts] WHERE [VID] = ?",
        array($vendorId)
    );

    if ($partExists) {
        jsonResponseBadRequest("ไม่สามารถลบผู้ผลิตนี้ได้ เนื่องจากมีข้อมูลชิ้นส่วนที่อ้างอิงอยู่");
    }

    $affectedRows = $db->delete(
        "Insp_Vendors",
        "[VID] = ?",
        array($vendorId)
    );

    jsonResponseOk(array(
        "deletedRows" => intval($affectedRows)
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
