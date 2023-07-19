<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: ../index.php");
}


if (isset($_GET['logout']) || strtolower($_SESSION["role"]) != "accounts") {
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

    header('Location: ../index.php');
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
            <h1>Dashboard</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <a href="daily-transactions.php">
                                <div class="card info-card sales-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Reports</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <img src="../assets/img/icons8-payment-history-96.png" style="width: 48px;" alt="">
                                            </div>
                                            <div class="ps-3">
                                                <span class="text-muted small pt-2 ps-1">Sales summary & reports</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <a href="vendors-transactions.php">
                                <div class="card info-card sales-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Vendors Stats</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <img src="../assets/img/icons8-chart-96.png" style="width: 48px;" alt="">
                                            </div>
                                            <div class="ps-3">
                                                <span class="text-muted small pt-2 ps-1">Vendors' sales summary & reports</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <a href="vendors-account.php">
                                <div class="card info-card sales-card">
                                    <div class="card-body">
                                        <h5 class="card-title">Vendors Account</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <img src="../assets/img/icons8-male-female-user-group-96.png" style="width: 48px;" alt="">
                                            </div>
                                            <div class="ps-3">
                                                <span class="text-muted small pt-2 ps-1">Add, edit, and remove vendors account</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <a href="settings.php">
                                        <h5 class="card-title">Forms</h5>
                                        <div class="d-flex align-items-center">
                                            <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                <img src="../assets/img/icons8-documents-96.png" style="width: 48px;" alt="">
                                            </div>
                                            <div class="ps-3">
                                                <span class="text-muted small pt-2 ps-1">Add, edit, and remove forms</span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                    </div>
                </div><!-- Forms Sales Card  -->

            </div><!-- End Left side columns -->

            <!-- Right side columns -->
            <!-- End Right side columns -->

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