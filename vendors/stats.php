<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: login.php");
}

if (isset($_GET['logout']) || strtolower($_SESSION["role"]) != "vendors") {
    session_destroy();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header('Location: ../login.php');
}
?>
<?php
require_once('../bootstrap.php');

use Src\Controller\AdminController;

$admin = new AdminController();
require_once('../inc/page-data.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= require_once("../inc/head.php") ?>
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Daily Transactions</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Daily Transactions</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">

            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        <?php
                        $summary = $admin->fetchVendorSummary($_SESSION["vendor_id"]);
                        $admissionInfo = $admin->fetchCurrentAdmissionPeriod();
                        ?>

                        <div class="card-body">
                            <h5 class="card-title">Summary (<?= $admissionInfo[0]["info"] ?>)</h5>

                            <!-- Form Types -->
                            <div class="form-types">
                                <div class="row">
                                    <?php foreach ($summary["form-types"] as $form) { ?>
                                        <!-- Masters Card -->
                                        <div class="col-xxl-4 col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 style="font-size: 18px; font-weight: 650; margin-top:20px"><?= $form["name"] ?></h6>
                                                    <div class="mt-2" style="display:flex; justify-content:space-between">

                                                        <div style="display: flex; flex-direction:column; justify-content:flex-start">
                                                            <span style="font-size: 16px;"><?= $form["total_num"] ?></span>
                                                            <span class="text-muted small">COUNT</span>
                                                        </div>

                                                        <div style="display: flex; flex-direction:column; justify-content:flex-start">
                                                            <h5 style="padding-bottom: 0; margin-bottom:0;">
                                                                <span class="small">GH</span>&#162;<span class="small"><?= $form["total_amount"] ? $form["total_amount"] : "0.00" ?></span>
                                                            </h5>
                                                            <span class="text-muted small">AMOUNT</span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- End Masters Card -->
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div><!-- End Transactions Summary row -->

        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>
    <script>
        $("dataTable-top").hide();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on({
                ajaxStart: function() {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    // Hide it after 3 seconds
                    $.LoadingOverlay("hide");
                }
            });
        });
    </script>
</body>

</html>