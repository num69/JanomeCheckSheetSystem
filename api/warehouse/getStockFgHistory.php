<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

$startDate = isset($_GET["startDate"]) ? trim($_GET["startDate"]) : "";
$endDate = isset($_GET["endDate"]) ? trim($_GET["endDate"]) : "";
$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
$pageSize = isset($_GET["pageSize"]) ? intval($_GET["pageSize"]) : 10;

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
    $cteSql = ""
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
        . ") ";

    $selectColumns = array(
        "SI.CQty_In AS QtyIn",
        "ISNULL(SO.CQty_OUT, 0) AS QtyOut",
        "PD.Qty_Plan - ISNULL(SO.CQty_OUT, 0) AS QtyBalance",
        "PD.FGID",
        "PD.Lot_no AS LotNo",
        "PD.Part_No AS PartNo",
        "PD.Part_Name AS PartName",
        "PD.Qty_Plan AS QtyPlan"
    );

    $fromSql = ""
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
        . ") AS SO ";

    $params = array(
        $queryStartDate,
        $queryEndDate,
        $queryStartDate,
        $queryEndDate
    );

    $orderBy = "PD.Lot_no ASC";

    $Db = new DbSqlSrv();

    $data = $Db->paginateSqlCte(
                    $cteSql,
                    $selectColumns,
                    $fromSql,
                    $params,
                    $page,
                    $pageSize,
                    $orderBy
                );
    // $test = $data["items"]; // เริ่มต้นด้วยข้อมูลชุดแรก

    // // ต้องการเพิ่มซ้ำอีก 16 รอบ (รวมเป็น 17 ชุด)
    // for ($i = 0; $i < 5; $i++) {
    //     $test = array_merge($test, $data["items"]);
    // }
    // $data["items"] = $test;
    jsonResponseOk($data);

} catch (Exception $e) {

    jsonResponseInternalServerError($e->getMessage());
    
}

?>