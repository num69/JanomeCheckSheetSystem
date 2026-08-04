<?php
$baseUrl = "../../";
include "../../include/header.php";
?>

<div class="container-fluid mt-3">

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-cogs mr-2"></i>
                Machine Status
            </h5>
        </div>

        <div class="card-body">
            <button class="btn btn-primary mb-3" id="btnAdd">
                <i class="fas fa-plus mr-1"></i>
                Add
            </button>

            <table id="statusTable" class="table table-bordered table-striped table-sm dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status Name</th>
                        <th>Color</th>
                        <th>Active</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Running</td>
                        <td><span class="badge badge-success">Green</span></td>
                        <td>Y</td>
                        <td>
                            <button class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm btn-delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

</div>

<script>
$(function () {
    $('#statusTable').DataTable({
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        language: {
            search: "ค้นหา:",
            lengthMenu: "แสดง _MENU_ รายการ",
            info: "แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            paginate: {
                previous: "ก่อนหน้า",
                next: "ถัดไป"
            },
            zeroRecords: "ไม่พบข้อมูล"
        }
    });

    $('.btn-delete').on('click', function () {
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'ข้อมูลนี้จะถูกลบออกจากระบบ',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ลบ',
            cancelButtonText: 'ยกเลิก'
        }).then(function (result) {
            if (result.isConfirmed) {
                Swal.fire('สำเร็จ', 'ลบข้อมูลเรียบร้อย', 'success');
            }
        });
    });
});
</script>

<?php include "../../include/footer.php"; ?>