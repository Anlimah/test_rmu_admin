<?php
session_start();

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["isLoggedIn"] !== true) {
    // Redirect to index.php
    header("Location: ./index.php");
    exit(); // Make sure to exit after redirection
}
require("../bootstrap.php");

use Controller\Classes;
use Controller\Courses;
use Core\Base;

$pageTitle = "Assign Class";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require Base::build_path("partials/head.php") ?>
</head>

<body>

    <?php require Base::build_path("partials/header.php") ?>

    <?php require Base::build_path("partials/aside.php") ?>

    <main id="main" class="main">

        <?php require Base::build_path("partials/page-title.php") ?>

        <section class="section dashboard">
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-12">
                    <div class="row">

                        <!-- Sales Card -->
                        <div class="col-xxl-12 col-md-12">
                            <div class="card">

                                <div class="card-body">
                                    <h5 class="card-title"></h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <select id="assign-course-who" class="form-select">
                                                <option value="" hidden>Choose...</option>
                                                <?php
                                                $config = require Base::build_path("config/database.php");
                                                $classObj = new Classes($config["database"]["mysql"]);
                                                $classes = $classObj->fetchByDepartment($_SESSION["user"]["fk_department"]);

                                                $counter = 0;
                                                foreach ($classes as $class) :
                                                ?>
                                                    <option value="<?= $class["classCode"] ?>"><?= $class["classCode"] ?></option>
                                                <?php
                                                    $counter++;
                                                endforeach
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- End Sales Card -->

                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Courses</h5>
                                    <!-- Borderless Table -->
                                    <table class="table table-borderless datatable">                                        <thead>
                                            <tr>
                                                <th scope="col">SN.</th>
                                                <th scope="col">Code</th>
                                                <th scope="col">Name</th>
                                                <th scope="col">Credit Hours</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $config = require Base::build_path("config/database.php");
                                            $courseObj = new Courses($config["database"]["mysql"]);
                                            $courses = $courseObj->fetchAll();

                                            $counter = 1;
                                            foreach ($courses as $course) :
                                            ?>
                                                <tr>
                                                    <th scope="row"><?= $counter ?></th>
                                                    <td><?= $course["courseCode"] ?></td>
                                                    <td><?= $course["courseName"] ?></td>
                                                    <td><?= $course["creditHours"] ?></td>
                                                    <td>
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input course" value="<?= $course["courseCode"] ?>">
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                                $counter++;
                                            endforeach
                                            ?>
                                        </tbody>
                                    </table>
                                    <!-- End Bordered Table -->

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <form id="assignCourseForm" method="POST" class="d-flex" style="justify-content: end;">
                            <input type="hidden" name="assign-what" id="assign-what" value="course">
                            <input type="hidden" name="assign-to" id="assign-to" value="class">
                            <input type="hidden" name="assign-who" id="assign-who" value="">
                            <input type="hidden" name="assign-course-list[]" id="assign-course-list" value="">
                            <input type="hidden" name="assign-depart" id="assign-depart" value="<?= $_SESSION["user"]["fk_department"] ?>">
                            <button class="btn btn-primary">Assign</button>
                        </form>
                    </div>

                </div><!-- End Left side columns -->

            </div>
        </section>

    </main><!-- End #main -->

    <?php require Base::build_path("partials/foot.php") ?>
    <script>
        $(document).ready(function() {
            function capitalizeFirstCharacter(str) {
                if (str.length > 0) return str.charAt(0).toUpperCase() + str.slice(1);
                else return str;
            }

            $("#assign-course-who").change("blur", function() {
                $("#assign-who").val(this.value);
            });

            $('.course').change(function() {
                var checkedCheckboxes = $('input[type="checkbox"]:checked');
                var courseArray = checkedCheckboxes.map(function() {
                    return this.value;
                }).get();
                $('#assign-course-list').val(JSON.stringify(courseArray));
            });

            $("#assignCourseForm").on("submit", function(e) {
                e.preventDefault();

                $.ajax({
                    type: "POST",
                    url: "../api/staff/assign",
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                }).done(function(data) {
                    console.log(data);
                    alert(data.message);
                    window.location.reload();
                }).fail(function(err) {
                    console.log(err);
                });
            });

        });
    </script>
</body>

</html>