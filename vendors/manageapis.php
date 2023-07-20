<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: index.php");
}

if (isset($_GET['logout']) || !isset($_SESSION["api_user"]) || strtolower($_SESSION["role"]) != "vendors") {
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

$vendor_id = isset($_SESSION["vendor_id"]) ? $_SESSION["vendor_id"] : "";

$_SESSION["lastAccessed"] = time();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= require_once("../inc/head.php") ?>
    <style>
        .btn-group-xs>.btn,
        .btn-xs {
            padding: 1px 5px !important;
            font-size: 12px !important;
            line-height: 1.5 !important;
            border-radius: 3px !important;
        }
    </style>
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Manage API Keys</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Manage API Keys</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">

            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        <?php $vendorAPIData = $admin->fetchVendorAPIData($vendor_id); ?>

                        <div class="card-body">
                            <h5 class="card-title">API Keys</h5>

                            <!-- Form Types -->
                            <div style="display: flex; justify-content: space-between;">
                                <div>
                                    <p>Generate new API keys when ever necessary</p>
                                </div>
                                <form method="POST">
                                    <button type="submit" class="btn btn-primary btn-sm" id="generateNewAPIKeys" style="padding: 10px 30px">GENERATE NEW API KEYS</button>
                                    <input type="hidden" name="__generateAPIKeys" value="<?= isset($vendor_id) ? sha1($vendor_id) : "" ?>">
                                </form>
                            </div>
                            <table class="table" style="margin-top: 50px">
                                <thead style="width:120px; background-color: #f1f1f1">
                                    <th scope="col">Status</th>
                                    <th scope="col">Vendor ID</th>
                                    <th scope="col">Date</th>
                                </thead>
                                <tbody>
                                    <?php if (!empty($vendorAPIData)) { ?>
                                        <tr>
                                            <td><span class="btn btn-success btn-xs">Active</span></td>
                                            <td><?= $vendorAPIData[0]["username"] ?></td>
                                            <td><?= $vendorAPIData[0]["added_at"] ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!-- End Transactions Summary row -->

            <!-- Purchase info Modal -->
            <div class="modal fade" id="genratedAPIKeysModal" tabindex="-1" aria-labelledby="genratedAPIKeysModal" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="genratedAPIKeysModalTitle">Generated API Keys</h1>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-success">
                                <p>Please copy the <b>VENDOR_ID</b> and <b>VENDOR_SECRET</b> and store it in a safe location.</p>
                                <p>NB: <b>VENDOR_SECRET</b> won't be available when you close this message.</p>
                            </div>
                            <table class="table">
                                <tr>
                                    <th scope="row" style="width:120px; background-color: #f1f1f1"><b>VENDOR_ID</b>: </th>
                                    <td id="vendorID"></td>
                                </tr>
                                <tr>
                                    <th scope="row" style="width:120px; background-color: #f1f1f1"><b>VENDOR_SECRET</b>: </th>
                                    <td id="vendorSecret"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


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

            var triggeredBy = 0;

            $("#generateNewAPIKeys").on("submit", function(e, d) {
                e.preventDefault();
                triggeredBy = 1;

                $.ajax({
                    type: "POST",
                    url: "../endpoint/generateNewAPIKeys",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            $("#clientID").text(result.message[0].client_id);
                            $("#clientSecret").text(result.message[0].client_secret);
                            $("#genratedAPIKeysModal").toggle("modal");
                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 3) $("#genSendTransIDBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> processing...');
                    else if (triggeredBy == 4) $("#sendTransIDBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> processing...');
                    else $("#alert-output").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 3) $("#genSendTransIDBtn").prop("disabled", false).html('Generate and send new application login info');
                    else if (triggeredBy == 4) $("#sendTransIDBtn").prop("disabled", false).html('Resend application login info');
                    else $("#alert-output").html('');
                }
            });
        });
    </script>
</body>

</html>