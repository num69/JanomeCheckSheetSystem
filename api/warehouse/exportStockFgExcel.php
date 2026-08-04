<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";
require_once __DIR__ . "/../include/xlsxwriter.class.php";

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
    $sql = ""
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
            . "SI.CQty_In AS QtyIn, "
            . "ISNULL(SO.CQty_OUT, 0) AS QtyOut, "
            . "PD.Qty_Plan - ISNULL(SO.CQty_OUT, 0) AS QtyBalance, "
            . "PD.FGID, "
            . "PD.Lot_no AS LotNo, "
            . "PD.Part_No AS PartNo, "
            . "PD.Part_Name AS PartName, "
            . "PD.Qty_Plan AS QtyPlan "

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
        . ") AS SO "

        . "ORDER BY "
            . "PD.Lot_no ASC, "
            . "PD.FGID ASC";

    $params = array(
        $queryStartDate,
        $queryEndDate,
        $queryStartDate,
        $queryEndDate
    );

    $Db = new DbSqlSrv();

    /*
     * เปลี่ยนชื่อ fetchAll ให้ตรงกับ method ใน DbSqlSrv ของคุณ
     */
    $items = $Db->fetch_all($sql, $params);

    $sheetName = "SHEET1";

    $writer = new XLSXWriter();


    $writer->setAuthor(
        "FG Warehouse System"
    );

     $writer->writeSheetHeader(
        $sheetName,
        array(
            "LOT NO" => "string",
            "PART NO" => "string",
            "PART NAME" => "string",
            "Q'TY PLAN" => "GENERAL",
            "STOCK IN" => "GENERAL",
            "STOCK OUT" => "GENERAL",
            "BALANCE" => "GENERAL"
        ),
        array(
            "suppress_row" => true,
            "widths" => array(
                15,
                20,
                52,
                14,
                14,
                14,
                14
            )
        )
    );

    /*
     * แถวที่ 1: หัวรายงาน
     */
    $title = ""
        . "DATA SEWING STOCK IN - OUT DATE "
        . $startDate
        . " To "
        . $endDate;

    $writer->writeSheetRow(
        $sheetName,
        array(
            $title,
            "",
            "",
            "",
            "",
            "",
            ""
        ),
        array(
            "font" => "Arial",
            "font-size" => 14,
            "font-style" => "bold",
            "halign" => "center",
            "valign" => "center",
            "height" => 26
        )
    );

    /*
     * รวมเซลล์ A1:G1
     *
     * Row และ Column เริ่มนับจาก 0
     */
    $writer->markMergedCell(
        $sheetName,
        0,
        0,
        0,
        6
    );

    /*
     * แถวที่ 2: หัวคอลัมน์
     */
    $writer->writeSheetRow(
        $sheetName,
        array(
            "LOT NO",
            "PART NO",
            "PART NAME",
            "Q'TY PLAN",
            "STOCK IN",
            "STOCK OUT",
            "BALANCE"
        ),
        array(
            "font" => "Arial",
            "font-size" => 11,
            "font-style" => "bold",
            "fill" => "#D3D3D3",
            "border" => "left,right,top,bottom",
            "border-style" => "thin",
            "border-color" => "#000000",
            "halign" => "center",
            "valign" => "center",
            "height" => 22
        )
    );

    /*
     * แถวที่ 3 เป็นต้นไป: เขียนข้อมูลลง Excel
     */
    foreach ($items as $item) {
        $writer->writeSheetRow(
            $sheetName,
            array(
                isset($item["LotNo"])
                    ? $item["LotNo"]
                    : "",

                isset($item["PartNo"])
                    ? $item["PartNo"]
                    : "",

                isset($item["PartName"])
                    ? $item["PartName"]
                    : "",

                isset($item["QtyPlan"])
                    ? (int) $item["QtyPlan"]
                    : 0,

                isset($item["QtyIn"])
                    ? (int) $item["QtyIn"]
                    : 0,

                isset($item["QtyOut"])
                    ? (int) $item["QtyOut"]
                    : 0,

                isset($item["QtyBalance"])
                    ? (int) $item["QtyBalance"]
                    : 0
            ),
            array(
                "font" => "Arial",
                "font-size" => 11,
                "border" => "left,right,top,bottom",
                "border-style" => "thin",
                "border-color" => "#000000",
                "valign" => "center",
                "height" => 20
            )
        );
    }

    /*
     * ตั้งชื่อไฟล์
     */
    $filename = ""
        . "FG_Stock_In_Out_"
        . $startDate
        . "_to_"
        . $endDate
        . ".xlsx";

    /*
     * ล้าง Output Buffer
     *
     * ถ้ามีข้อความหรือช่องว่างติดไปกับไฟล์
     * Excel อาจแจ้งว่าไฟล์เสีย
     */
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header(
        "Content-Type: "
        . "application/vnd.openxmlformats-officedocument."
        . "spreadsheetml.sheet"
    );

    header(
        'Content-Disposition: attachment; filename="'
        . XLSXWriter::sanitize_filename($filename)
        . '"'
    );

    header("Content-Transfer-Encoding: binary");
    header("Cache-Control: must-revalidate");
    header("Pragma: public");

    /*
     * ส่ง Excel ให้ผู้ใช้ดาวน์โหลด
     */
    $writer->writeToStdOut();
    
    exit;

} catch (Exception $e) {

    jsonResponseInternalServerError($e->getMessage());
    exit();
}