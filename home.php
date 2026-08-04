<?php
$baseUrl = "";
require_once __DIR__ . "/include/auth.php";
$title = "FG Warehouse System";
$pageTitle = "Dashboard";
$pageSubtitle = "ภาพรวมระบบคลังสินค้า FG";
include __DIR__ . "/include/header.php";
?>

<div class="app-layout">

    <?php include __DIR__ . "/include/sidebar.php"; ?>

    <main class="app-main">
        <?php include __DIR__ . "/include/topbar.php"; ?>

        <section class="content-area page-animated">

            <div class="factory-hero">
                <div>
                    <h4>FG Warehouse System</h4>
                    <p>ระบบจัดการคลังสินค้า Finished Goods สำหรับโรงงาน</p>
                </div>
                <div class="hero-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-blue">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div>
                            <div class="status-label">Total Stock</div>
                            <div class="status-value">1,250</div>
                            <div class="status-sub">Pallet / Box</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-green">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                        <div>
                            <div class="status-label">Receive Today</div>
                            <div class="status-value">85</div>
                            <div class="status-sub">Items</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-orange">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div>
                            <div class="status-label">Issue Today</div>
                            <div class="status-value">42</div>
                            <div class="status-sub">Items</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-red">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="status-label">Low Stock</div>
                            <div class="status-value">7</div>
                            <div class="status-sub">Models</div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row">

                <div class="col-lg-8 mb-3">
                    <div class="factory-panel">
                        <div class="panel-header">
                            <h6>
                                <i class="fas fa-tasks"></i>
                                Quick Operation
                            </h6>
                        </div>

                        <div class="quick-menu-grid">

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-barcode"></i>
                                <span>Scan Barcode</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-dolly"></i>
                                <span>Receive FG</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-truck-loading"></i>
                                <span>Issue FG</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-search"></i>
                                <span>Search Stock</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Stock Check</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-file-excel"></i>
                                <span>Export Report</span>
                            </a>

                        </div>
                    </div>
                </div>

                <div class="col-lg-4 mb-3">
                    <div class="factory-panel">
                        <div class="panel-header">
                            <h6>
                                <i class="fas fa-bell"></i>
                                System Notice
                            </h6>
                        </div>

                        <div class="notice-list">
                            <div class="notice-item">
                                <span class="notice-dot bg-green"></span>
                                ระบบพร้อมใช้งาน
                            </div>
                            <div class="notice-item">
                                <span class="notice-dot bg-orange"></span>
                                มีสินค้าใกล้หมด 7 รายการ
                            </div>
                            <div class="notice-item">
                                <span class="notice-dot bg-blue"></span>
                                วันนี้มีรายการรับเข้า 85 รายการ
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section>

    </main>
</div>



<?php include "include/footer.php"; ?>