<?php
require_once __DIR__ . "/include/session.php";
// $baseUrl = "./";
$title = "Login - Factory System";
$bodyClass = "login-page";
$pageScripts = array("assets/js/login.js");
$pageStyles = array("assets/css/login.css");

if (isset($_SESSION["user"])) {
    header("Location: home.php");
}

include __DIR__ . "/include/header.php";
?>

<div class="login-wrapper page-animated">
    <div class="login-card">

        <div class="login-brand">
            <div class="brand-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <h4>FG Warehouse System</h4>
            <p>Factory Management Portal</p>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label>Username</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    <input type="text" id="username" name="username" class="form-control" placeholder="admin1" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    <input type="password" id="password" name="password" class="form-control" placeholder="1234" required>
                </div>
            </div>

            <button type="submit" id="loginButton" class="btn btn-factory btn-block">
                <i class="fas fa-sign-in-alt"></i>
                เข้าสู่ระบบ
            </button>
            <div class="login-footer">
                Local Factory System
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . "/include/footer.php"; ?>