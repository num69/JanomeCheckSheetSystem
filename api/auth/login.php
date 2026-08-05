<?php
include __DIR__ . "/../../include/session.php";
include __DIR__ . "/../include/db.php";
include __DIR__ . "/../include/response.php";

try {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        jsonResponseBadRequest("Username and password are required");
        exit();
    }

    $params = array($username, md5($password));

    $db = new DbSqlSrv();
    $sql = "SELECT TOP 1 [UID]
            ,[UCode]
            ,[NameEN]
            ,[NameTH]
            ,[Position]
            ,[Email]
            ,[UImage]
            ,[USignature]
            ,[Username]
            ,[Password]
            ,[UGroup]
            ,[UStatus]
        FROM [dbo].[Insp_Users] WHERE 
        [UStatus] = 'Y' AND [Username] = ? AND [Password] = ?";

    $row = $db->fetch_one($sql, $params);

    if (!$row) {
        jsonResponseBadRequest("Invalid username or password");
        exit();
    }

    // Set session variables
    $_SESSION["user"] = array(
        "id" => $row["UID"],
        "code" => $row["UCode"],
        "name_en" => $row["NameEN"],
        "name_th" => $row["NameTH"],
        "position" => $row["Position"],
        "email" => $row["Email"],
        "image" => $row["UImage"],
        "signature" => $row["USignature"],
        "username" => $row["Username"],
        "group" => $row["UGroup"],
        "status" => $row["UStatus"]
    );

    jsonResponseOk($row);
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
    exit();
}
