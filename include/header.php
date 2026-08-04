<?php
$baseUrl = isset($baseUrl) ? $baseUrl : '';
$title = isset($title) ? $title : 'Factory System';
$bodyClass = isset($bodyClass) ? $bodyClass : '';
$bodyStyle = isset($bodyStyle) ? $bodyStyle : '';
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/bootstrap/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/fontawesome/css/all.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/datatables/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/datatables/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/datatables/css/buttons.bootstrap4.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/select2/css/select2-bootstrap4.min.css">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/select2/css/select2-bootstrap4.min.css">

    <!-- Air Datepicker -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/vendor/air-datepicker/css/air-datepicker.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/app.css?v=<?= time() ?>">
    <?php if (isset($pageStyles) && is_array($pageStyles)): ?>
        <?php foreach ($pageStyles as $style): ?>
            <link rel="stylesheet" href="<?= $baseUrl . htmlspecialchars($style, ENT_QUOTES, "UTF-8") ?>">
        <?php endforeach; ?>
    <?php endif; ?>

</head>
<body class="<?= htmlspecialchars($bodyClass) ?> page-hidden-overflow" style="<?= htmlspecialchars($bodyStyle) ?>">
<div id="pageLoading" class="page-loading d-none">
    <div class="loading-box">
        <div class="factory-spinner">
            <i class="fas fa-cog"></i>
        </div>
        <div class="loading-text">กำลังโหลดข้อมูล...</div>
    </div>
</div>