<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$startDate = isset($_GET["startDate"]) ? trim($_GET["startDate"]) : "";
$endDate = isset($_GET["endDate"]) ? trim($_GET["endDate"]) : "";

if ($startDate === "" || $endDate === "") {
    jsonResponseBadRequest("กรุณาระบุวันที่เริ่มต้นและวันที่สิ้นสุด");
    exit;
}

$startDateObject = DateTime::createFromFormat("Y-m-d", $startDate);
$endDateObject = DateTime::createFromFormat("Y-m-d", $endDate);

if (
    $startDateObject === false ||
    $endDateObject === false ||
    $startDateObject->format("Y-m-d") !== $startDate ||
    $endDateObject->format("Y-m-d") !== $endDate
) {
    jsonResponseBadRequest("รูปแบบวันที่ไม่ถูกต้อง ต้องเป็น YYYY-MM-DD");
    exit;
}

if ($startDateObject > $endDateObject) {
    jsonResponseBadRequest("วันที่เริ่มต้นต้องไม่มากกว่าวันที่สิ้นสุด");
    exit;
}

$endDateObject->modify("+1 day");

$queryStartDate = $startDateObject->format("Y-m-d 00:00:00");
$queryEndDate = $endDateObject->format("Y-m-d 00:00:00");

try {
 $summarySql = ""
        . "WITH StockIn AS "
        . "( "
            . "SELECT "
                . "SN.FGID, "
                . "SUM(SN.Qty_In) AS CQty_In "
            . "FROM FG_Trans_StockIN AS SN "
            . "WHERE "
                . "SN.Ent_Date >= ? "
                . "AND SN.Ent_Date < ? "
            . "GROUP BY "
                . "SN.FGID "
        . ") "
        . "SELECT "
            . "COUNT(*) AS TotalItems, "
            . "ISNULL(SUM(SI.CQty_In), 0) AS TotalStockIn, "
            . "ISNULL(SUM(ISNULL(SO.CQty_OUT, 0)), 0) AS TotalStockOut, "
            . "ISNULL(SUM(PD.Qty_Plan - ISNULL(SO.CQty_OUT, 0)), 0) AS TotalBalance "
        . "FROM StockIn AS SI "
        . "INNER JOIN FG_Product_Detail AS PD "
            . "ON SI.FGID = PD.FGID "
        . "OUTER APPLY "
        . "( "
            . "SELECT "
                . "SUM(O.Qty_OUT) AS CQty_OUT "
            . "FROM FG_Trans_StockOUT AS O "
            . "WHERE "
                . "O.FGID = SI.FGID "
                . "AND O.Ent_Date >= ? "
                . "AND O.Ent_Date < ? "
        . ") AS SO";

    $params = array(
        $queryStartDate,
        $queryEndDate,
        $queryStartDate,
        $queryEndDate
    );

    $Db = new DbSqlSrv();

    $summary = $Db->fetch_one(
        $summarySql,
        $params
    );

    jsonResponseOk(array(
        "totalItems" => isset($summary["TotalItems"])
            ? (int) $summary["TotalItems"]
            : 0,

        "totalStockIn" => isset($summary["TotalStockIn"])
            ? (float) $summary["TotalStockIn"]
            : 0,

        "totalStockOut" => isset($summary["TotalStockOut"])
            ? (float) $summary["TotalStockOut"]
            : 0,

        "totalBalance" => isset($summary["TotalBalance"])
            ? (float) $summary["TotalBalance"]
            : 0
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
