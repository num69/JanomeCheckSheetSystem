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
			. "[UCode] LIKE ? OR "
			. "[NameTH] LIKE ? OR "
			. "[NameEN] LIKE ? OR "
			. "[Email] LIKE ? OR "
			. "[Position] LIKE ? OR "
			. "[Username] LIKE ? "
		. ")";

		$likeValue = "%" . $keyword . "%";

		for ($index = 1; $index <= 6; $index++) {
			$params[] = $likeValue;
		}
	}

	$sql = "SELECT "
            . "[UID] AS UserId, "
            . "[UCode] AS UserCode, "
            . "COALESCE("
                . "NULLIF(LTRIM(RTRIM([NameTH])), ''), "
                . "NULLIF(LTRIM(RTRIM([NameEN])), ''), "
                . "NULLIF(LTRIM(RTRIM([Username])), '')"
            . ") AS DisplayName, "
            . "[NameTH] AS NameTh, "
            . "[NameEN] AS NameEn, "
            . "[Position], "
            . "[Email] AS Email, "
            . "[Username] AS Username, "
            . "[UGroup] AS UserGroup, "
            . "[UStatus] AS StatusCode "
		. "FROM [dbo].[Insp_Users] "
		. "WHERE " . implode(" AND ", $where);

	$db = new DbSqlSrv();
	$data = $db->paginateSql(
		$sql,
		$params,
		$page,
		$pageSize,
		"DisplayName ASC, UserCode ASC, UserId ASC"
	);

	jsonResponseOk($data);
} catch (Exception $e) {
	jsonResponseInternalServerError($e->getMessage());
}
