<?php
include __DIR__ . "/../include/session.php";
if (isset($_SESSION["user"])) {
    header("Location: home.php");
} else {
    header("Location: login.php");
}
exit;
?>