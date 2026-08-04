<?php
$baseUrl = "";
include "include/header.php";
?>

<div class="container mt-4">
    <button class="btn btn-primary" onclick="Swal.fire('OK', 'SweetAlert ใช้ได้', 'success')">
        Test SweetAlert
    </button>

    <hr>
<a href="<?= $baseUrl ?>uploads/1.rar" download>
    ดาวน์โหลดไฟล์
</a>
    <!-- \\192.168.90.250\itdataall\DevelopmentAll
    User = admin
    Pass = !1Tjanome@all -->
</div>

<?php include "include/footer.php"; ?>