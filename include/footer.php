<?php /** @var string $baseUrl */ ?>
        <!-- <script>
        window.onerror = function (
            message,
            source,
            line,
            column,
            error
        ) {
            var detail =
                "JavaScript Error\n\n" +
                "Message: " + message + "\n" +
                "File: " + source + "\n" +
                "Line: " + line + "\n" +
                "Column: " + column;

            if (error && error.stack) {
                detail += "\n\nStack:\n" + error.stack;
            }

            alert(detail);

            return false;
        };

        window.addEventListener("unhandledrejection", function (event) {
            var reason = event.reason;

            alert(
                "Promise Error\n\n" +
                (
                    reason && reason.stack
                        ? reason.stack
                        : String(reason)
                )
            );
        });
        </script> -->
        <!-- jQuery -->
        <script src="<?= $baseUrl ?>assets/vendor/jquery/jquery.min.js"></script>

        <!-- Bootstrap 4 Bundle -->
        <script src="<?= $baseUrl ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

        <!-- DataTables -->
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/jquery.dataTables.min.js"></script>
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/dataTables.bootstrap4.min.js"></script>
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/dataTables.responsive.min.js"></script>
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/responsive.bootstrap4.min.js"></script>

        <!-- DataTables Buttons -->
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/dataTables.buttons.min.js"></script>
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/buttons.bootstrap4.min.js"></script>
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/jszip.min.js"></script>
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/buttons.html5.min.js"></script>
        <script src="<?= $baseUrl ?>assets/vendor/datatables/js/buttons.print.min.js"></script>

        <!-- Select2 -->
        <script src="<?= $baseUrl ?>assets/vendor/select2/js/select2.min.js"></script>

        <!-- SweetAlert2 -->
        <script src="<?= $baseUrl ?>assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>

        <!-- Chart.js -->
        <script src="<?= $baseUrl ?>assets/vendor/chartjs/Chart.min.js"></script>

        <!-- Air Datepicker -->
        <script src="<?= $baseUrl ?>assets/js/app-air-datepicker.js"></script>

        <!-- App Animation -->
        <script src="<?= $baseUrl ?>assets/js/app-animation.js?v=<?= time() ?>"></script>

        <!-- Custom JS -->
        <script>
            window.APP_CONFIG = {
                baseUrl: <?= json_encode(isset($baseUrl) ? $baseUrl : "") ?>,
                SIDEBAR_MENU_CACHE_KEY: 'sidebarMenuCache',

            };
        </script>

        <script src="<?= $baseUrl ?>assets/js/app.js?v=<?= time() ?>"></script>
        <script src="<?= $baseUrl ?>assets/js/topbar.js?v=<?= time() ?>"></script>

        <!-- <script>
            alert("getQueryParam: " + typeof window.appAnimation);
        </script> -->


        <?php if (isset($pageScripts) && is_array($pageScripts)): ?>
            <?php foreach ($pageScripts as $script): ?>
                <script src="<?= $baseUrl . htmlspecialchars($script, ENT_QUOTES, "UTF-8") ?>"></script>
            <?php endforeach; ?>
        <?php endif; ?>

    </body>
</html>
