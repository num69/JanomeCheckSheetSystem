<?php
$baseUrl = "../../";
require_once __DIR__ . "/../../" . "include/auth.php";
$pageTitle = "Part Management";
$pageSubtitle = "ค้นหาและจัดการข้อมูลชิ้นส่วน";
$pageScripts = array("assets/js/pages/setting/partManagement.js?v=" . time());
$pageStyles = array("assets/css/pages/setting/partManagement.css");

include __DIR__ . "/../../" . "include/header.php";
?>

<div class="app-layout">

    <?php include __DIR__ . "/../../" . "include/sidebar.php"; ?>

    <main class="app-main">
        <?php include __DIR__ . "/../../" . "include/topbar.php"; ?>

        <section class="content-area page-animated">
            <div class="page-header">
                <div class="page-header-title">
                    <div class="page-header-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <div>
                        <h4 class="mb-1">จัดการชิ้นส่วน</h4>
                        <p class="mb-0">ค้นหา เพิ่ม แก้ไข และลบข้อมูลชิ้นส่วน</p>
                    </div>
                </div>

                <div class="page-header-actions">
                    <button type="button" class="btn btn-secondary" id="btnAddPart">
                        <i class="fas fa-plus mr-1"></i>
                        เพิ่มชิ้นส่วน
                    </button>
                </div>
            </div>

            <div class="card filter-card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="fas fa-filter"></i>
                        ค้นหาและกรองข้อมูล
                    </div>
                </div>
                <div class="card-body">
                    <form id="partFilterForm">
                        <div class="form-row align-items-end">
                            <div class="form-group col-lg-4 col-md-6">
                                <label for="partKeyword">คำค้นหา</label>
                                <input type="text" class="form-control" id="partKeyword" name="keyword" placeholder="รหัสชิ้นส่วน / ชื่อชิ้นส่วน">
                            </div>
                            <div class="form-group col-lg-4">
                                <button type="button" class="btn btn-accent mr-2" id="btnSearchPart">
                                    <i class="fas fa-search mr-1"></i>
                                    ค้นหา
                                </button>
                                <button type="button" class="btn btn-light" id="btnResetPart">
                                    <i class="fas fa-undo-alt mr-1"></i>
                                    ล้าง
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card entity-table-card">
                <div class="card-header">
                    <div class="card-header-title">
                        <i class="fas fa-table"></i>
                        รายการชิ้นส่วน
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="partTable" class="table table-hover table-bordered w-100 entity-table">
                            <thead>
                                <tr>
                                    <th>รหัสชิ้นส่วน</th>
                                    <th>ชื่อชิ้นส่วน</th>
                                    <th>วันที่สร้าง</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="partTableBody"></tbody>
                        </table>
                    </div>
                    <div id="partPagination"></div>
                </div>
            </div>

        </section>

    </main>
</div>

<div class="modal fade" id="editPartModal" tabindex="-1" role="dialog" aria-labelledby="editPartModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPartModalLabel">
                    <i class="fas fa-cogs mr-2"></i>
                    ชิ้นส่วน
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPartForm">
                <div class="modal-body">
                    <input type="hidden" id="editPartId" name="partId">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="editPartCode">รหัสชิ้นส่วน</label>
                            <input type="text" class="form-control" id="editPartCode" name="partCode" maxlength="20" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="editPartName">ชื่อชิ้นส่วน</label>
                            <input type="text" class="form-control" id="editPartName" name="partName" maxlength="255" required>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSavePart">
                        <i class="fas fa-save mr-1"></i>
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../" . "include/footer.php"; ?>
