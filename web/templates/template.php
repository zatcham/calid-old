<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="assets/favicons/favicon.ico">

    <title>Pagename - DEV1 - Sensor System</title> <!-- TODO : Dynamic Title -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <!--    <link href="css/home.css" rel="stylesheet">-->
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js" crossorigin="anonymous"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

</head>

<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="home.php">Sensor System</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="account.php">Settings</a></li>
                <li><a class="dropdown-item" href="activity.php">Activity Log</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Core</div>
                    <a class="nav-link" href="home.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <div class="sb-sidenav-menu-heading">Sensors</div>
                    <div>
                        <a class="nav-link" href="new_sensor.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>
                            Add new sensor
                        </a>
                        <a class="nav-link" href="list_sensors.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-ul"></i></div>
                            List sensors
                        </a>
                    </div>
                    <div class="sb-sidenav-menu-heading">Data</div>
                    <div>
                        <a class="nav-link nav-link-active" href="visualise.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                            Graphing
                        </a>
                        <a class="nav-link" href="list_sensors.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Tables
                        </a>
                    </div>
                    <div class="sb-sidenav-menu-heading">Account</div>
                    <a class="nav-link" href="users.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                        Users
                    </a>
                    <a class="nav-link" href="account.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                        My Account
                    </a>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <?php echo $currentuser; ?>
            </div>
        </nav>
    </div>

    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">Account</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">&copy; 2021 Zach Matcham</div>
                </div>
            </div>
        </footer>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
<!--<script src="js/scripts.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<!--<script src="js/datatables-simple-demo.js"></script>-->
<script src="https://unpkg.com/react/umd/react.production.min.js" crossorigin></script>


</body>
</html>