<?php
$baseUrl = "../../";
require_once __DIR__ . "/../../" . "include/auth.php";
// $title .= "- User Management";
$pageTitle = "User Management";
$pageSubtitle = "ค้นหาและจัดการรายชื่อผู้ใช้งาน";
$pageScripts = array("assets/js/pages/setting/userManagement.js?v=" . time());
$pageStyles = array("assets/css/pages/setting/userManagement.css");

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
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <div>
                        <h4 class="mb-1">จัดการผู้ใช้งาน</h4>
                        <p class="mb-0">ค้นหา ดูข้อมูล และกรองผู้ใช้งานจากระบบ</p>
                    </div>
                </div>

                <div class="page-header-actions">
                    <button type="button" class="btn btn-secondary" id="btnAddUser">
                        <i class="fas fa-user-plus mr-1"></i>
                        เพิ่มผู้ใช้
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
                    <form id="userFilterForm">
                        <div class="form-row align-items-end">
                            <div class="form-group col-lg-3 col-md-6">
                                <label for="keyword">คำค้นหา</label>
                                <input type="text" class="form-control" id="keyword" name="keyword" placeholder="ชื่อ / อีเมล / Username / รหัสผู้ใช้">
                            </div>
                            <div class="form-group col-lg-3">
                                <button type="button" class="btn btn-accent mr-2" id="btnSearchUser">
                                    <i class="fas fa-search mr-1"></i>
                                    ค้นหา
                                </button>
                                <button type="button" class="btn btn-light" id="btnResetUser">
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
                        รายชื่อผู้ใช้
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="userTable" class="table table-hover table-bordered w-100 entity-table">
                            <thead>
                                <tr>
                                    <th>รหัสพนักงาน</th>
                                    <th>ชื่อ - สกุล</th>
                                    <th>ตำแหน่งงาน</th>
                                    <th>กลุ่ม / สิทธิ์</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="userTableBody">

                            </tbody>
                        </table>
                    </div>
                    <div id="userPagination"></div>
                </div>
            </div>

        </section>

    </main>
</div>

<!-- editUserModal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit mr-2"></i>
                    ผู้ใช้งาน
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editUserForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="editUserId" name="userId">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="editUserCode">รหัสพนักงาน</label>
                            <input type="text" class="form-control" id="editUserCode" name="userCode" maxlength="8" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="editUsername">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="username" maxlength="10" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="editPassword">Password ใหม่</label>
                            <input type="password" class="form-control" id="editPassword" name="password" maxlength="100" autocomplete="new-password" placeholder="เว้นว่างเพื่อไม่เปลี่ยน">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="editNameEn">ชื่อภาษาอังกฤษ (Name EN)</label>
                            <input type="text" class="form-control" id="editNameEn" name="nameEn" maxlength="50">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="editNameTh">ชื่อภาษาไทย (Name TH)</label>
                            <input type="text" class="form-control" id="editNameTh" name="nameTh" maxlength="50">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="editPosition">ตำแหน่งงาน</label>
                            <input type="text" class="form-control" id="editPosition" name="position" maxlength="20">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="editEmail">อีเมล (Email)</label>
                            <input type="email" class="form-control" id="editEmail" name="email" maxlength="50">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="editUserGroup">กลุ่ม / สิทธิ์</label>
                            <select class="form-control" id="editUserGroup" name="userGroup" required>
                                <option value="">เลือก</option>
                                <option value="1">หัวหน้า</option>
                                <option value="0">พนักงาน</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="editStatusCode">สถานะ</label>
                            <select class="form-control" id="editStatusCode" name="statusCode" required>
                                <option value="">เลือก</option>
                                <option value="Y">ใช้งาน</option>
                                <option value="N">ไม่ได้ใช้งาน</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>รูปโปรไฟล์ (User Image)</label>
                            <div class="upload-tile-wrapper">
                                <label class="upload-tile" for="editUserImage">
                                    <img id="editUserImagePreview" class="upload-preview-image d-none" alt="User Image Preview">
                                    <div id="editUserImagePlaceholder" class="upload-placeholder">
                                        <i class="fas fa-image"></i>
                                        <span>คลิกเพื่ออัปโหลดรูป</span>
                                    </div>
                                    <div class="upload-overlay">
                                        <span id="editUserImageOverlayText">อัปโหลดรูป</span>
                                    </div>
                                </label>
                                <input type="file" id="editUserImage" name="userImage" accept="image/*" class="d-none">
                            </div>
                            <small class="form-text text-muted" id="currentUserImageText">ไฟล์ปัจจุบัน: -</small>
                        </div>
                        <div class="form-group col-md-6">
                            <label>ลายเซ็น (User Signature)</label>
                            <div class="upload-tile-wrapper">
                                <label class="upload-tile" for="editUserSignature">
                                    <img id="editUserSignaturePreview" class="upload-preview-image d-none" alt="User Signature Preview">
                                    <div id="editUserSignaturePlaceholder" class="upload-placeholder">
                                        <i class="fas fa-signature"></i>
                                        <span>คลิกเพื่ออัปโหลดลายเซ็น</span>
                                    </div>
                                    <div class="upload-overlay">
                                        <span id="editUserSignatureOverlayText">อัปโหลดลายเซ็น</span>
                                    </div>
                                </label>
                                <input type="file" id="editUserSignature" name="userSignature" accept="image/*" class="d-none">
                            </div>
                            <small class="form-text text-muted" id="currentUserSignatureText">ไฟล์ปัจจุบัน: -</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveUser">
                        <i class="fas fa-save mr-1"></i>
                        บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php include __DIR__ . "/../../" . "include/footer.php"; ?>