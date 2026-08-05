<?php
$baseUrl = "../../../";
require_once __DIR__ . "/../../../" . "include/auth.php";
$pageTitle = "Warehouse";
$pageSubtitle = "FG Stock";
$pageScripts = array("assets/js/pages/warehouse/report/fgStock.js?v=" . time());
$pageStyles = array("assets/css/pages/warehouse/report/fgStock.css");

include __DIR__ . "/../../../" . "include/header.php";
?>

<div class="app-layout">

    <?php include __DIR__ . "/../../../" . "include/sidebar.php"; ?>

    <main class="app-main">
        <?php include __DIR__ . "/../../../" . "include/topbar.php"; ?>

        <section class="content-area page-animated">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <div class="page-header-title">
                        <div class="page-header-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>

                        <div>
                            <h4 class="mb-1">FG Stock In - Out</h4>
                            <p class="mb-0">
                                รายงานการรับเข้าและจ่ายออกสินค้า Finished Goods
                            </p>
                        </div>
                    </div>
                </div>

                <div class="page-header-actions">
                    <button
                        type="button"
                        class="btn btn-outline-success"
                        id="btnExportExcel">
                        <i class="fas fa-file-excel mr-1"></i>
                        Export Excel
                    </button>

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        id="btnPrint">
                        <i class="fas fa-print mr-1"></i>
                        Print
                    </button>
                </div>
            </div>

            <!-- Filter -->
            <div class="card report-filter-card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="fas fa-filter"></i>
                        เงื่อนไขการค้นหา
                    </div>
                </div>

                <div class="card-body">
                    <form id="reportFilterForm">
                        <div class="form-row align-items-end">

                            <div class="form-group col-lg-4 col-md-6">
                                <label for="startDate">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    วันที่เริ่มต้น
                                </label>

                                <input
                                    type="text"
                                    class="form-control bg-white"
                                    id="startDate"
                                    name="startDate"
                                    readonly
                                    required>
                            </div>

                            <div class="form-group col-lg-4 col-md-6">
                                <label for="endDate">
                                    <i class="far fa-calendar-check mr-1"></i>
                                    วันที่สิ้นสุด
                                </label>

                                <input
                                    type="text"
                                    class="form-control bg-white"
                                    id="endDate"
                                    name="endDate"
                                    readonly
                                    required>
                            </div>

                            <div class="form-group col-lg-4">
                                <button
                                    type="button"
                                    class="btn btn-primary btn-search"
                                    id="btnSearch">
                                    <i class="fas fa-search mr-1"></i>
                                    ค้นหาข้อมูล
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-light btn-reset"
                                    id="btnReset">
                                    <i class="fas fa-undo-alt mr-1"></i>
                                    ล้างค่า
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary -->
            <div class="row report-summary">

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="summary-card">
                        <div class="summary-icon summary-icon-primary">
                            <i class="fas fa-boxes"></i>
                        </div>

                        <div class="summary-content">
                            <div class="summary-label">จำนวนรายการ</div>
                            <div class="summary-value" id="totalItems">0</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="summary-card">
                        <div class="summary-icon summary-icon-success">
                            <i class="fas fa-arrow-down"></i>
                        </div>

                        <div class="summary-content">
                            <div class="summary-label">Stock In</div>
                            <div class="summary-value" id="totalStockIn">0</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="summary-card">
                        <div class="summary-icon summary-icon-danger">
                            <i class="fas fa-arrow-up"></i>
                        </div>

                        <div class="summary-content">
                            <div class="summary-label">Stock Out</div>
                            <div class="summary-value" id="totalStockOut">0</div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="summary-card">
                        <div class="summary-icon summary-icon-warning">
                            <i class="fas fa-warehouse"></i>
                        </div>

                        <div class="summary-content">
                            <div class="summary-label">Balance</div>
                            <div class="summary-value" id="totalBalance">0</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Report Table -->
            <div class="card report-table-card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="fas fa-table"></i>
                        รายละเอียด FG Stock In - Out
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table
                            id="reportTable"
                            class="table table-hover table-bordered w-100">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Lot No.</th>
                                    <th>Part No.</th>
                                    <th>Part Name</th>
                                    <th class="text-right">Qty Plan</th>
                                    <th class="text-right">Stock In</th>
                                    <th class="text-right">Stock Out</th>
                                    <th class="text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div id="reportPagination"></div>
                </div>
            </div>
        </section>

    </main>
</div>



<?php include __DIR__ . "/../../../" . "include/footer.php"; ?>