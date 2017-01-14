<?php
/**
 * Author: Ngo, Nguyen Da
 * Email: ngodanguyen@gmail.com
 */

if(!defined('IS_INCLUDE')) { die("This file can not run directly !!"); }

require ($_SERVER["DOCUMENT_ROOT"]."/configs/config.inc.php");
//require ($_SERVER["DOCUMENT_ROOT"]."/vendors/sendgrid-php/vendor/autoload.php");
require ($_SERVER["DOCUMENT_ROOT"]."/vendors/phpmailer/PHPMailerAutoload.php");

$errorMessage = "";

$ldapconn = ldap_connect($ldap_config['ldap_uri']) or die("Could not connect to " . $ldap_config['ldap_uri']);

ldap_set_option($ldapconn, OPT_REFERRALS, 0);
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, OPT_X_TLS, OPT_X_TLS_DEMAND);
ldap_set_option($ldapconn, OPT_X_TLS_DEMAND, true);
ldap_set_option($ldapconn, OPT_DEBUG_LEVEL, 255);


/**
 *  Show page header Logout button after user logged in
 */
function showLogoutButton() {
    if ($_SESSION && (!isset($_SESSION['token']) || sizeof($_SESSION) >= 3)) {
        echo "<div id='headerLogoutButton' class='form-inline float-xs-right'>";
        echo "<a class='btn btn-outline-success' href='?type=logout'>Logout</a>";
        echo "</div>";
    }
}


function showGreeting() {
    if (sizeof($_SESSION) >= 3) {
        echo "<p>Howdy, ".$_SESSION['cn']."!</p><hr>";
    }
}

function enableGCaptcha($flag) {
    global $gcaptcha_secret;
    if ($flag) {
        echo "<div class='g-recaptcha' data-sitekey='".$gcaptcha_secret."'></div><br>";
    }
}


/**
 * @param $password
 * @return string
 */
function validatePassword($password) {
    global $errorMessage;

    if (strlen($password) < 8 || strlen($password) > 24) {
        $errorMessage .= "<p>&diams; Password must contain more than 8 and less than 24 characters</p>";
    }
    if (!preg_match("#[0-9]+#", $password)) {
        $errorMessage .= "<p>&diams; Password must contain at least 1 digit</p>";
    }
    if (!preg_match("#[a-z]+#",$password)) {
        $errorMessage .= "<p>&diams; Password must contain at least 1 lower character</p>";
    }
    if (!preg_match("#[A-Z]+#",$password)) {
        $errorMessage .= "<p>&diams; Password must contain at least 1 upper character</p>";
    }
    /*
    if (preg_match("#[@%]+#",$password)) {
        $errorMessage .= "<p>&diams; Password could not contain @,% character</p>";
    }
    if( !preg_match("#\W+#", $pwd) ) {
        $errorMessage .= "<p>&diams; Password must contain at least 1 symbol</p>";
    }
     */
    if ($errorMessage != "") {
        return $errorMessage;
    } else return "OK";
}


/**
 * @param $password - plain text password
 * @return ssha hashed password
 */
function hashPassword($password) {
    mt_srand((double)microtime()*1000000);
    $salt = pack("CCCC", mt_rand(), mt_rand(), mt_rand(), mt_rand());
    $hashedPassword = "{SSHA}" . base64_encode(pack("H*", sha1($password . $salt)) . $salt);
    return $hashedPassword;
}


/* @function encrypt(string $data)
 * Encrypt a data
 * @param data
 * @return encrypted data
 * @author Matthias Ganzinger
 */
function encrypt($data, $keyphrase) {

    /* Open the cipher (AES-256)*/
    $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');

    /* Create the IV and determine the keysize length, use MCRYPT_RAND
     * on Windows instead */
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
    $ks = mcrypt_enc_get_key_size($td);

    /* Create key */
    $key = substr(md5($keyphrase), 0, $ks);

    /* Intialize encryption */
    mcrypt_generic_init($td, $key, $iv);

    /* Encrypt data */
    $encrypted = mcrypt_generic($td, $data);

    /* Terminate encryption handler */
    mcrypt_generic_deinit($td);

    /* Terminate decryption handle and close module */
    mcrypt_module_close($td);

    /* base64 encode iv and message */
    $iv = base64_encode($iv);
    $encrypted = base64_encode($encrypted);

    /* return data nn:ivencrypted */
    return strlen($iv). ":" . $iv . $encrypted;
}


/* @function decrypt(string $data)
 * Decrypt a data
 * @param data
 * @return decrypted data
 * @author Matthias Ganzinger
 */
