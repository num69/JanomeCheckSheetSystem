<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$partId = isset($_POST["partId"]) ? intval($_POST["partId"]) : 0;

if ($partId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสชิ้นส่วน");
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

    $affectedRows = $db->delete(
        "insp_Parts",
        "[PID] = ?",
        array($partId)
    );

    jsonResponseOk(array(
        "deletedRows" => intval($affectedRows)
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
