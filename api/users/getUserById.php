<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$userId = isset($_GET["userId"]) ? intval($_GET["userId"]) : 0;

if ($userId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสผู้ใช้งาน");
}

try {
    $db = new DbSqlSrv();

    $row = $db->fetch_one(
        "SELECT "
            . "[UID] AS UserId, "
            . "[UCode] AS UserCode, "
            . "[NameEN] AS NameEn, "
            . "[NameTH] AS NameTh, "
            . "[Position] AS Position, "
            . "[Email] AS Email, "
            . "[UImage] AS UserImage, "
            . "[USignature] AS UserSignature, "
            . "[Username] AS Username, "
            . "[UGroup] AS UserGroup, "
            . "[UStatus] AS StatusCode "
        . "FROM [dbo].[Insp_Users] "
        . "WHERE [UID] = ?",
        array($userId)
    );

    if (!$row) {
        jsonResponseBadRequest("ไม่พบข้อมูลผู้ใช้งาน");
    }

    jsonResponseOk($row);
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
