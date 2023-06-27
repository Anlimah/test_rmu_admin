<?php
session_start();

if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: ../login.php");
}

if (isset($_GET['logout'])  || strtolower($_SESSION["role"]) != "admissions") {
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
    <style>
        .arrow {
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Applications</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <?php
                    if (isset($_GET["t"])) {
                        $form_name = $admin->getFormTypeName($_GET["t"]);
                        echo '<li class="breadcrumb-item active">' . $form_name[0]["name"] . '</li>';
                    }
                    ?>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class=" section dashboard">

            <!-- programs summary view -->
            <div class="row" <?= !isset($_GET["t"]) ? 'style="display:none"' : "" ?>>

                <!-- Recent Sales -->
                <div class="col-12">

                    <div class="card recent-sales overflow-auto">

                        <div class="filter">
                            <span class="icon export-excel" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Export Excel">
                                <img src="../assets/img/icons8-microsoft-excel-2019-48.png" alt="" style="width: 24px;">
                            </span>
                            <span class="icon download-pdf" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download PDF">
                                <img src="../assets/img/icons8-pdf-48.png" alt="" style="width: 24px;">
                            </span>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">Applicantions</h5>

                            <div class="row mx-auto">
                                <!-- summary data buttons -->
                                <button id="apps-total" class="btn btn-outline-primary col me-2 toggle-output">
                                    Total
                                    <span class="badge text-bg-secondary">
                                        <?= isset($_GET["t"]) ? $admin->fetchTotalApplications($_GET["t"])[0]["total"] : ""; ?>
                                    </span>
                                </button>

                                <button id="apps-submitted" class="btn btn-outline-primary col me-2 toggle-output">
                                    Submitted
                                    <span class="badge text-bg-secondary">
                                        <?= isset($_GET["t"]) ? $admin->fetchTotalSubmittedOrUnsubmittedApps($_GET["t"], true)[0]["total"] : ""; ?>
                                    </span>
                                </button>

                                <button id="apps-in-progress" class="btn btn-outline-primary col me-2 toggle-output">
                                    In Progress
                                    <span class="badge text-bg-secondary">
                                        <?= isset($_GET["t"]) ? $admin->fetchTotalSubmittedOrUnsubmittedApps($_GET["t"], false)[0]["total"] : ""; ?>
                                    </span>
                                </button>

                                <button id="apps-admitted" class="btn btn-outline-primary col me-2 toggle-output">
                                    Admitted
                                    <span class="badge text-bg-secondary">
                                        <?= isset($_GET["t"]) ? $admin->fetchTotalAdmittedOrUnadmittedApplicants($_GET["t"], true)[0]["total"] : ""; ?>
                                    </span>
                                </button>

                                <button id="apps-unadmitted" class="btn btn-outline-primary col me-2 toggle-output">
                                    Unadmitted
                                    <span class="badge text-bg-secondary">
                                        <?= isset($_GET["t"]) ? $admin->fetchTotalAdmittedOrUnadmittedApplicants($_GET["t"], false)[0]["total"] : ""; ?>
                                    </span>
                                </button>

                                <button id="apps-awaiting" class="btn btn-outline-primary col toggle-output">
                                    Awaiting
                                    <span class="badge text-bg-secondary">
                                        <?= isset($_GET["t"]) ? $admin->fetchTotalAwaitingResultsByFormType($_GET["t"])[0]["total"] : ""; ?>
                                    </span>
                                </button>

                            </div>
                            <div class="collapse" id="toggle-output">
                                <hr class="mb-4">

                                <form action="" class="mb-4 mt-4" id="form-filter">
                                    <div class="row">
                                        <div class="col-4">
                                            <label for="country" class="form-label">Country</label>
                                            <select name="country" id="country" class="form-select">
                                                <option value="" hidden>Choose</option>
                                                <option value="All">All</option>
                                                <option value="Cameroun">Cameroun</option>
                                                <option value="Ghana">Ghana</option>
                                                <option value="Gambia">Gambia</option>
                                                <option value="Liberia">Liberia</option>
                                                <option value="Sierra Leone">Sierra Leone</option>
                                                <option value="Others">Others</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label for="program" class="form-label">Programs</label>
                                            <select name="program" id="program" class="form-select">
                                                <option value="" hidden>Choose</option>
                                                <option value="All">All</option>
                                                <?php
                                                $data = $admin->fetchPrograms(0);
                                                foreach ($data as $ft) {
                                                ?>
                                                    <option value="<?= $ft['name'] ?>"><?= $ft['name'] ?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                                <div id="info-output"></div>
                                <table class="table table-borderless datatable table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col" style="width:150px">Name</th>
                                            <th scope="col">Country</th>
                                            <th scope="col">Application Type</th>
                                            <th scope="col">Programme (1<sup>st</sup> Choice)</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                    </div>
                </div><!-- End Recent Sales -->

            </div> <!-- programs summary view -->
            <!-- Right side columns -->
            <!-- End Right side columns -->

        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>

    <script>
        // when 
        $(document).ready(function() {
            var summary_selected = "";
            // when a summary data button is clicked
            $(".toggle-output").click(function() {
                $('.toggle-output').css('border-bottom', 'none');
                $(this).css('border-bottom', '3px solid #000');

                // Remove arrow from all buttons
                $(".arrow").remove();
                $(".form-select option:selected").attr("selected", false);
                $(".form-select option[value='All']").attr('selected', true);

                // Add arrow to selected button
                $(this).append("<span class='arrow'>&#x25BC;</span>");

                summary_selected = $(this).attr("id");
                data = {
                    action: summary_selected,
                    form_t: getUrlVars()["t"]
                };

                $.ajax({
                    type: "POST",
                    url: "../endpoint/apps-data",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                if (value["declaration"] == 1) {
                                    $("tbody").append(
                                        '<tr>' +
                                        '<th scope="row"><a href="javascript:void()">' + (index + 1) + '</a></th>' +
                                        '<td>' + value.fullname + '</td>' +
                                        '<td>' + value.nationality + '</td>' +
                                        '<td>' + value.app_type + '</td>' +
                                        '<td>' + value.first_prog + '</td>' +
                                        '<td><span class="badge text-bg-success">Submitted</span></td>' +
                                        '<td><b><a href="applicant-info.php?t=' + getUrlVars()["t"] + '&q=' + value.id + '">Open</a></b></td>' +
                                        '</tr>');
                                } else {
                                    $("tbody").append(
                                        '<tr>' +
                                        '<th scope="row"><a href="javascript:void()">' + (index + 1) + '</a></th>' +
                                        '<td>' + value.fullname + '</td>' +
                                        '<td>' + value.nationality + '</td>' +
                                        '<td>' + value.app_type + '</td>' +
                                        '<td>' + value.first_prog + '</td>' +
                                        '<td><span class="badge text-bg-danger">In Progress</span></td>' +
                                        '</tr>');
                                }

                            });
                            $("#info-output").hide();

                        } else {
                            $("tbody").html("<tr style='text-align: center'><td colspan='7'>No entries found</td></tr>");
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });

                if ($("#toggle-output").is(":visible") === false) $("#toggle-output").slideToggle();
            });

            // when 
            $(".form-select").change("blur", function(e) {
                e.preventDefault();

                data = {
                    "action": summary_selected,
                    "country": $("#country").val(),
                    "type": getUrlVars()["t"],
                    "program": $("#program").val(),
                }

                var id = this.id

                $.ajax({
                    type: "POST",
                    url: "../endpoint/applicants",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                let status = value.declaration == 1 ? '<span class="badge text-bg-success">Submitted</span>' : '<span class="badge text-bg-danger">In Progress</span>';
                                $("tbody").append(
                                    '<tr>' +
                                    '<th scope="row"><a href="javascript:void()">' + (index + 1) + '</a></th>' +
                                    '<td>' + value.fullname + '</td>' +
                                    '<td>' + value.nationality + '</td>' +
                                    '<td>' + value.app_type + '</td>' +
                                    '<td>' + value.first_prog + '</td>' +
                                    '<td>' + status + '</td>' +
                                    '<td><b><a href="applicant-info.php?q=' + value.id + '">Open</a></b></td>' +
                                    '</tr>');
                            });
                            $("#info-output").hide();

                        } else {
                            $("tbody").html("<tr style='text-align: center'><td colspan='7'>No entries found</td></tr>");
                        }

                        if (id == "type") {
                            $.ajax({
                                type: "GET",
                                url: "../endpoint/programs",
                                data: {
                                    "type": getUrlVars()["t"],
                                },
                                success: function(result) {
                                    console.log(result);
                                    if (result.success) {
                                        $("#program").html('<option value="All">All</option>');
                                        $.each(result.message, function(index, value) {
                                            $("#program").append('<option value="' + value.name + '">' + value.name + '</option>');
                                        });
                                    }
                                },
                                error: function(error) {
                                    console.log(error);
                                }
                            });
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            function getUrlVars() {
                var vars = {};
                var parts = window.location.href.replace(
                    /[?&]+([^=&]+)=([^&]*)/gi,
                    function(m, key, value) {
                        vars[key] = value;
                    }
                );
                return vars;
            }

            //Use a default value when param is missing
            function getUrlParam(parameter, defaultvalue) {
                var urlparameter = defaultvalue;
                if (window.location.href.indexOf(parameter) > -1) {
                    urlparameter = getUrlVars()[parameter];
                }
                return urlparameter;
            }

            if (getUrlVars()["status"] != "" || getUrlVars()["status"] != undefined) {
                if (getUrlVars()["exttrid"] != "" || getUrlVars()["exttrid"] != undefined) {}
            }

            $(".export-excel").click(function() {
                if (summary_selected !== "") {
                    data = {
                        "action": summary_selected,
                        "country": $("#country").val(),
                        "type": getUrlVars()["t"],
                        "program": $("#program").val(),
                    }

                    $.ajax({
                        type: "POST",
                        url: "../endpoint/export-excel",
                        data: data,
                        success: function(result) {
                            console.log(result);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });

                    //window.open("../export-excel.php?w=sdjgskfsd&a=hoh&c=jgkg&t=hjgkj&p=jgksjgks", "_blank");
                }
            });

            $(".download-pdf").click(function() {
                if (summary_selected !== "") {
                    data = {
                        "action": summary_selected,
                        "country": $("#country").val(),
                        "type": getUrlVars()["t"],
                        "program": $("#program").val(),
                    }
                    window.open("../download-pdf.php?w=apps&t=" + getUrlVars()["t"] + "&a=" + data["action"] + "&c=" + data["country"] + "&t=" + data["type"] + "&p=" + data["program"], "_blank");
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