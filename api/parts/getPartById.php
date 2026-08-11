<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$partId = isset($_GET["partId"]) ? intval($_GET["partId"]) : 0;

if ($partId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสชิ้นส่วน");
}

try {
    $db = new DbSqlSrv();

    $row = $db->fetch_one(
        "SELECT "
            . "[PID] AS PartId, "
            . "[PCode] AS PartCode, "
            . "[PName] AS PartName, "
            . "CONVERT(varchar(19), [CreatedAt], 120) AS CreatedAtText "
        . "FROM [dbo].[insp_Parts] "
        . "WHERE [PID] = ?",
        array($partId)
    );

    if (!$row) {
        jsonResponseBadRequest("ไม่พบข้อมูลชิ้นส่วน");
    }

    jsonResponseOk($row);
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
