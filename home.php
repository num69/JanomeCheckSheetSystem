<?php
$baseUrl = "";
require_once __DIR__ . "/include/auth.php";
$title = "Check Sheet System - Dashboard";
$pageTitle = "Dashboard";
$pageSubtitle = "ภาพรวมการตรวจสอบ Check Sheet";
include __DIR__ . "/include/header.php";
?>

<div class="app-layout">

    <?php include __DIR__ . "/include/sidebar.php"; ?>

    <main class="app-main">
        <?php include __DIR__ . "/include/topbar.php"; ?>

        <section class="content-area page-animated">

            <div class="factory-hero">
                <div class="factory-hero-content">
                    <div class="factory-eyebrow"><span class="pulse-dot"></span> CHECK SHEET / LIVE OVERVIEW</div>
                    <h4>Factory Check Sheet System</h4>
                    <p>ระบบติดตามและตรวจสอบรายการ Check Sheet ภายในโรงงาน</p>
                    <div class="factory-meta">
                        <span><i class="fas fa-clock mr-1"></i> อัปเดตล่าสุดวันนี้</span>
                        <span><i class="fas fa-shield-alt mr-1"></i> ระบบพร้อมใช้งาน</span>
                    </div>
                </div>
                <div class="hero-mark" aria-hidden="true">
                    <span class="hero-ring hero-ring-one"></span>
                    <span class="hero-ring hero-ring-two"></span>
                    <i class="fas fa-warehouse"></i>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-blue">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <div class="status-label">Check Sheets Today</div>
                            <div class="status-value">128</div>
                            <div class="status-sub">รายการตรวจวันนี้</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <div class="status-label">Completed</div>
                            <div class="status-value">96</div>
                            <div class="status-sub">ตรวจเสร็จแล้ว</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-orange">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div>
                            <div class="status-label">In Progress</div>
                            <div class="status-value">24</div>
                            <div class="status-sub">กำลังตรวจสอบ</div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="status-card">
                        <div class="status-icon bg-red">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div>
                            <div class="status-label">Need Attention</div>
                            <div class="status-value">8</div>
                            <div class="status-sub">รายการผิดปกติ</div>
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
                                <span>เริ่มตรวจสอบ</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-dolly"></i>
                                <span>รายการตรวจวันนี้</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-truck-loading"></i>
                                <span>งานที่กำลังดำเนินการ</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-search"></i>
                                <span>ค้นหา Check Sheet</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-clipboard-list"></i>
                                <span>ตรวจสอบย้อนหลัง</span>
                            </a>

                            <a href="#" class="quick-menu-card">
                                <i class="fas fa-file-excel"></i>
                                <span>ส่งออกรายงาน</span>
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
                                มีรายการผิดปกติที่ต้องติดตาม 8 รายการ
                            </div>
                            <div class="notice-item">
                                <span class="notice-dot bg-blue"></span>
                                วันนี้ตรวจเสร็จแล้ว 96 รายการ
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </section>

    </main>
</div>



<?php include "include/footer.php"; ?>
