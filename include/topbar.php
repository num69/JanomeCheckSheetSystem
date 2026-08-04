<?php
$pageTitle = isset($pageTitle) ? $pageTitle : "";
$pageSubtitle = isset($pageSubtitle) ? $pageSubtitle : "";
$displayName = isset($_SESSION["user"]["name_th"]) ? $_SESSION["user"]["name_th"] : "Unknown User";
$role = isset($_SESSION["user"]["group"]) ? $_SESSION["user"]["group"] : "User";
/** @var string $baseUrl */
?>

<header class="navbar navbar-expand bg-white border-bottom px-3">
    <button type="button"
        class="btn btn-secondary btn-sm sidebar-toggle mr-3"
        title="Toggle Sidebar"
        aria-label="Toggle sidebar"
        aria-controls="appSidebar"
        aria-expanded="true">
        <i class="fas fa-bars"></i>
    </button>
    <div>
        <h5 class="mb-0 font-weight-bold text-dark">
            <?= htmlspecialchars($pageTitle) ?>
        </h5>
        <small class="text-muted">
            <?= htmlspecialchars($pageSubtitle) ?>
        </small>
    </div>

    <div class="ml-auto d-flex align-items-center">

        <button type="button"
            class="btn btn-light rounded-circle position-relative mr-2"
            title="Notification">
            <i class="fas fa-bell"></i>

            <span class="badge badge-danger position-absolute topbar-badge">
                3
            </span>
        </button>

        <div class="dropdown">
            <button
                class="btn btn-light dropdown-toggle d-flex align-items-center"
                id="userDropdown"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">

                <div class="text-right mr-2 d-none d-sm-block">
                    <div class="font-weight-bold">
                        <?= htmlspecialchars($displayName) ?>
                    </div>
                    <small class="text-muted">
                        <?= htmlspecialchars($role) ?>
                    </small>
                </div>

                <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center user-avatar">
                    <i class="fas fa-user"></i>
                </span>
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow"
                aria-labelledby="userDropdown">

                <div class="dropdown-header">
                    <strong><?= htmlspecialchars($displayName) ?></strong><br>
                    <small><?= htmlspecialchars($role) ?></small>
                </div>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item"
                    href="<?= htmlspecialchars($baseUrl) ?>pages/profile/index.php">
                    <i class="fas fa-id-card mr-2"></i>
                    Profile
                </a>

                <a class="dropdown-item"
                    href="<?= htmlspecialchars($baseUrl) ?>pages/account/change_password.php">
                    <i class="fas fa-key mr-2"></i>
                    Change Password
                </a>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item text-danger"
                    href="<?= htmlspecialchars($baseUrl) ?>logout.php">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </a>
            </div>
        </div>

    </div>
</header>