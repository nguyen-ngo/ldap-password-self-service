<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags always come first -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <title>LDAP Password Management</title>

    <link rel="shortcut icon" href="images/favicon.png" type="image/x-icon">
    <link rel="icon" href="images/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/styles.css" >

    <script src="https://www.google.com/recaptcha/api.js"></script>

    <link rel="stylesheet" href="vendors/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="row">
                <div id="headerTitle" class="col-md-8"><h2><a href="<?php echo $homepage; ?>"><i class="fa fa-home" aria-hidden="true"></i> LDAP Password Management</a></h2></div>
                <div class="col-md-4">
                    <div id="headerLogoutButton" class="form-inline float-xs-right">
                        <?php showLogoutButton(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
