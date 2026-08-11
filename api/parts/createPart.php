<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$partCode = isset($_POST["partCode"]) ? trim($_POST["partCode"]) : "";
$partName = isset($_POST["partName"]) ? trim($_POST["partName"]) : "";

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

    $exists = $db->fetch_one(
        "SELECT TOP 1 [PID] "
        . "FROM [dbo].[insp_Parts] "
        . "WHERE [PCode] = ?",
        array($partCode)
    );

    if ($exists) {
        jsonResponseBadRequest("รหัสชิ้นส่วนซ้ำกับข้อมูลเดิม");
    }

    $insertedId = $db->insert_id(
        "insp_Parts",
        array(
            "PCode" => $partCode,
            "PName" => $partName
        ),
        "PID"
    );

    jsonResponseOk(array(
        "partId" => intval($insertedId)
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
