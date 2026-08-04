<?php
require_once __DIR__ . "/../../include/session.php";
require_once __DIR__ . "/response.php";

function authorized()
{
    if (!isset($_SESSION["user"])) {
        jsonResponseUnauthorized();
    }
}
?>