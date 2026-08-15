<?php

/** @var string $baseUrl */
?>

<aside id="appSidebar" class="sidebar bg-dark text-white d-flex flex-column">

    <div class="sidebar-brand d-flex align-items-center px-3 py-3">
       <div class="sidebar-brand-icon p-1 mr-2 d-flex align-items-center justify-content-center rounded mr-2 bg-warning">
            <img src="<?= $baseUrl ?>assets/images/sewing-machine.png"
                alt="JANOME Logo" 
                class="logo-icon flex-shrink-0">
        </div>

        <div class="sidebar-brand-text">
            <div class="d-flex flex-column ms-2 align-items-start">
                <img src="<?= $baseUrl ?>assets/images/logo-janome.png"
                alt="JANOME Logo" 
                class="logo-main mb-1">
                <small class="text-light janome-subtitle">Check Sheet System</small>
            </div>
        </div>
    </div>

    <nav class="nav flex-column sidebar-nav px-2 sidebar-menu-animated" data-sidebar-menu="true">
    </nav>

    <a href="<?= htmlspecialchars($baseUrl) ?>logout.php"
        class="nav-link text-danger d-flex align-items-center sidebar-logout"
        title="ออกจากระบบ"
        aria-label="ออกจากระบบ">
        <i class="fas fa-sign-out-alt mr-3"></i>
        <span>Logout<small>ออกจากระบบ</small></span>
    </a>
</aside>

<button
    type="button"
    class="sidebar-overlay"
    aria-label="Close sidebar"
    tabindex="-1"></button>
