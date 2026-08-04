<?php

/** @var string $baseUrl */
?>

<aside id="appSidebar" class="sidebar bg-dark text-white d-flex flex-column">

    <div class="sidebar-brand d-flex align-items-center px-3 py-3">
        <div class="sidebar-brand-icon bg-warning text-dark d-flex align-items-center justify-content-center rounded mr-2">
            <i class="fas fa-warehouse"></i>
        </div>

        <div class="sidebar-brand-text">
            <strong class="d-block">FG Warehouse</strong>
            <small class="text-light">Factory System</small>
        </div>
    </div>

    <nav class="nav flex-column sidebar-nav px-2 sidebar-menu-animated" data-sidebar-menu="true">
    </nav>

    <a href="<?= $baseUrl ?>logout.php"
        class="nav-link text-danger d-flex align-items-center sidebar-logout">
        <i class="fas fa-sign-out-alt mr-3"></i>
        <span>Logout</span>
    </a>
</aside>

<button
    type="button"
    class="sidebar-overlay"
    aria-label="Close sidebar"
    tabindex="-1"></button>