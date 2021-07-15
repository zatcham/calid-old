<?php
require("include/variables.php");
require("include/db_connect.php");
require("include/functions.php");
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: error/403.html");
    exit;
}

$pagename = "Reset Password";
$pname = $pagename . " - " . $server_name . " - Sensor System";

$currentuser = $_SESSION["username"];
$currentuserid = $_SESSION["id"];

$usraccdate = getAccCreateDate($currentuserid);
$usremail = getAccEmailAd($currentuserid);

$current_password = $new_password = $confirm_password = "";
$cpassword_err = $npassword_err = $cnpassword_err = "";
$reset_success = $reset_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // checking for existing password
    if (empty(trim($_POST["inp-cpassword"]))) {
        $cpassword_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["inp-cpassword"])) < 6) {
        $cpassword_err = "Password must have at least 6 characters.";
    } else {
        $current_password = trim($_POST["inp-cpassword"]);
    }
    // check new pwd
    if (empty(trim($_POST["inp-npassword"]))) {
        $npassword_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["inp-npassword"])) < 6) {
        $npassword_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["inp-npassword"]);
    }
    // check confirm is equal to new adn exists
    if (empty(trim($_POST["inp-cnpassword"]))) {
        $cnpassword_err = "Please confirm password.";
    } else {
        $confirm_password = trim($_POST["inp-cnpassword"]);
        if (empty($npassword_err) && ($new_password != $confirm_password)) {
            $cnpassword_err = "Password did not match.";
        }
    }

    // now check no errs
    if (empty($cpassword_err) && empty($npassword_err) && empty($cnpassword_err)) {
        $sqlq = "SELECT `password` FROM users WHERE `id`=$currentuserid;";
        $result = (mysqli_query($dbconn, $sqlq));
        if ($result != False) {
            if (mysqli_num_rows($result) > 0) {
                while ($data = mysqli_fetch_assoc($result)) {
                    $dbpwd = $data["password"];
                }
                if (password_verify($current_password, $dbpwd)) {
                    echo ("Password ok");
                    $sql = "UPDATE users SET `password`=?, `LastPWChange`=? WHERE `id`=?";
                    if ($stmt = mysqli_prepare($dbconn, $sql)) {
                        mysqli_stmt_bind_param($stmt, "sss", $param_pass, $param_date, $param_id);
                        $param_pass = password_hash($new_password, PASSWORD_DEFAULT);
                        $param_date = (date("Y-m-d H:i:s"));
                        $param_id = $currentuserid;
                        if (mysqli_stmt_execute($stmt)) {
                            // show success
                            $reset_success = '
                            <div class="alert alert-success" role="alert">
                                Password reset successfully!
                            </div>';
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                            $reset_error = '
                            <div class="alert alert-success" role="alert">
                            Oops! Something went wrong. Please try again later.
                            </div>
                            ';
                        }
                        mysqli_stmt_close($stmt);
                    }
                }
                mysqli_close($dbconn);
                }
            } else {
                echo("No results");
            }
        } else {
            echo "Error - 1";
        }
}
?>


<!-- https://startbootstrap.com/template/sb-admin - HTML is based off of this-->

<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="public/assets/favicons/favicon.ico">

    <title><?php echo $pname; ?></title> <!-- TODO : Dynamic Title -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <!--    <link href="css/home.css" rel="stylesheet">-->
    <link href="../css/style.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"
            crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

</head>

<body class="sb-nav-fixed">

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="../home.php">Sensor System</a>
    <!--    <a class="navbar-brand ps-3" href="home.php">Sensor System</a>-->
    <!-- Sidebar Toggle-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0 navmob" id="sidebarToggle" href="#!"><i
                class="fas fa-bars"></i></button>
    <!-- Navbar Search-->
    <!-- Navbar-->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
               aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#!">Settings</a></li>
                <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                <li>
                    <hr class="dropdown-divider"/>
                </li>
                <li><a class="dropdown-item" href="#!">Logout</a></li>
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
                    <a class="nav-link" href="../home.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <div class="sb-sidenav-menu-heading">Sensors</div>
                    <div>
                        <a class="nav-link" href="../sensors/new_sensor.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-plus"></i></div>
                            Add new sensor
                        </a>
                        <a class="nav-link" href="../sensors/list_sensors.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-list-ul"></i></div>
                            List sensors
                        </a>
                    </div>
                    <div class="sb-sidenav-menu-heading">Data</div>
                    <div>
                        <a class="nav-link nav-link-active" href="../../visualise.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                            Graphing
                        </a>
                        <a class="nav-link" href="../sensors/list_sensors.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            Tables
                        </a>
                    </div>
                    <div class="sb-sidenav-menu-heading">Account</div>
                    <a class="nav-link" href="users.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                        Users
                    </a>
                    <a class="nav-link" href="settings.php">
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
                <hr />
                <?php echo $reset_success; ?>
                <?php echo $reset_error; ?>
                <div class="row">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group mb-3 row">
                            <label class="col-sm-2 col-form-label" for="inp-cpassword">Current Password:</label>
                            <div class="col-sm-10">
                                <input class="form-control" id="inp-cpassword" name="inp-cpassword" type="password" value="">
                                <span class="invalid-feedback d-block"><?php echo $cpassword_err; ?></span>
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                            <label class="col-sm-2 col-form-label" for="inp-npassword">New Password:</label>
                            <div class="col-sm-10">
                                <input class="form-control" id="inp-npassword" name="inp-npassword" type="password"
                                       value="">
                                <span class="invalid-feedback d-block"><?php echo $npassword_err; ?></span>
                            </div>
                        </div>
                        <div class="form-group mb-3 row">
                            <label class="col-sm-2 col-form-label" for="inp-cnpassword">Confirm new password:</label>
                            <div class="col-sm-10">
                                <input class="form-control" id="inp-cnpassword" name="inp-cnpassword" type="password"
                                       value="">
                                <span class="invalid-feedback d-block"><?php echo $cnpassword_err; ?></span> <!-- so app. bs is broken and need d-block for inv-fdbck -->
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Change Password</button>
<!--                            <input type="submit" class="btn btn-success" value=" Change Password">-->
                        </div>
                    </form>
                    <hr/>
                    <div class="float-right">
                        <a href="settings.php" class="btn btn-secondary"><i class="fas fa-user"></i> Account Settings</a>
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

</body>
</html>
