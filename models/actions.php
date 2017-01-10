<?php
/**
 * Author: Ngo, Nguyen Da
 * Email: ngodanguyen@gmail.com
 */

if ($_POST['loginActive'] == "") { die("Can not run directly !!"); } else { define('IS_INCLUDE', true); }

session_start();


require ($_SERVER["DOCUMENT_ROOT"]."/configs/config.inc.php");
require ($_SERVER["DOCUMENT_ROOT"]."/models/functions.php");


$errorMessage = "";


if ($_GET['action'] == 'authenticate') {
    if (!$_POST['gcaptcha']) {
        $errorMessage .= "<p>&#9888; Please check the Captcha</p>";
    } else {
        $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$gcaptcha_secret."&response=".$_POST['gcaptcha']."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
        if (!$response['success']) {
            $errorMessage .= "<p>&#9888; There's something wrong with gCaptcha</p>";
        }
    }

    if (!$_POST['username']) {
        $errorMessage .= "<p>&#9888; Username is required</p>";
    }

    if (!$_POST['password']) {
        $errorMessage .= "<p>&#9888; Password is required</p>";
    }

    if ($errorMessage != "") {
        echo $errorMessage;
        exit();
    }

    $user = $_POST['username'];
    $password = $_POST['password'];
    $searchBase = $ldap_config['ldap_base_dn'];
    $searchFilter = "(cn=$user)";
    $attr = array ("dn", "cn", "mail");

    $result = ldap_get_entries($ldapconn,ldap_search($ldapconn, $searchBase, $searchFilter, $attr));
    if (!$result) {
        echo "LDAP error: ".ldap_error($ldapconn);
    } else {
        if (ldapAuthenticate($result[0]['dn'], $password)) {
            $_SESSION['dn'] = $result[0]['dn'];
            $_SESSION['cn'] = $result[0]['cn'][0];
            $_SESSION['mail'] = $result[0]['mail'][0];
        } else {
            $errorMessage = "<p>&#9888; Could not find username/password combination - Please try again</p>";
        }

        if ($errorMessage != "") {
            echo $errorMessage;
            exit();
        } else echo "OK";
    }
}


if ($_GET['action'] == 'changepass') {
    if (!$_POST['oldpassword']) {
        $errorMessage .= "<p>&#9888; Please input your current password</p>";
    } else {
        if (!ldapAuthenticate($_SESSION['dn'], $_POST['oldpassword'])) {
            $errorMessage .= "<p>&#9888; Your current password is incorrect</p>";
        }
    }

    if (!$_POST['newpassword']) {
        $errorMessage .= "<p>&#9888; Please input your new password</p>";
    }

    if ($_POST['oldpassword'] == $_POST['newpassword']) {
        $errorMessage .= "<p>&#9888; New password can't be the same with old password";
    }

    if (!$_POST['confirmpassword']) {
        $errorMessage .= "<p>&#9888; Please confirm your new password</p>";
    }

    if ($_POST['newpassword'] !== $_POST['confirmpassword']) {
        $errorMessage .= "<p>&#9888; Your new password does not match</p>";
    }

    if ($errorMessage != "") {
        echo $errorMessage;
        exit();
    } else {
        $isPasswordValid = validatePassword($_POST['newpassword']);
        if ($isPasswordValid != "OK") {
            echo $isPasswordValid;
            exit();
        } else {
            $changeResult = changePassword($_SESSION['dn'], $_POST['newpassword']);
            if ($changeResult != "OK") {
                echo $changeResult;
            } else {
                $mailContent = "Hello ".$_SESSION['cn'].",\n\nYour password has been changed.\n\nIf you didn't change password, please contact your administrator immediately.";
                // Send notification to user via email
                $isSent = sendMail($_SESSION['mail'], $mailContent);
                if ($isSent != 200 && $isSent != 202) {
                    $errorMessage .= $isSent;
                } else echo "OK";
            }
        }
    }
}


if ($_GET['action'] == 'reset') {
        if (!$_POST['gcaptcha']) {
            $errorMessage .= "<p>&#9888; Please check the Captcha</p>";
        } else {
            $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$gcaptcha_secret."&response=".$_POST['gcaptcha']."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
            if (!$response['success']) {
                $errorMessage .= "<p>&#9888; There's something wrong with gCaptcha</p>";
            }
        }

    if (!$_POST['username']) {
        $errorMessage .= "<p>&#9888; Username is required</p>";
    }

    if (!$_POST['email']) {
        $errorMessage .= "<p>&#9888; Email is required</p>";
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errorMessage .= "<p>&#9888; Email is not valid</p>";
    }

    if ($errorMessage != "") {
        echo $errorMessage;
        exit();
    }

    $user = $_POST['username'];
    $email = $_POST['email'];
    $searchBase = $ldap_config['ldap_base_dn'];
    $searchFilter = "(cn=$user)";
    $attr = array ("dn", "cn", "mail");

    if (!$searchResult = ldap_search($ldapconn, $searchBase, $searchFilter, $attr)) {
        $errorMessage = ldap_error($ldapconn);
    } else {
        $result = ldap_get_entries($ldapconn,$searchResult);
        if ($result[0]['mail'][0] != $email) {
            $errorMessage .= "<p>&#9888; Username and Email doesn't not match</p>";
        }
    }

    if ($errorMessage != "") {
        echo $errorMessage;
        exit();
    }

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i <=8; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    // Build and store token
    $_SESSION['inf'] = $result[0]['dn'].":".$result[0]['cn'][0].":".$result[0]['mail'][0];
    $_SESSION['token'] = session_id();
    $token = encrypt($_SESSION['token'], $keyphrase);

    // Generate link to verify reset password
    $verifyLink = $homepage."?type=resetbytoken&token=".$token;
    // Generate mail content
    $mailContent = "Hello $user,\n\nClick the link below to reset your password\n$verifyLink\n\nIf you didn't request a password reset, please ignore this email.";
    // Send verify link to user via email
    $isSent = sendMail($email, $mailContent);
    if ($isSent != 200 && $isSent != 202) {
        $errorMessage .= $isSent;
    } else echo "OK";
}

?>
