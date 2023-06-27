<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: login.php");
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
    <?php require_once("../inc/head.php") ?>
    <style>
        ._textD {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php require_once("../inc/header.php") ?>

    <?php require_once("../inc/sidebar.php") ?>

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
                        $summary = $admin->fetchInitialSummaryRecord();

                        if (!empty($summary)) {
                            //collections
                            $collect_total = $summary["collections"]["collect"]["total_num"] ? $summary["collections"]["collect"]["total_num"] : "0";
                            $collect_amount = $summary["collections"]["collect"]["total_amount"] ? $summary["collections"]["collect"]["total_amount"] : "0.00";

                            $vendor_total = $summary["collections"]["vendor"]["total_num"] ? $summary["collections"]["vendor"]["total_num"] : "0";
                            $vendor_amount = $summary["collections"]["vendor"]["total_amount"] ? $summary["collections"]["vendor"]["total_amount"] : "0.00";

                            $online_total = $summary["collections"]["online"]["total_num"] ? $summary["collections"]["online"]["total_num"] : "0";
                            $online_amount = $summary["collections"]["online"]["total_amount"] ? $summary["collections"]["online"]["total_amount"] : "0.00";

                            $provider_total = $summary["collections"]["provider"]["total_num"] ? $summary["collections"]["provider"]["total_num"] : "0";
                            $provider_amount = $summary["collections"]["provider"]["total_amount"] ? $summary["collections"]["provider"]["total_amount"] : "0.00";

                        ?>

                            <div class="card-body">
                                <p class="card-title">Summary</p>

                                <!-- Transactions cards-->
                                <div class="transactions">
                                    <div class="row">

                                        <?php
                                        $total_trans = (int) $summary["transactions"][0]["total"];
                                        foreach ($summary["transactions"] as $transaction) {

                                            $status = isset($transaction["status"]) ?  $transaction["status"] : "TOTAL";

                                            $status_color = match ($status) {
                                                "TOTAL" => "info",
                                                "COMPLETED" => "success",
                                                "PENDING" => "warning",
                                                "FAILED" => "danger"
                                            };

                                            $trans = $transaction["total"] ? $transaction["total"] : 0;
                                            $trans_percent = $total_trans ? ($trans / $total_trans) * 100 : $total_trans;
                                        ?>

                                            <!-- Pending Transactions Card -->
                                            <div class="col-xxl-3 col-md-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h5 class="card-title" style="font-size: 22px; margin-bottom: 0 !important; padding-bottom: 5px !important; font-weight:300 !important "><?= $trans ?></h5>
                                                        <h6 style="font-size: 18px; font-weight: 650;"><?= $status ?> Transactions</h6>
                                                        <div class="progress mb-2 mt-2" role="progressbar" aria-label="Info example" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar bg-<?= $status_color ?>" style="width: <?= $trans_percent ?>%"></div>
                                                        </div>
                                                        <span class="text-muted mt-4">Daily <?= $status ?> transactions</span>
                                                    </div>
                                                </div>
                                            </div><!-- End Pending Transactions Card -->

                                        <?php
                                        }
                                        ?>

                                    </div>
                                </div>

                                <!-- Collections -->
                                <div class="collections">
                                    <div class="row">

                                        <!-- Successful Collections Card -->
                                        <div class="col-xxl-3 col-md-3">
                                            <div class="card info-card sales-card revenue-card">
                                                <div class="card-body">
                                                    <h6 style="font-size: 18px !important; margin: 20px 0; color: #444 ">Successful Collections</h6>

                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <i class="bi bi-currency-dollar"></i>
                                                        </div>
                                                        <div class="ps-3">
                                                            <h5><span class="small">GH</span>&#162;<span><?= $collect_amount ?></span></h5>
                                                            <span class="text-muted pt-1">COUNT: </span>
                                                            <span class="pt-2 ps-1" style="font-size: 16px;"><?= $collect_total ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- End Successful Collections Card -->

                                        <!-- Vendors Collections Card -->
                                        <div class="col-xxl-3 col-md-3">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <h6 style="font-size: 18px !important; margin: 20px 0; color: #444">Vendors Collections</h6>

                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <img src="../assets/img/icons8-sell-48.png" style="width: 48px;" alt="">
                                                        </div>
                                                        <div class="ps-3">
                                                            <h5><span class="small">GH</span>&#162;<span><?= $vendor_amount ?></span></h5>
                                                            <span class="text-muted pt-1">COUNT: </span>
                                                            <span class="pt-2 ps-1" style="font-size: 16px;"><?= $vendor_total ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- End Vendors Collections Card -->

                                        <!-- Online Collections Card -->
                                        <div class="col-xxl-3 col-md-3">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <h6 style="font-size: 18px !important; margin: 20px 0; color: #444">Online Collections</h6>

                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <img src="../assets/img/icons8-online-payment-64.png" style="width: 48px;" alt="">
                                                        </div>
                                                        <div class="ps-3">
                                                            <h5><span class="small">GH</span>&#162;<span><?= $online_amount ?></span></h5>
                                                            <span class="text-muted pt-1">COUNT: </span>
                                                            <span class="pt-2 ps-1" style="font-size: 16px;"><?= $online_total ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- End Online Collections Card -->

                                        <!-- Provider Payouts Card -->
                                        <div class="col-xxl-3 col-md-3">
                                            <div class="card info-card sales-card">
                                                <div class="card-body">
                                                    <h6 style="font-size: 18px !important; margin: 20px 0; color: #444">Service Provider Payouts</h6>

                                                    <div class="d-flex align-items-center">
                                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                                            <img src="../assets/img/icons8-withdrawal-96.png" style="width: 48px;" alt="">
                                                        </div>
                                                        <div class="ps-3">
                                                            <h5><span class="small">GH</span>&#162;<span><?= $provider_amount ?></span></h5>
                                                            <span class="text-muted pt-1">COUNT: </span>
                                                            <span class="pt-2 ps-1" style="font-size: 16px;"><?= $provider_total ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- End Provider Payouts Card -->

                                    </div>
                                </div>

                                <!-- Form Types -->
                                <div class="form-types">
                                    <div class="row">

                                        <?php foreach ($summary["form-types"] as $form) { ?>
                                            <!-- Masters Card -->
                                            <div class="col-xxl-3 col-md-3">
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

                                <div class="payment-methods">

                                </div>

                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div><!-- End Transactions Summary row -->

            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        <div class="card-body">
                            <h5 class="card-title">Daily Transactions</h5>
                            <p>Filter transactions by: </p>
                            <!-- Left side columns -->
                            <form id="reportsForm" method="post">
                                <div class="row">

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="admission-period" class="form-label">Admission Period</label>
                                        <select name="admission-period" id="admission-period" class="form-select">
                                            <option value="" hidden>Choose</option>
                                            <option value="All">All</option>
                                            <?php
                                            $result = $admin->fetchAllAdmissionPeriod();
                                            foreach ($result as $value) {
                                            ?>
                                                <option value="<?= $value["id"] ?>"><?= $value["info"] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="from-date" class="form-label">From (Date)</label>
                                        <input type="date" name="from-date" id="from-date" class="form-control">
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="to-date" class="form-label">To (Date)</label>
                                        <input type="date" name="to-date" id="to-date" class="form-control">
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="form-type" class="form-label">Form Type</label>
                                        <select name="form-type" id="form-type" class="form-select">
                                            <option value="" hidden>Choose</option>
                                            <option value="All">All</option>
                                            <?php
                                            $result = $admin->getAvailableForms();
                                            foreach ($result as $value) {
                                            ?>
                                                <option value="<?= $value["id"] ?>"><?= $value["name"] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="purchase-status" class="form-label">Purchase Status</label>
                                        <select name="purchase-status" id="purchase-status" class="form-select">
                                            <option value="" hidden>Choose</option>
                                            <option value="All">All</option>
                                            <option value="COMPLETED">COMPLETED</option>
                                            <option value="FAILED">FAILED</option>
                                            <option value="PENDING">PENDING</option>
                                        </select>
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="payment-method" class="form-label">Payment Method</label>
                                        <select name="payment-method" id="payment-method" class="form-select">
                                            <option value="" hidden>Choose</option>
                                            <option value="All">All</option>
                                            <option value="CARD">CARD</option>
                                            <option value="CASH">CASH</option>
                                            <option value="MOMO">MOMO</option>
                                            <option value="USSD">USSD</option>
                                        </select>
                                    </div>

                                </div>
                            </form>

                            <div class="mt-4" style="display: flex; justify-content: space-between">
                                <h4>Total: <span id="totalData"></span></h4>
                                <div id="alert-output"></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div><!-- End Transactions fitering row -->

            <!-- Transactions Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        <div class="filter">
                            <span class="icon download-file" id="excelFileDownload" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Export as Excel file">
                                <img src="../assets/img/icons8-microsoft-excel-2019-48.png" alt="Download as Excel file" style="cursor:pointer;width: 22px;">
                            </span>
                            <span class="icon download-file" id="pdfFileDownload" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download as PDF file">
                                <img src="../assets/img/icons8-pdf-48.png" alt="Download as PDF file" style="width: 22px;cursor:pointer;">
                            </span>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Transactions</h5>

                            <div style="margin-top: 10px !important">
                                <table class="table table-borderless table-striped table-hover" id="dataT">

                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">S/N</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Transaction ID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Phone Number</th>
                                            <th scope="col">Admission Period</th>
                                            <th scope="col">Form Bought</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Payment Method</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- Transactions List row -->

            <!-- Purchase info Modal -->
            <div class="modal fade" id="purchaseInfoModal" tabindex="-1" aria-labelledby="purchaseInfoModal" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="purchaseInfoModalTitle">Purchase Information</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger alert-dismissible infoFeed" style="display:none" role="alert">
                                <span id="msgContent">Holy guacamole! You should check in on some of those fields below.</span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <div class="mb-4 row">
                                <div class="mb-3 col-5">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Trans. ID: </span>
                                        <input disabled type="text" class="form-control _textD" id="p-transID" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="mb-3 col-7">
                                    <div class="input-group">
                                        <span class="input-group-text" id="basic-addon3">Admission Period: </span>
                                        <input disabled type="text" class="form-control _textD" id="p-admisP" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                            </div>
                            <fieldset class="mb-4 mt-4">
                                <legend>Personal</legend>
                                <div class="row">
                                    <div class="mb-3 col">
                                        <label for="p-name" class="form-label">Name</label>
                                        <input disabled type="text" class="form-control _textD" id="p-name">
                                    </div>
                                    <div class="mb-3 col">
                                        <label for="p-country" class="form-label">Country</label>
                                        <input disabled type="text" class="form-control _textD" id="p-country">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col">
                                        <label for="p-email" class="form-label">Email Address</label>
                                        <input disabled type="text" class="form-control _textD" id="p-email">
                                    </div>
                                    <div class="mb-3 col">
                                        <label for="p-phoneN" class="form-label">Phone Number</label>
                                        <input disabled type="text" class="form-control _textD" id="p-phoneN">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="mb-4">
                                <legend>Form</legend>
                                <div class="row">
                                    <div class="mb-3 col">
                                        <label for="p-appN" class="form-label">App Number</label>
                                        <input disabled type="text" class="form-control _textD" id="p-appN">
                                    </div>
                                    <div class="mb-3 col">
                                        <label for="p-pin" class="form-label">PIN</label>
                                        <input disabled type="text" class="form-control _textD" id="p-pin">
                                    </div>
                                    <div class="mb-3 col">
                                        <label for="p-status" class="form-label">Status</label>
                                        <input disabled type="text" class="form-control _textD" id="p-status">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col">
                                        <label for="p-vendor" class="form-label">Vendor</label>
                                        <input disabled type="text" class="form-control _textD" id="p-vendor">
                                    </div>
                                    <div class="mb-3 col">
                                        <label for="p-formT" class="form-label">Form Type</label>
                                        <input disabled type="text" class="form-control _textD" id="p-formT">
                                    </div>
                                    <div class="mb-3 col">
                                        <label for="p-payM" class="form-label">Payment Method</label>
                                        <input disabled type="text" class="form-control _textD" id="p-payM">
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="row" style="width:100% !important">
                                    <form id="sendPurchaseInfoForm" method="post" style="display: flex; justify-content:center">
                                        <button id="sendTransIDBtn" type="submit" class="btn btn-success" style="padding:15px !important">Generate and resend application login info</button>
                                        <input type="hidden" name="sendTransID" id="sendTransID" value="">
                                    </form>
                                </div>
                            </fieldset>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side columns -->
            <!-- End Right side columns -->

        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>
    <script>
        $(document).ready(function() {

            var triggeredBy = 0;

            // when 
            $(".form-select, .form-control").change("blur", function(e) {
                e.preventDefault();
                $("#reportsForm").submit();
            });

            $("#reportsForm").on("submit", function(e, d) {
                e.preventDefault();

                triggeredBy = 1;
                let data = new FormData(this);

                // Executes when download is click, either for excel or pdf download
                if (d == "pdfFileDownload" || d == "excelFileDownload") {
                    $.ajax({
                        type: "POST",
                        url: "../endpoint/download-file",
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.success) {
                                window.open("../download-pdf.php?w=" + d, "_blank");
                            } else {
                                $("#alert-output").html('');
                                $("#alert-output").html(
                                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    '<i class="bi bi-info-circle me-1"></i>' + result.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>'
                                );
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });

                }

                // Executes when purchase data is fetched
                else {
                    $.ajax({
                        type: "POST",
                        url: "../endpoint/salesReport",
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
                                        '<td>' + value.paymentMethod + '</td>' +
                                        '<td>' +
                                        '<button id="' + value.id + '" class="btn btn-xs btn-primary openPurchaseInfo" data-bs-toggle="modal" data-bs-target="#purchaseInfoModal">View</button>' +
                                        '</td>' +
                                        '</tr>'
                                    );
                                });
                            } else {
                                $("#alert-output").html('');
                                $("#alert-output").html(
                                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    '<i class="bi bi-info-circle me-1"></i>' + result.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>'
                                );
                            }

                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
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
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $("#sendPurchaseInfoForm").on("submit", function(e) {
                e.preventDefault();
                triggeredBy = 3;
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
                            $(".infoFeed").removeClass("alert-danger").addClass("alert-success").toggle();
                        } else {
                            $(".infoFeed").removeClass("alert-success").addClass("alert-danger").toggle();
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 3) $("#sendTransIDBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> processing...');
                    else $("#alert-output").html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 3) $("#sendTransIDBtn").prop("disabled", false).html('Send application login info');
                    else $("#alert-output").html('');
                }
            });

            $(document).on("click", ".download-file", function() {
                let data = {
                    actionType: $(this).attr("id")
                }
                $("#reportsForm").trigger("submit", $(this).attr("id"));
            });

        });
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