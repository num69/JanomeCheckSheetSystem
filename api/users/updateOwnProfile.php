<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

function saveOwnProfileUpload($inputName, $subpath)
{
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$inputName];
    if ($file["error"] !== UPLOAD_ERR_OK || !is_uploaded_file($file["tmp_name"])) {
        throw new RuntimeException("อัปโหลดไฟล์ไม่สำเร็จ");
    }

    $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    if (!in_array($extension, array("jpg", "jpeg", "png", "gif", "webp"), true)) {
        throw new RuntimeException("รองรับเฉพาะไฟล์ JPG, JPEG, PNG, GIF และ WEBP");
    }

    $directory = dirname(__DIR__, 2) . "/uploads/" . $subpath;
    if (!is_dir($directory) && !mkdir($directory, 0775, true)) {
        throw new RuntimeException("ไม่สามารถสร้างโฟลเดอร์อัปโหลด");
    }

    do {
        $filename = strtolower(base_convert((string) time(), 10, 36) . base_convert((string) mt_rand(1000, 9999), 10, 36) . "." . $extension);
    } while (file_exists($directory . "/" . $filename));

    if (!move_uploaded_file($file["tmp_name"], $directory . "/" . $filename)) {
        throw new RuntimeException("ไม่สามารถบันทึกไฟล์อัปโหลด");
    }

    return $filename;
}

function deleteReplacedUpload($filename, $subpath)
{
    $safeFilename = basename(str_replace("\\", "/", (string) $filename));
    $path = dirname(__DIR__, 2) . "/uploads/" . $subpath . "/" . $safeFilename;
    if ($safeFilename !== "" && is_file($path)) {
        @unlink($path);
    }
}

$userId = isset($_SESSION["user"]["id"]) ? intval($_SESSION["user"]["id"]) : 0;
$nameTh = isset($_POST["nameTh"]) ? trim($_POST["nameTh"]) : "";
$nameEn = isset($_POST["nameEn"]) ? trim($_POST["nameEn"]) : "";
$position = isset($_POST["position"]) ? trim($_POST["position"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";

if ($userId <= 0) {
    jsonResponseUnauthorized();
}

if ($nameTh === "" && $nameEn === "") {
    jsonResponseBadRequest("กรุณาระบุชื่อภาษาไทยหรือชื่อภาษาอังกฤษอย่างน้อย 1 รายการ");
}

if (mb_strlen($nameTh, "UTF-8") > 50 || mb_strlen($nameEn, "UTF-8") > 50) {
    jsonResponseBadRequest("ชื่อผู้ใช้งานยาวเกิน 50 ตัวอักษร");
}

if (mb_strlen($position, "UTF-8") > 20) {
    jsonResponseBadRequest("ตำแหน่งงานยาวเกิน 20 ตัวอักษร");
}

if (mb_strlen($email, "UTF-8") > 50) {
    jsonResponseBadRequest("Email ยาวเกิน 50 ตัวอักษร");
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponseBadRequest("รูปแบบ Email ไม่ถูกต้อง");
}

try {
    $db = new DbSqlSrv();
    $currentUser = $db->fetch_one(
        "SELECT TOP 1 [UImage], [USignature] FROM [dbo].[Insp_Users] WHERE [UID] = ?",
        array($userId)
    );

    if (!$currentUser) {
        jsonResponseBadRequest("ไม่พบข้อมูลผู้ใช้งาน");
    }

    $newImage = saveOwnProfileUpload("userImage", "profile");
    $newSignature = saveOwnProfileUpload("userSignature", "signature");
    $data = array(
        "NameTH" => $nameTh === "" ? null : $nameTh,
        "NameEN" => $nameEn === "" ? null : $nameEn,
        "Position" => $position === "" ? null : $position,
        "Email" => $email === "" ? null : $email
    );

    if ($newImage !== null) {
        $data["UImage"] = $newImage;
    }
    if ($newSignature !== null) {
        $data["USignature"] = $newSignature;
    }

    $db->update(
        "Insp_Users",
        $data,
        "[UID] = ?",
        array($userId)
    );

    $_SESSION["user"]["name_th"] = $nameTh === "" ? null : $nameTh;
    $_SESSION["user"]["name_en"] = $nameEn === "" ? null : $nameEn;
    $_SESSION["user"]["position"] = $position === "" ? null : $position;
    $_SESSION["user"]["email"] = $email === "" ? null : $email;
    if ($newImage !== null) {
        deleteReplacedUpload($currentUser["UImage"], "profile");
        $_SESSION["user"]["image"] = $newImage;
    }
    if ($newSignature !== null) {
        deleteReplacedUpload($currentUser["USignature"], "signature");
        $_SESSION["user"]["signature"] = $newSignature;
    }

    $displayName = $nameTh !== "" ? $nameTh : $nameEn;
    jsonResponseOk(array(
        "displayName" => $displayName,
        "position" => $position,
        "imageUrl" => $newImage !== null ? "uploads/profile/" . rawurlencode($newImage) : null
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
