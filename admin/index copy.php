<?php
session_start();

$_SESSION["lastAccessed"] = time();

$isAdmin = false;
if (strtolower($_SESSION["role"]) == "admin" || strtolower($_SESSION["role"]) == "developers") $isAdmin = true;

require_once('../bootstrap.php');

use Src\Controller\AdminController;
use Src\Core\Course;
use Src\Core\Department;
use Src\Core\Program;
use Src\Core\Staff;
use Src\Core\Student;

require_once('../inc/admin-database-con.php');

$admin = new AdminController($db, $user, $pass);
$course = new Course($db, $user, $pass);
$department = new Department($db, $user, $pass);
$program = new Program($db, $user, $pass);
$staff = new Staff($db, $user, $pass);
$student = new Student($db, $user, $pass);
require_once('../inc/page-data.php');

$adminSetup = false;
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
            <h1>Administrator</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class=" section dashboard">

            <!-- <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">
                        <div class="card-body">
                            <p class="card-title">Summary</p>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Dashboard view -->
            <div class="row" <?= isset($_GET["a"]) && isset($_GET["s"]) ? 'style="display:none"' : "" ?>>
                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Departments</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <img src="../assets/img/icons8-captain.png" style="width: 48px;" alt="">
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $department->total()[0]["total"]; ?></h6>
                                            <span class="text-muted small pt-2 ps-1">Counts</span>
                                            <a href="departments.php" class="btn btn-xs btn-primary" style="margin-left:10px">Open</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Staffs</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <img src="../assets/img/icons8-captain.png" style="width: 48px;" alt="">
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $staff->total()[0]["total"]; ?></h6>
                                            <span class="text-muted small pt-2 ps-1">Counts</span>
                                            <a href="staffs.php" class="btn btn-xs btn-primary" style="margin-left:10px">Open</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Programs</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <img src="../assets/img/icons8-captain.png" style="width: 48px;" alt="">
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $program->total()[0]["total"]; ?></h6>
                                            <span class="text-muted small pt-2 ps-1">Counts</span>
                                            <a href="programs.php" class="btn btn-xs btn-primary" style="margin-left:10px">Open</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Courses</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <img src="../assets/img/icons8-captain.png" style="width: 48px;" alt="">
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $course->total()[0]["total"]; ?></h6>
                                            <span class="text-muted small pt-2 ps-1">Counts</span>
                                            <a href="courses.php" class="btn btn-xs btn-primary" style="margin-left:10px">Open</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                    </div>

                    <div class="row">

                        <!-- Applications Card -->
                        <div class="col-xxl-3 col-md-3">
                            <div class="card info-card sales-card">
                                <div class="card-body">
                                    <h5 class="card-title">Students</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <img src="../assets/img/icons8-captain.png" style="width: 48px;" alt="">
                                        </div>
                                        <div class="ps-3">
                                            <h6><?= $student->total()[0]["total"]; ?></h6>
                                            <span class="text-muted small pt-2 ps-1">Counts</span>
                                            <a href="students.php" class="btn btn-xs btn-primary" style="margin-left:10px">Open</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End Applications Card -->

                    </div>
                </div><!-- Forms Sales Card  -->

            </div> <!-- End of Dashboard view -->
        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>
    <script src="../js/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#admission-period").change("blur", function(e) {
                data = {
                    "data": $(this).val()
                };
                $.ajax({
                    type: "POST",
                    url: "../endpoint/set-admission-period",
                    data: data,
                    success: function(result) {
                        console.log(result);
                        if (result.message == "logout") {
                            window.location.href = "?logout=true";
                            return;
                        }
                        if (!result.success) alert(result.message);
                        else window.location.reload();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
</body>

</html>