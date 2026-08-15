<?php
/** @var string $baseUrl */
$pageTitle = isset($pageTitle) ? $pageTitle : "";
$pageSubtitle = isset($pageSubtitle) ? $pageSubtitle : "";
$nameTh = isset($_SESSION["user"]["name_th"]) ? trim((string) $_SESSION["user"]["name_th"]) : "";
$nameEn = isset($_SESSION["user"]["name_en"]) ? trim((string) $_SESSION["user"]["name_en"]) : "";
$username = isset($_SESSION["user"]["username"]) ? trim((string) $_SESSION["user"]["username"]) : "";
$displayName = $nameTh !== "" ? $nameTh : ($nameEn !== "" ? $nameEn : ($username !== "" ? $username : "Unknown User"));
$userGroup = isset($_SESSION["user"]["group"]) ? trim((string) $_SESSION["user"]["group"]) : "";
$role = $userGroup === "1" ? "หัวหน้างาน" : ($userGroup === "0" ? "พนักงาน" : "ผู้ใช้งาน");
$profilePath = isset($_SESSION["user"]["image"]) ? trim((string) $_SESSION["user"]["image"]) : "";
$profileFilename = $profilePath !== "" ? basename(str_replace("\\", "/", $profilePath)) : "";
$profileUrl = $profileFilename !== "" ? $baseUrl . "uploads/profile/" . rawurlencode($profileFilename) : "";
$signaturePath = isset($_SESSION["user"]["signature"]) ? trim((string) $_SESSION["user"]["signature"]) : "";
$signatureFilename = $signaturePath !== "" ? basename(str_replace("\\", "/", $signaturePath)) : "";
$signatureUrl = $signatureFilename !== "" ? $baseUrl . "uploads/signature/" . rawurlencode($signatureFilename) : "";
$userCode = isset($_SESSION["user"]["code"]) ? $_SESSION["user"]["code"] : "-";
$position = isset($_SESSION["user"]["position"]) ? trim((string) $_SESSION["user"]["position"]) : "";
$email = isset($_SESSION["user"]["email"]) ? trim((string) $_SESSION["user"]["email"]) : "";

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
                    <div class="font-weight-bold js-current-display-name">
                        <?= htmlspecialchars($displayName) ?>
                    </div>
                    <span class="badge topbar-role-badge <?= $userGroup === "1" ? "badge-primary" : "badge-secondary" ?>">
                        <?= htmlspecialchars($role) ?>
                    </span>
                </div>

                <span class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center user-avatar">
                    <?php if ($profileUrl !== ""): ?>
                        <img src="<?= htmlspecialchars($profileUrl) ?>" class="user-avatar-image js-topbar-profile-image"
                            alt="รูปโปรไฟล์ของ <?= htmlspecialchars($displayName) ?>"
                            onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                        <i class="fas fa-user d-none" aria-hidden="true"></i>
                    <?php else: ?>
                        <img src="" class="user-avatar-image js-topbar-profile-image d-none" alt="รูปโปรไฟล์ของ <?= htmlspecialchars($displayName) ?>">
                        <i class="fas fa-user" aria-hidden="true"></i>
                    <?php endif; ?>
                </span>
            </button>

            <div class="dropdown-menu dropdown-menu-right shadow topbar-user-menu"
                aria-labelledby="userDropdown">

                <div class="dropdown-header topbar-user-menu-header">
                    <span class="topbar-user-menu-kicker"><i class="fas fa-user-circle mr-1"></i>บัญชีผู้ใช้งาน</span>
                    <strong class="js-current-display-name"><?= htmlspecialchars($displayName) ?></strong><br>
                    <span class="badge topbar-role-badge <?= $userGroup === "1" ? "badge-primary" : "badge-secondary" ?>">
                        <?= htmlspecialchars($role) ?>
                    </span>
                </div>

                <div class="dropdown-divider"></div>

                <button type="button" class="dropdown-item topbar-menu-action" data-toggle="modal" data-target="#profileModal">
                    <span class="topbar-menu-icon"><i class="fas fa-id-card"></i></span>
                    <span>Profile<small>ดูและแก้ไขข้อมูลส่วนตัว</small></span>
                </button>

                <button type="button" class="dropdown-item topbar-menu-action" data-toggle="modal" data-target="#changePasswordModal">
                    <span class="topbar-menu-icon"><i class="fas fa-key"></i></span>
                    <span>Change Password<small>เปลี่ยนรหัสผ่านบัญชี</small></span>
                </button>

                <div class="dropdown-divider"></div>

                <a class="dropdown-item topbar-menu-action topbar-logout-action text-danger"
                    href="<?= htmlspecialchars($baseUrl) ?>logout.php">
                    <span class="topbar-menu-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span>Logout<small>ออกจากระบบ</small></span>
                </a>
            </div>
        </div>

    </div>
</header>

