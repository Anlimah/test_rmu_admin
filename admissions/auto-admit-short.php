<?php
session_start();

if (!isset($_SESSION["adminLogSuccess"]) || $_SESSION["adminLogSuccess"] == false || !isset($_SESSION["user"]) || empty($_SESSION["user"])) {
    header("Location: ../index.php");
}

$isUser = false;
if (strtolower($_SESSION["role"]) == "admissions" || strtolower($_SESSION["role"]) == "developers") $isUser = true;

if (isset($_GET['logout']) || !$isUser) {
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

$_SESSION["lastAccessed"] = time();

require_once('../bootstrap.php');

use Src\Controller\AdminController;

require_once('../inc/admin-database-con.php');

$admin = new AdminController($db, $user, $pass);
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
            <h1>Admit Voc/Prof Applicants</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Admit Voc/Prof Applicants</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <div class="row">

                <!-- Recent Sales -->
                <div class="col-12">

                    <div class="card recent-sales overflow-auto">

                        <div class="card-body">
                            <h5 class="card-title"></h5>
                            <div id="info-output"></div>
                            <table class="table table-borderless datatable table-striped table-hover">
                                <thead>
                                    <tr class="table-dark">
                                        <th scope="col">#</th>
                                        <th scope="col" colspan="1">Full Name</th>
                                        <th scope="col" colspan="1">Programme: (<span class="pro-choice">1<sup>st</sup></span>) Choice</th>
                                        <th scope="col" colspan="4" style="text-align: center;">Core Subjects</th>
                                        <th scope="col" colspan="4" style="text-align: center;">Elective Subjects</th>
                                        <th scope="col" colspan="1" style="text-align: center;">Actions</th>
                                    </tr>
                                    <tr class="table-grey">
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Core Mathematics">CM</th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="English Language">EL</th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Integrated Science">IS</th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Social Studies">SS</th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 1">E1</th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 2">E2</th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 3">E3</th>
                                        <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 4">E4</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <div class="mt-4" style="float:right">
                                <button class="btn btn-primary" id="admit-short">Admit</button>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                    </div>
                </div><!-- End Recent Sales -->

                <!-- Right side columns -->
                <!-- End Right side columns -->

            </div>
        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>

    <script>
        $(document).ready(function() {

            var fetchBroadsheet = function() {
                data = {
                    "cert-type": $("#cert-type").val(),
                    "prog-type": $("#prog-type").val(),
                }

                $.ajax({
                    type: "POST",
                    url: "../endpoint/getUnadmittedShortApps",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                //let status = value.declaration == 1 ? '<span class="badge text-bg-success">Q</span>' : '<span class="badge text-bg-danger">F</span>';
                                $("tbody").append(
                                    '<tr>' +
                                    '<th scope="row">' + (index + 1) + '</th>' +
                                    '<td>' + value.app_pers.first_name + ' ' + value.app_pers.last_name + '</td>' +
                                    '<td>' + value.app_pers.programme + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[0].subject + '">' + value.sch_rslt[0].grade + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[1].subject + '">' + value.sch_rslt[1].grade + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[2].subject + '">' + value.sch_rslt[2].grade + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[3].subject + '">' + value.sch_rslt[3].grade + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[4].subject + '">' + value.sch_rslt[4].grade + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[5].subject + '">' + value.sch_rslt[5].grade + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[6].subject + '">' + value.sch_rslt[6].grade + '</td>' +
                                    '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[7].subject + '">' + value.sch_rslt[7].grade + '</td>' +
                                    '<td style="text-align: center">' +
                                    '<form method="POST" id="viewApplicantDetailsForm">' +
                                    '<input type="hidden" value="' + value.app_pers.programme + '" name="prog">' +
                                    '<input type="hidden" value="' + value.app_pers.id + '" name="appId" id="appId">' +
                                    '<button class="btn btn-primary btn-xs" type="submit" id="viewAppDeatilsBtn">View</button>' +
                                    '</form>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });

                        } else {
                            if (result.message == "logout") {
                                window.location.href = "?logout=true";
                                return;
                            }
                            $("tbody").html('');
                            $("#info-output").html(
                                '<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                                '<i class="bi bi-info-circle me-1"></i>' + result.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                            $("#admit-short").hide();
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            let triggeredBy = 1;
            fetchBroadsheet();

            $('#admit-short').click(function() {
                triggeredBy = 2;
                $.ajax({
                    type: "POST",
                    url: "../endpoint/admit-short",
                    data: data,
                    success: function(result) {
                        console.log(result);
                        if (result.success) fetchBroadsheet();
                        else if (result.message == "logout") window.location.href = "?logout=true";
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(document).on('submit', "#viewApplicantDetailsForm", function(e) {
                e.preventDefault();
                triggeredBy = 3;

                payload = new FormData(this);
                appId = $("#appId").val();

                $.ajax({
                    type: "POST",
                    url: "../endpoint/program-info",
                    data: payload,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            url = "applicant-info.php?t=" + result.message[0].type + "&c=" + result.message[0].category + "&q=" + appId;
                            location.href = url;
                        } else if (result.message == "logout") window.location.href = "?logout=true";

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });

            })

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 1) $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> sending...');
                    if (triggeredBy == 2) $("#admit-short").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...');
                    if (triggeredBy == 3) $("#viewAppDeatilsBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 1) $("#submitBtn").prop("disabled", false).html('Fetch Data');
                    if (triggeredBy == 2) $("#admit-short").prop("disabled", false).html('Admit');
                    if (triggeredBy == 3) $("#viewAppDeatilsBtn").prop("disabled", false).html('View');
                }
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