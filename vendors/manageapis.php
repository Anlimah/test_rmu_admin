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

                        <?php $vendorAPIData = $admin->fetchVendorAPIData($_SESSION["vendor_id"]); ?>

                        <div class="card-body">
                            <h5 class="card-title">API Keys</h5>

                            <!-- Form Types -->
                            <div style="display: flex; justify-content: space-between;">
                                <div>
                                    <p>Use your test API keys to test your program</p>
                                </div>
                                <div>
                                    <button class="btn btn-primary btn-sm" id="generateNewAPIKeys" style="padding: 10px 30px">GENERATE NEW API KEYS</button>
                                </div>
                            </div>
                            <table class="table" style="margin-top: 50px">
                                <thead style="width:120px; background-color: #f1f1f1">
                                    <th>Status</th>
                                    <th>Vendor ID</th>
                                    <th>Date</th>
                                </thead>
                                <tbody class="table">
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
            <div class="modal fade" id="purchaseInfoModal" tabindex="-1" aria-labelledby="purchaseInfoModal" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="purchaseInfoModalTitle">Generated API Keys</h1>
                        </div>
                        <div class="modal-body">
                            <div> </div>
                            <table class="table">
                                <tr>
                                    <td>Vendor ID: </td>
                                    <td>ahsdohasdhah</td>
                                </tr>
                                <tr>
                                    <td>Vendor Secret: </td>
                                    <td>ahsdohasdhah</td>
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

            // when 
            $(".form-select, .form-control").change("blur", function(e) {
                e.preventDefault();
                $("#reportsForm").submit();
            });

            $("#reportsForm").on("submit", function(e, d) {
                e.preventDefault();
                triggeredBy = 1;

                $.ajax({
                    type: "POST",
                    url: "../endpoint/vendorSalesReport",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("#totalData").text(result.message.length);
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                $("tbody").append(
                                    '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + value.added_at + '</td>' +
                                    '<td>' + value.id + '</td>' +
                                    '<td>' + value.fullName + '</td>' +
                                    '<td>' + value.phoneNumber + '</td>' +
                                    '<td>' + value.admissionPeriod + '</td>' +
                                    '<td>' + value.formType + '</td>' +
                                    '<td>' + value.status + '</td>' +
                                    '<td>' +
                                    '<button id="' + value.id + '" class="btn btn-xs btn-primary openPurchaseInfo" data-bs-toggle="modal" data-bs-target="#purchaseInfoModal">View</button>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                            $("#totalData").text(0);
                            $("tbody").html("<tr style='text-align: center'><td colspan='9'>" + result.message + "</td></tr>");
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(document).on("click", ".openPurchaseInfo", function() {
                triggeredBy = 2;
                let data = {
                    _data: $(this).attr("id")
                }

                $.ajax({
                    type: "POST",
                    url: "../endpoint/purchaseInfo",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("#p-transID").val(result.message[0].transID);
                            $("#p-admisP").val(result.message[0].admisP);
                            $("#p-name").val(result.message[0].fullName);
                            $("#p-country").val(result.message[0].country);
                            $("#p-email").val(result.message[0].email);
                            $("#p-phoneN").val(result.message[0].phoneN);
                            $("#p-appN").val(result.message[0].appN);
                            $("#p-pin").val(result.message[0].pin);
                            $("#p-status").val(result.message[0].status);
                            $("#p-vendor").val(result.message[0].vendor);
                            $("#p-formT").val(result.message[0].formT);
                            $("#p-payM").val(result.message[0].payM);
                            $("#sendTransID").val(result.message[0].transID);
                            $("#genSendTransID").val(result.message[0].transID);
                            $("#printVoucher").prop("href", "print-form.php?exttrid=" + result.message[0].transID);
                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                            alert(result.message);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $("#genSendPurchaseInfoForm").on("submit", function(e) {
                e.preventDefault();

                var confirmMsg = confirm("Please note that applicant current progress on the application portal will be lost after new login info are generated! Click OK to continue.");
                if (!confirmMsg) return;

                triggeredBy = 3;
                $.ajax({
                    type: "POST",
                    url: "../endpoint/gen-send-purchase-info",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result);

                        $("#msgContent").text(result.message);
                        if (result.success) {
                            $(".infoFeed").removeClass("alert-danger").addClass("alert-success");
                            $(".infoFeed").fadeIn("slow", function() {
                                $(".infoFeed").fadeOut(5000);
                            });
                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                            $(".infoFeed").removeClass("alert-success").addClass("alert-danger");
                            $(".infoFeed").fadeIn("slow", function() {
                                $(".infoFeed").fadeOut(5000);
                            });
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $("#sendPurchaseInfoForm").on("submit", function(e) {
                e.preventDefault();
                triggeredBy = 4;
                $.ajax({
                    type: "POST",
                    url: "../endpoint/send-purchase-info",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result);

                        $("#msgContent").text(result.message);
                        if (result.success) {
                            $(".infoFeed").removeClass("alert-danger").addClass("alert-success").fadeOut(3000);
                            $(".infoFeed").fadeIn("slow", function() {
                                $(".infoFeed").fadeOut(5000);
                            });
                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                            $(".infoFeed").removeClass("alert-success").addClass("alert-danger");
                            $(".infoFeed").fadeIn("slow", function() {
                                $(".infoFeed").fadeOut(5000);
                            });
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