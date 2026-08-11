<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

function normalizeOneChar($value)
{
    return strtoupper(trim((string) $value));
}

function saveUploadFile($inputName, $subpath)
{
    if (!isset($_FILES[$inputName]) || !is_array($_FILES[$inputName])) {
        return null;
    }

    $file = $_FILES[$inputName];

    if (!isset($file["error"]) || $file["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("อัปโหลดไฟล์ไม่สำเร็จ");
    }

    $allowedExtensions = array("jpg", "jpeg", "png", "gif", "webp");
    $extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        throw new RuntimeException("รองรับเฉพาะไฟล์รูปภาพประเภท JPG, JPEG, PNG, GIF และ WEBP");
    }

    if (!isset($file["tmp_name"]) || !is_uploaded_file($file["tmp_name"])) {
        throw new RuntimeException("ไม่พบไฟล์ที่อัปโหลด");
    }

    $uploadDirectory = dirname(__DIR__, 2) . "/uploads/".$subpath;

    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true)) {
        throw new RuntimeException("ไม่สามารถสร้างโฟลเดอร์อัปโหลด");
    }

    $attempt = 0;
    do {
        $seed = base_convert((string) time(), 10, 36) . base_convert((string) mt_rand(100, 999), 10, 36);
        $filename = strtolower(substr($seed, 0, 10) . "." . $extension);
        $destination = $uploadDirectory . "/" . $filename;
        $attempt++;
    } while (file_exists($destination) && $attempt < 5);

    if (mb_strlen($filename, "UTF-8") > 20) {
        throw new RuntimeException("ชื่อไฟล์ยาวเกินข้อจำกัดของระบบ");
    }

    if (!move_uploaded_file($file["tmp_name"], $destination)) {
        throw new RuntimeException("ไม่สามารถบันทึกไฟล์อัปโหลด");
    }

    return $filename;
}

$userId = isset($_POST["userId"]) ? intval($_POST["userId"]) : 0;
$userCode = isset($_POST["userCode"]) ? trim($_POST["userCode"]) : "";
$nameEn = isset($_POST["nameEn"]) ? trim($_POST["nameEn"]) : "";
$nameTh = isset($_POST["nameTh"]) ? trim($_POST["nameTh"]) : "";
$position = isset($_POST["position"]) ? trim($_POST["position"]) : "";
$email = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
$userGroup = normalizeOneChar(isset($_POST["userGroup"]) ? $_POST["userGroup"] : "");
$statusCode = normalizeOneChar(isset($_POST["statusCode"]) ? $_POST["statusCode"] : "");

if ($userId <= 0) {
    jsonResponseBadRequest("ไม่พบรหัสผู้ใช้งาน");
}

if ($userCode === "") {
    jsonResponseBadRequest("กรุณาระบุรหัสพนักงาน");
}

if ($username === "") {
    jsonResponseBadRequest("กรุณาระบุ Username");
}

if (!in_array($userGroup, array("1", "0"), true)) {
    jsonResponseBadRequest("UGroup ต้องเป็น 1 หรือ 0");
}

if (!in_array($statusCode, array("Y", "N"), true)) {
    jsonResponseBadRequest("UStatus ต้องเป็น Y หรือ N");
}

if (mb_strlen($userCode, "UTF-8") > 8) {
    jsonResponseBadRequest("รหัสพนักงานยาวเกิน 8 ตัวอักษร");
}

if (mb_strlen($nameEn, "UTF-8") > 50) {
    jsonResponseBadRequest("Name EN ยาวเกิน 50 ตัวอักษร");
}

if (mb_strlen($nameTh, "UTF-8") > 50) {
    jsonResponseBadRequest("Name TH ยาวเกิน 50 ตัวอักษร");
}

if (mb_strlen($position, "UTF-8") > 20) {
    jsonResponseBadRequest("ตำแหน่งงานยาวเกิน 20 ตัวอักษร");
}

if (mb_strlen($email, "UTF-8") > 50) {
    jsonResponseBadRequest("Email ยาวเกิน 50 ตัวอักษร");
}

if (mb_strlen($username, "UTF-8") > 10) {
    jsonResponseBadRequest("Username ยาวเกิน 10 ตัวอักษร");
}

if ($password !== "" && mb_strlen($password, "UTF-8") > 100) {
    jsonResponseBadRequest("Password ยาวเกิน 100 ตัวอักษร");
}

if ($email !== "" && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponseBadRequest("รูปแบบ Email ไม่ถูกต้อง");
}

try {
    $db = new DbSqlSrv();

    $currentUser = $db->fetch_one(
        "SELECT TOP 1 [UID], [UImage], [USignature] FROM [dbo].[Insp_Users] WHERE [UID] = ?",
        array($userId)
    );

    if (!$currentUser) {
        jsonResponseBadRequest("ไม่พบข้อมูลผู้ใช้งาน");
    }

    $exists = $db->fetch_one(
        "SELECT TOP 1 [UID] "
        . "FROM [dbo].[Insp_Users] "
        . "WHERE ([UCode] = ? OR [Username] = ?) "
        . "AND [UID] <> ?",
        array($userCode, $username, $userId)
    );

    if ($exists) {
        jsonResponseBadRequest("รหัสพนักงานหรือ Username ซ้ำกับข้อมูลเดิม");
    }

    $newUserImage = saveUploadFile("userImage", "i");
    $newUserSignature = saveUploadFile("userSignature", "s");

    $data = array(
        "UCode" => $userCode,
        "NameEN" => ($nameEn === "") ? null : $nameEn,
        "NameTH" => ($nameTh === "") ? null : $nameTh,
        "Position" => ($position === "") ? null : $position,
        "Email" => ($email === "") ? null : $email,
        "Username" => $username,
        "UGroup" => $userGroup,
        "UStatus" => $statusCode
    );

    if ($newUserImage !== null) {
        $data["UImage"] = $newUserImage;
    }

    if ($newUserSignature !== null) {
        $data["USignature"] = $newUserSignature;
    }

    if ($password !== "") {
        $data["Password"] = md5($password);
    }

    $affectedRows = $db->update(
        "Insp_Users",
        $data,
        "[UID] = ?",
        array($userId)
    );

    $uploadDirectory = dirname(__DIR__, 2) . "/uploads";

    if ($newUserImage !== null && !empty($currentUser["UImage"]) && $currentUser["UImage"] !== $newUserImage) {
        $oldImagePath = $uploadDirectory . "/" . $currentUser["UImage"];
        if (is_file($oldImagePath)) {
            @unlink($oldImagePath);
        }
    }

    if ($newUserSignature !== null && !empty($currentUser["USignature"]) && $currentUser["USignature"] !== $newUserSignature) {
        $oldSignaturePath = $uploadDirectory . "/" . $currentUser["USignature"];
        if (is_file($oldSignaturePath)) {
            @unlink($oldSignaturePath);
        }
    }

    jsonResponseOk(array(
        "updatedRows" => intval($affectedRows)
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
