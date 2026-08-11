<?php
$baseUrl = "../../";
require_once __DIR__ . "/../../" . "include/auth.php";
$pageTitle = "Vendor Management";
$pageSubtitle = "ค้นหาและจัดการข้อมูลผู้ผลิต";
$pageScripts = array("assets/js/pages/setting/vendorManagement.js?v=" . time());
$pageStyles = array("assets/css/pages/setting/vendorManagement.css");

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
                        <i class="fas fa-industry"></i>
                    </div>
                    <div>
                        <h4 class="mb-1">จัดการผู้ผลิต</h4>
                        <p class="mb-0">ค้นหา เพิ่ม แก้ไข และลบข้อมูลผู้ผลิต</p>
                    </div>
                </div>

                <div class="page-header-actions">
                    <button type="button" class="btn btn-secondary" id="btnAddVendor">
                        <i class="fas fa-plus mr-1"></i>
                        เพิ่มผู้ผลิต
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
                    <form id="vendorFilterForm">
                        <div class="form-row align-items-end">
                            <div class="form-group col-lg-3 col-md-6">
                                <label for="vendorKeyword">คำค้นหา</label>
                                <input type="text" class="form-control" id="vendorKeyword" name="keyword" placeholder="รหัสผู้ผลิต / ชื่อผู้ผลิต">
                            </div>
                            <div class="form-group col-lg-3">
                                <button type="button" class="btn btn-accent mr-2" id="btnSearchVendor">
                                    <i class="fas fa-search mr-1"></i>
                                    ค้นหา
                                </button>
                                <button type="button" class="btn btn-light" id="btnResetVendor">
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
                        รายชื่อผู้ผลิต
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="vendorTable" class="table table-hover table-bordered w-100 entity-table">
                            <thead>
                                <tr>
                                    <th>รหัสผู้ผลิต</th>
                                    <th>ชื่อผู้ผลิต</th>
                                    <th>วันที่สร้าง</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="vendorTableBody">

                            </tbody>
                        </table>
                    </div>
                    <div id="vendorPagination"></div>
                </div>
            </div>

        </section>

    </main>
</div>

<!-- editVendorModal -->
<div class="modal fade" id="editVendorModal" tabindex="-1" role="dialog" aria-labelledby="editVendorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVendorModalLabel">
                    <i class="fas fa-industry mr-2"></i>
                    ผู้ผลิต
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editVendorForm">
                <div class="modal-body">
                    <input type="hidden" id="editVendorId" name="vendorId">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="editVendorCode">รหัสผู้ผลิต</label>
                            <input type="text" class="form-control" id="editVendorCode" name="vendorCode" maxlength="20" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="editVendorName">ชื่อผู้ผลิต</label>
                            <input type="text" class="form-control" id="editVendorName" name="vendorName" maxlength="255" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveVendor">
                        <i class="fas fa-save mr-1"></i>
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php include __DIR__ . "/../../" . "include/footer.php"; ?>