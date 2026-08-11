<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$partId = isset($_POST["partId"]) ? intval($_POST["partId"]) : 0;
$partCode = isset($_POST["partCode"]) ? trim($_POST["partCode"]) : "";
$partName = isset($_POST["partName"]) ? trim($_POST["partName"]) : "";

if ($partId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสชิ้นส่วน");
}

if ($partCode === "") {
    jsonResponseBadRequest("กรุณาระบุรหัสชิ้นส่วน");
}

if ($partName === "") {
    jsonResponseBadRequest("กรุณาระบุชื่อชิ้นส่วน");
}

if (mb_strlen($partCode, "UTF-8") > 20) {
    jsonResponseBadRequest("รหัสชิ้นส่วนยาวเกิน 20 ตัวอักษร");
}

if (mb_strlen($partName, "UTF-8") > 255) {
    jsonResponseBadRequest("ชื่อชิ้นส่วนยาวเกิน 255 ตัวอักษร");
}

try {
    $db = new DbSqlSrv();

    $current = $db->fetch_one(
        "SELECT TOP 1 [PID] FROM [dbo].[insp_Parts] WHERE [PID] = ?",
        array($partId)
    );

    if (!$current) {
        jsonResponseBadRequest("ไม่พบข้อมูลชิ้นส่วน");
    }

    $exists = $db->fetch_one(
        "SELECT TOP 1 [PID] "
        . "FROM [dbo].[insp_Parts] "
        . "WHERE [PCode] = ? AND [PID] <> ?",
        array($partCode, $partId)
    );

    if ($exists) {
        jsonResponseBadRequest("รหัสชิ้นส่วนซ้ำกับข้อมูลเดิม");
    }

    $affectedRows = $db->update(
        "insp_Parts",
        array(
            "PCode" => $partCode,
            "PName" => $partName
        ),
        "[PID] = ?",
        array($partId)
    );

    jsonResponseOk(array(
        "updatedRows" => intval($affectedRows)
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
