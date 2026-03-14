
<footer class="footer text-center">
    All Rights Reserved by Endeavour Solution Nigeria Limited
    <a href="#">On The Clock</a>.
</footer>
<!-- ============================================================== -->
<!-- End footer -->
<!-- ============================================================== -->
</div>
<!-- ============================================================== -->
<!-- End Page wrapper  -->
<!-- ============================================================== -->
</div>

<!-- ============================================================== -->
<!-- End Wrapper -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="dist/js/jquery-3.6.0.min.js"></script>
<?php
$pageKey = basename($_SERVER['PHP_SELF']);
$username = $_SESSION[$session_variable . "username"];

if (isset($_SESSION['cached_footer'][$username][$pageKey])) {
    echo $_SESSION['cached_footer'][$username][$pageKey];
    return;
}

ob_start();
?>
<script src="assets/extra-libs/DataTables/datatables.min.js"></script>
<script src="dist/js/select2.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(function () {
            $('select.select2').each(function () {
                if (!$(this).next('.select2-container').length) {
                    $(this).select2({
                        width: '100%' // or 'resolve'
                    });
                }
            });
        }, 100); // Slight delay ensures it works on dynamically loaded content
    });
</script>

<!-- Bootstrap tether Core JavaScript -->
<script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js" defer></script>
<script src="assets/extra-libs/sparkline/sparkline.js" defer></script>
<!--Wave Effects--> 
<script src="dist/js/waves.js" defer></script>
<!--Menu sidebar--> 
<script src="dist/js/sidebarmenu.js" defer></script>
<!--Custom JavaScript--> 
<script src="dist/js/custom.min.js" defer></script>
<!--This page JavaScript--> 
 <!--<script src="dist/js/pages/dashboards/dashboard1.js"></script>--> 

<!--Charts js Files--> 
<?php if ($pageKey == 'Dashboard.php') { ?>
    <script src="assets/libs/chart/matrix.interface.js" defer></script>
    <script src="assets/libs/chart/excanvas.min.js" defer></script>
    <script src="assets/libs/flot/excanvas.js" defer></script>
    <script src="assets/libs/flot/jquery.flot.js" defer></script>
    <script src="assets/libs/flot/jquery.flot.pie.js" defer></script>
    <script src="assets/libs/flot/jquery.flot.time.js" defer></script>
    <script src="assets/libs/flot/jquery.flot.stack.js" defer></script>
    <script src="assets/libs/flot/jquery.flot.crosshair.js" defer></script>
    <script src="assets/libs/chart/jquery.peity.min.js" defer></script>
    <script src="assets/libs/chart/matrix.charts.js" defer></script>

    <!--<script src="assets/libs/chart/jquery.flot.pie.min.js"></script>-->
    <script src="assets/libs/flot.tooltip/js/jquery.flot.tooltip.min.js" defer></script>
    <script src="dist/js/pages/chart/chart-page-init.js"></script>
    <script src="js/chart.js"></script>
<?php } ?>
<!--<script src="assets/extra-libs/DataTables/datatables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>-->

<!-- DataTables: load CDN then fallback to local -->
<!--<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script>
  if (typeof $.fn.dataTable === 'undefined') {
    document.write('<script src="assets/extra-libs/DataTables/datatables.min.js"><\/script>');
  }
</script>-->
<script src="assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.js" defer></script>
<?php
$_SESSION['cached_footer'][$username][$pageKey] = ob_get_clean();
echo $_SESSION['cached_footer'][$username][$pageKey];
//include "cached_footer.php"; 
?>

<!--<script src="assets/libs/jquery/dist/jquery.min.js"></script>-->
<!-- Add Select2 CSS -->
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->

<script>
    $(document).ready(function () {
        // Initialize DataTable  
        $('#zero_config').DataTable({
            paging: true,
            searching: true,
            info: true,
            autoWidth: false,
            order: [],
            lengthMenu: [[50, 100, 25, 10], [50, 100, 25, 10]],
            columnDefs: [
                {orderable: false, targets: 0}
            ]
        });

        // Initialize Select2  
        $('.select2').select2({
            tags: true, // Enable adding new options by typing  
            placeholder: function () {
                return $(this).data('Select'); // Use data attribute for custom placeholders  
            },
            allowClear: true, // Allow clearing the selection  
            width: '100%' // Make the select box full width  
        });
    });
</script>
</body>
</html>
