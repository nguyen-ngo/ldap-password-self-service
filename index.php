<?php
/**
 * Author: Ngo, Nguyen Da
 * Email: ngodanguyen@gmail.com
 */
session_start();

define('IS_INCLUDE', true);

require ($_SERVER["DOCUMENT_ROOT"]."/configs/config.inc.php");
require ($_SERVER["DOCUMENT_ROOT"]."/models/functions.php");

include("views/header.php");


if (!$_SESSION || sizeof($_SESSION) < 3) { // Not logged user
    if ($_GET) $type = htmlspecialchars(stripslashes($_GET['type']));

    if ($type == 'forgot') { // Forgot password page
        include ("views/forgot.php");
    } elseif ($type == 'resetbytoken') { // Reset password
        include ("views/resetbytoken.php");
    } else { // All other pages will go login page
        include ("views/login.php");
    }
} else { // Logged user
    if ($_GET) $type = htmlspecialchars(stripslashes($_GET['type']));

    if ($type == 'logout') { // Log user out
        session_unset();
        include ("views/login.php");
    } elseif ($type == '' || $type == 'login' || $type == 'forgot') {
        include ("views/home.php");
    } else { // All other pages will go 404 page
        include ("views/404.php");
    }
}


include("views/footer.php");
?>
