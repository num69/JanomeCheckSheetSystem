<?php
require_once __DIR__ . "/session.php";

if (!isset($_SESSION["user"])) {
    $baseUrl = isset($baseUrl) ? $baseUrl : "";
    header("Location: " . $baseUrl . "login.php");
    exit;
}
?>