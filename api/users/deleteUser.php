<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();
$userId = isset($_POST["userId"]) ? intval($_POST["userId"]) : 0;
if ($userId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสผู้ใช้งาน");
}

try {
    $db = new DbSqlSrv();
    $user = $db->fetch_one("SELECT [UID] FROM [dbo].[Insp_Users] WHERE [UID] = ?", array($userId));
    if (!$user) {
        jsonResponseBadRequest("ไม่พบข้อมูลผู้ใช้งาน");
    }
    $affectedRows = $db->delete("Insp_Users", "[UID] = ?", array($userId));
    jsonResponseOk(array("deletedRows" => intval($affectedRows)));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
