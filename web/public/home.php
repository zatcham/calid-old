<?php
require __DIR__ . '../vendor/autoload.php';
require("../include/functions.php");
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$currentuser = $_SESSION["username"];
$currentuserid = $_SESSION["id"];

//$sensors_assigned = "10";
// not sure why assigning instead of just echo 🤷‍♀️
$sensors_assigned = getAssignedSensorCount($currentuserid);
$sensors_online = getLastSeenSCount($currentuserid);
$sensors_offline = getOfflineSCount($currentuserid);
$sensors_alerts = "0"; // TODO : alert count, add new col to db and alert functs

?>

<!-- https://startbootstrap.com/template/sb-admin - HTML is based off of this-->

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="/public/assets/favicons/favicon.ico">
    <meta http-equiv="refresh" content="900;url=logout.php"/> <!-- Auto logout -->

    <title>Home - DEV1 - Sensor System</title> <!-- TODO : Dynamic Title -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <!--    <link href="css/home.css" rel="stylesheet">-->
    <link href="css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"
            crossorigin="anonymous"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
</head>

<body class="sb-nav-fixed">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="home.php">Sensor System</a>
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
               aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#!">Settings</a></li>
                <li><a class="dropdown-item" href="account/activity.php">Activity Log</a></li>
                <li>
                    <hr class="dropdown-divider"/>
                </li>
                <li><a class="dropdown-item" href="auth/logout.php">Logout</a></li>
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
                        <a class="nav-link" href="sensors/new_sensor.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>
                            Add new sensor
                        </a>
                        <a class="nav-link" href="sensors/list_sensors.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-ul"></i></div>
                            List sensors
                        </a>
                    </div>
                    <div class="sb-sidenav-menu-heading">Data</div>
                    <div>
                        <a class="nav-link" href="../visualise.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                            Graphing
                        </a>
                        <a class="nav-link" href="sensors/list_sensors.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Tables
                        </a>
                    </div>
                    <div class="sb-sidenav-menu-heading">Account</div>
                    <?php
                    if(isUserAdmin($currentuserid) == "Admin") {
                        echo ('<a class="nav-link" href="users.php">
                                    <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                                        Users
                                </a>');
                            } ?>
                    <a class="nav-link" href="account/settings.php">
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
                <h1 class="mt-4">Dashboard</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                            Sensors assigned:
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $sensors_assigned; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-link fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="#">View Details</a>
                                <div class="text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                            Sensors online:
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $sensors_online; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-power-off fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="#">View Details</a>
                                <div class="text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-orange shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                            Sensors with alerts:
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $sensors_alerts; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="#">View Details</a>
                                <div class="text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card bg-danger shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-white text-uppercase mb-1">
                                            Sensors offline:
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-white"><?php echo $sensors_offline; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-circle fa-2x text-gray-800"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="#">View Details</a>
                                <div class="text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Average temperature for today
                            </div>
                            <div class="card-body">
                                <div id="chart-temperature" class="container"></div>
                                <div id="chartnx-temp" class="container">
                                    There is either no temperature data available for the past 24 hours, or an error has
                                    occured.
                                </div>
                                <?php echo(getAvgTempGraph($currentuserid)); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-chart-area me-1"></i>
                                Average humidity for today <!-- TODO : Add temp / hum avg cahrt -->
                            </div>
                            <div class="card-body">
                                <div id="chart-humidity" class="container"></div>
                                <?php echo(getAvgHumGraph($currentuserid)); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        DataTable Example
                    </div>
                    <div class="card-body">
                    </div>
                </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4"
        crossorigin="anonymous"></script>
<!--<script src="js/scripts.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" crossorigin="anonymous"></script>
<!--<script src="js/datatables-simple-demo.js"></script>-->
<script src="https://unpkg.com/react/umd/react.production.min.js" crossorigin></script>

<script>
    var obj = $('#chart-temperature');
    if (obj.length <= 0 || obj.css('display') != "none") // It's not there!
        $('#chartnx-temp').css('display', 'none')
</script>
</body>
</html>