<div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content profile-modal-content">
            <div class="profile-modal-hero">
                <button type="button" class="close profile-modal-close" data-dismiss="modal" aria-label="ปิด">
                    <span aria-hidden="true">&times;</span>
                </button>
                <label class="profile-modal-avatar profile-avatar-upload" for="ownProfileImage" title="คลิกเพื่อเปลี่ยนรูปโปรไฟล์">
                    <img id="ownProfileImagePreview" class="<?= $profileUrl === "" ? "d-none" : "" ?>" src="<?= htmlspecialchars($profileUrl) ?>" alt="รูปโปรไฟล์ของ <?= htmlspecialchars($displayName) ?>"
                        onerror="this.classList.add('d-none'); this.nextElementSibling.classList.remove('d-none');">
                    <i class="fas fa-user <?= $profileUrl !== "" ? "d-none" : "" ?>" aria-hidden="true"></i>
                    <span class="profile-avatar-upload-hint"><i class="fas fa-camera" aria-hidden="true"></i></span>
                </label>
                <div class="profile-modal-group">
                    <span class="badge profile-group-badge <?= $userGroup === "1" ? "profile-group-badge-supervisor" : "profile-group-badge-employee" ?>">
                        <?= htmlspecialchars($role) ?>
                    </span>
                </div>
                <h5 class="mb-1 js-current-display-name" id="profileModalLabel"><?= htmlspecialchars($displayName) ?></h5>
                <div class="profile-modal-position" id="profileHeroPosition"><?= htmlspecialchars($position ?: "-") ?></div>
            </div>
            <form id="editOwnProfileForm">
                <div class="modal-body profile-detail-grid">
                    <input type="file" class="d-none" id="ownProfileImage" name="userImage" accept="image/jpeg,image/png,image/gif,image/webp">
                    <div class="form-group mb-0"><label for="profileUserCode">รหัสพนักงาน</label><input type="text" class="form-control" id="profileUserCode" value="<?= htmlspecialchars($userCode ?: "-") ?>" readonly></div>
                    <div class="form-group mb-0"><label for="profileUsername">Username</label><input type="text" class="form-control" id="profileUsername" value="<?= htmlspecialchars($username ?: "-") ?>" readonly></div>
                    <div class="form-group mb-0"><label for="profileNameTh">ชื่อภาษาไทย</label><input type="text" class="form-control" id="profileNameTh" name="nameTh" maxlength="50" value="<?= htmlspecialchars($nameTh) ?>"></div>
                    <div class="form-group mb-0"><label for="profileNameEn">ชื่อภาษาอังกฤษ</label><input type="text" class="form-control" id="profileNameEn" name="nameEn" maxlength="50" value="<?= htmlspecialchars($nameEn) ?>"></div>
                    <div class="form-group mb-0"><label for="profilePosition">ตำแหน่ง</label><input type="text" class="form-control" id="profilePosition" name="position" maxlength="20" value="<?= htmlspecialchars($position) ?>"></div>
                    <div class="form-group mb-0"><label for="profileEmail">Email</label><input type="email" class="form-control" id="profileEmail" name="email" maxlength="50" value="<?= htmlspecialchars($email) ?>"></div>
                    <div class="profile-upload-field profile-signature-field">
                        <label>ลายเซ็น</label>
                        <label class="profile-upload-box profile-signature-box" for="ownProfileSignature">
                            <img id="ownProfileSignaturePreview" class="<?= $signatureUrl === "" ? "d-none" : "" ?>" src="<?= htmlspecialchars($signatureUrl) ?>" alt="ตัวอย่างลายเซ็น">
                            <span id="ownProfileSignaturePlaceholder" class="<?= $signatureUrl !== "" ? "d-none" : "" ?>"><i class="fas fa-signature"></i> เลือกลายเซ็น</span>
                        </label>
                        <input type="file" class="d-none" id="ownProfileSignature" name="userSignature" accept="image/jpeg,image/png,image/gif,image/webp">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveOwnProfile"><i class="fas fa-save mr-1"></i>บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content password-modal-content">
            <form id="changeOwnPasswordForm">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <div class="password-modal-icon"><i class="fas fa-key" aria-hidden="true"></i></div>
                        <h5 class="modal-title mt-3" id="changePasswordModalLabel">เปลี่ยนรหัสผ่าน</h5>
                        <small class="text-muted">ตั้งรหัสผ่านใหม่สำหรับบัญชีของคุณ</small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="ปิด"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="currentPassword">รหัสผ่านปัจจุบัน</label>
                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" maxlength="100" autocomplete="current-password" required>
                    </div>
                    <div class="form-group">
                        <label for="newPassword">รหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="newPassword" name="newPassword" maxlength="100" autocomplete="new-password" required>
                    </div>
                    <div class="form-group mb-0">
                        <label for="confirmPassword">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" maxlength="100" autocomplete="new-password" required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="btnChangeOwnPassword">
                        <i class="fas fa-save mr-1"></i>บันทึกรหัสผ่าน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
