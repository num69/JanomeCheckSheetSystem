<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";
require_once __DIR__ . "/../include/db.php";

authorized();

try {
    $db = new DbSqlSrv();
    $rows = $db->fetch_all(
        "SELECT "
            . "[VID] AS VendorId, "
            . "[VCode] AS VendorCode, "
            . "[VName] AS VendorName "
        . "FROM [dbo].[Insp_Vendors] "
        . "ORDER BY [VCode] ASC, [VID] ASC"
    );

    jsonResponseOk(array(
        "items" => $rows
    ));
} catch (Exception $e) {
    jsonResponseInternalServerError($e->getMessage());
}
