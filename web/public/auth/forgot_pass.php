<?php
// TODO : All of this - HTML also to do
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="public/assets/favicons/favicon.ico">

    <title>Password Rest - DEV1 - Sensor System</title> <!-- TODO : Dynamic Title -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <link href="../css/login.css" rel="stylesheet">
</head>
<body class="text-center">
<div class="wrapper border rounded" style="background-color: #f5f5f5; padding: 40px;">
    <h2>Password recovery</h2>
    <p>Enter your email address and we will send you a link to reset your password</p>

    <?php
    if(!empty($login_err)){
        echo '<div class="alert alert-danger">' . $login_err . '</div>';
    }
    if(!empty($login_suc)){
        echo '<div class="alert alert-success">' . $login_suc . '</div>';
    }
    ?>

    <form style="form-signin" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group" style="padding-bottom: 10px;">
            <label>Username:
                <input type="text" name="username" class="form-control placeholder="Username" required autofocus>
            </label>
            <span class="invalid-feedback"><?php echo $username_err; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary btn-block" value="Reset Password">
            <button class="btn btn-primary btn-block">Return to login</button>
            <p class="mt-5 mb-3 text-muted">&copy; 2021 Zach Matcham</p>
        </div>

    </form>
</div>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

</html>