function decrypt($data, $keyphrase) {

    /* replace spaces with +, otherwise base64_decode will fail */
    $data = str_replace(" ", "+", $data);

    /* get iv */
    $ivcount = substr($data, 0, strpos($data, ':'));
    $message = strstr($data, ':');
    $iv = substr($message, 1, $ivcount);
    $iv = base64_decode($iv);

    /* get data */
    $encrypted = base64_decode(substr($message, $ivcount+1));

    /* Open the cipher */
    $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
    $ks = mcrypt_enc_get_key_size($td);

    /* Create key */
    $key = substr(md5($keyphrase), 0, $ks);

    /* Intialize encryption */
    mcrypt_generic_init($td, $key, $iv);

    /* Decrypt encrypted string */
    $decrypted = mdecrypt_generic($td, $encrypted);

    /* Terminate decryption handle and close module */
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    /* Show string */
    return trim($decrypted);
}


/**
 * @param $binddn - User dn
 * @param $bindpw - User password
 * @return bool
 */
function ldapAuthenticate($binddn, $bindpw) {
    global $ldapconn;

    $ldapbind = ldap_bind($ldapconn, $binddn, $bindpw);

    if ($ldapbind) {
        return TRUE;
    } else {
        return FALSE;
    }
}


/**
 * @param $userdn - User dn
 * @param $usermail - User email
 * @param $newpassword - New plain password
 * @return string
 */
function changePassword($userdn, $newpassword) {
    global $ldap_config;
    global $ldapconn;

    if(!ldap_bind($ldapconn, $ldap_config["ldap_bindn"], $ldap_config["ldap_binpw"])) {
        return "LDAP error: ".ldap_error($ldapconn);
        exit();
    }

    $userdata['userpassword'] = hashPassword($newpassword);
    $changePassword = ldap_modify($ldapconn, $userdn, $userdata);
    if (!$changePassword) {
        return "LDAP error: ".ldap_error($ldapconn);
        exit();
    } else {
        return "OK";
    }
}


function sendMail($recipient, $content) {
    global $errorMessage;
 /*
    global $sendgrid_api_key;
    $from = new SendGrid\Email("LDAP Password Management administrator", "pwmadmin@yourdomain.com");
    $subject = "Notification mail from LDAP Password Management";
    $to = new SendGrid\Email("", $recipient);
    $body = new SendGrid\Content("text/html", $content);
    $mail = new SendGrid\Mail($from, $subject, $to, $body);
    $sg = new \SendGrid($sendgrid_api_key);
    $response = $sg->client->mail()->send()->post($mail);
    return $response->statusCode();
 */
    $mail = new PHPMailer();
    //$mail->SMTPDebug = 2;
    $mail->IsSMTP();
    $mail->SMTPAuth = false;
    $mail->Host = "mail.yourdomain.com";
    $mail->Port = 25;
    $mail->Username = "pwmadmin@yourdomain.com";
    $mail->Password = "";
    $mail->SetFrom('pwmadmin@yourdomain.com', 'LDAP Password Management administrator');
    $mail->Subject = "Notification mail from LDAP Password Management";
    $mail->MsgHTML($content);
    $mail->AddAddress($recipient, "");
    if($mail->Send()) {
        return "200";
    } else {
        return $mail->ErrorInfo;
    }
}


function resetByToken() {
    global $keyphrase;
    if ($_GET['token'] == "") {
        echo "<div><p class='alert alert-danger'>&#9888; Token is required</p></div>";
    } else {
        if (!isset($_SESSION) || $_SESSION['token'] == "") {
            echo "<div><p class='alert alert-danger'>&#9888; Token is expired or invalid</p></div>";
        } else {
            $userToken = $_GET['token'];
            $sessionToken = $_SESSION['token'];
            $userInfo = explode(":", $_SESSION['inf']);
            if (decrypt($userToken, $keyphrase) === $sessionToken) {
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters);
                $randomString = '';
                for ($i = 0; $i <=8; $i++) {
                    $randomString .= $characters[rand(0, $charactersLength - 1)];
                }
                $changeResult = changePassword($userInfo[0], $randomString);
                if ($changeResult != "OK") {
                    echo $changeResult;
                } else {
                    $mailContent = "Hello ".$userInfo[1].",<br>Your password has been reset.<br><br>Your new password is ".$randomString.".<br>If you didn't request to reset password, please contact your administrator immediately.";
                    // Send notification to user via email
                    $isSent = sendMail($userInfo[2], $mailContent);
                    if ($isSent != 200 && $isSent != 202) {
                        $errorMessage .= $isSent;
                    } else echo "<div><p class='alert alert-success'>&#10003; Your password has been reset.</p></div>";
                }
            } else {
                echo "<div><p class='alert alert-danger'>&#9888; Token is expired or invalid</p></div>";
            }
        }
    }
}
?>
