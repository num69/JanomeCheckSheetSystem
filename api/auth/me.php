<?php
require_once __DIR__ . "/../include/authorized.php";
require_once __DIR__ . "/../include/response.php";

authorized();

jsonResponseOk($_SESSION["user"]);
?>