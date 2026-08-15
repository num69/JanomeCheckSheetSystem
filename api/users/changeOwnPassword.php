<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$userId = isset($_SESSION["user"]["id"]) ? intval($_SESSION["user"]["id"]) : 0;
$currentPassword = isset($_POST["currentPassword"]) ? trim($_POST["currentPassword"]) : "";
$newPassword = isset($_POST["newPassword"]) ? trim($_POST["newPassword"]) : "";
$confirmPassword = isset($_POST["confirmPassword"]) ? trim($_POST["confirmPassword"]) : "";

if ($userId <= 0) {
    jsonResponseUnauthorized();
}

if ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {
    jsonResponseBadRequest("กรุณากรอกรหัสผ่านให้ครบถ้วน");
}

if ($newPassword !== $confirmPassword) {
    jsonResponseBadRequest("รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน");
}

if (mb_strlen($newPassword, "UTF-8") > 100) {
    jsonResponseBadRequest("รหัสผ่านใหม่ยาวเกิน 100 ตัวอักษร");
}

if ($currentPassword === $newPassword) {
    jsonResponseBadRequest("รหัสผ่านใหม่ต้องไม่ซ้ำกับรหัสผ่านปัจจุบัน");
}

try {
    $db = new DbSqlSrv();
    $user = $db->fetch_one(
        "SELECT TOP 1 [UID] FROM [dbo].[Insp_Users] WHERE [UID] = ? AND [Password] = ? AND [UStatus] = 'Y'",
        array($userId, md5($currentPassword))
    );

    if (!$user) {
        jsonResponseBadRequest("รหัสผ่านปัจจุบันไม่ถูกต้อง");
    }

    $affectedRows = $db->update(
        "Insp_Users",
        array("Password" => md5($newPassword)),
        "[UID] = ?",
        array($userId)
    );

    jsonResponseOk(array("updatedRows" => intval($affectedRows)));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
