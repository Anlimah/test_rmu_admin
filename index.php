<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["admin"]) && !empty($_SESSION["admin"])) {
} else {
  header("Location: login.php");
}

if (isset($_GET['logout'])) {
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

  header('Location: login.php');
}
?>
<?php
require_once('bootstrap.php');

use Src\Controller\AdminController;

$admin = new AdminController();
require_once('inc/page-data.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?= require_once("inc/head.php") ?>
</head>

<body>
  <?= require_once("inc/header.php") ?>

  <?= require_once("inc/sidebar.php") ?>

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
              <a href="https://forms.rmuictonline.com/buy-vendor/">
                <div class="card info-card sales-card">
                  <div class="card-body">
                    <h5 class="card-title">Sell Form</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <img src="./assets/img/icons8-sell-48.png" style="width: 48px;" alt="">
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">forms</span>
                      </div>
                    </div>
                  </div>
                </div>
              </a>
            </div><!-- End Applications Card -->

            <!-- Applications Card -->
            <div class="col-xxl-3 col-md-3">
              <a href="forms-sale.php">
                <div class="card info-card sales-card">
                  <div class="card-body">
                    <h5 class="card-title">Form Sales Stats</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <img src="./assets/img/icons8-stocks-growth-96.png" style="width: 48px;" alt="">
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">Statistics</span>
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
                  <a href="general-settings.php">
                    <h5 class="card-title">Settings</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <img src="./assets/img/icons8-services-96.png" style="width: 48px;" alt="">
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">Statistics</span>
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

  <?= require_once("inc/footer-section.php") ?>
  <script src="js/jquery-3.6.0.min.js"></script>
  <script>
    $("dataTable-top").hide();
  </script>

</body>

</html>