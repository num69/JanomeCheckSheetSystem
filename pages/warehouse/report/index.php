<?php
$baseUrl = "../../../";
require_once __DIR__ . "/../../../include/auth.php";
$title = "Report Overview";
$pageTitle = "Warehouse Report";
$pageSubtitle = "ภาพรวมรายงานคลังสินค้า";

include __DIR__ . "/../../../include/header.php";
?>

<div class="app-layout">

    <?php include __DIR__ . "/../../../include/sidebar.php"; ?>

    <main class="app-main">
        <?php include __DIR__ . "/../../../include/topbar.php"; ?>

        <section class="content-area page-animated">
            <div class="factory-panel">
                <div class="panel-header">
                    <h6>
                        <i class="fas fa-chart-line"></i>
                        Report Overview
                    </h6>
                </div>

                <div class="notice-list">
                    <div class="notice-item">
                        <span class="notice-dot bg-blue"></span>
                        หน้ารายงานหลักของคลังสินค้า
                    </div>
                    <div class="notice-item">
                        <span class="notice-dot bg-green"></span>
                        ใช้เมนูย่อยเพื่อเปิดรายงานเฉพาะทาง เช่น FG Stock
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<?php include __DIR__ . "/../../../include/footer.php"; ?>