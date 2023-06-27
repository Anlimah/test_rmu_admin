<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link " href="index.php">
                <i class="bi bi-grid"></i>
                <span>Dashboard</span>
            </a>
        </li><!-- End Dashboard Nav -->

        <!-- <li class="nav-item">
            <a class="nav-link collapsed" href="applications.php">
                <i class="bi bi-people"></i>
                <span>Application</span>
            </a>
        </li>End Application Page Nav -->

        <?php
        if (strtolower($_SESSION["role"]) == "admissions") {
        ?>
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="admit-applicants.php">
                    <i class="bi bi-files"></i>
                    <span>Admit Applicants</span>
                </a>
            </li>End Admit Applicants Page Nav -->

            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="broadsheet.php">
                    <i class="bi bi-person-check"></i>
                    <span>Broadsheet</span>
                </a>
            </li>End Application Page Nav -->

            <li class="nav-item">
                <a class="nav-link collapsed" href="backup.php">
                    <i class="bi bi-database-fill-down"></i>
                    <span>Backup Database</span>
                </a>
            </li><!-- End Application Page Nav -->
        <?php } ?>

        <?php
        if (strtolower($_SESSION["role"]) == "admissions" || strtolower($_SESSION["role"]) == "accounts") {
        ?>
            <li class="nav-item">
                <a class="nav-link collapsed" href="../<?= strtolower($_SESSION["role"]) ?>/user-account.php">
                    <i class="bi bi-shield-shaded"></i>
                    <span>User Accounts</span>
                </a>
            </li><!-- End User Account Page Nav -->
        <?php } ?>

    </ul>
</aside><!-- End Sidebar-->