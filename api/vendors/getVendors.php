<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$keyword = isset($_GET["keyword"]) ? trim($_GET["keyword"]) : "";
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$pageSize = isset($_GET["pageSize"]) ? intval($_GET["pageSize"]) : 10;

if ($page < 1) {
    $page = 1;
}

if ($pageSize < 1) {
    $pageSize = 10;
}

if ($pageSize > 100) {
    $pageSize = 100;
}

try {
    $where = array("1 = 1");
    $params = array();

    if ($keyword !== "") {
        $where[] = "("
            . "[VCode] LIKE ? OR "
            . "[VName] LIKE ?"
            . ")";

        $likeValue = "%" . $keyword . "%";
        $params[] = $likeValue;
        $params[] = $likeValue;
    }

    $sql = "SELECT "
        . "[VID] AS VendorId, "
        . "[VCode] AS VendorCode, "
        . "[VName] AS VendorName, "
        . "CONVERT(varchar(19), [CreatedAt], 120) AS CreatedAtText "
        . "FROM [dbo].[Insp_Vendors] "
        . "WHERE " . implode(" AND ", $where);

    $db = new DbSqlSrv();
    $data = $db->paginateSql(
        $sql,
        $params,
        $page,
        $pageSize,
        "VendorCode ASC, VendorId ASC"
    );

    jsonResponseOk($data);
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
