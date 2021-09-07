<?php

require_once __DIR__  . '\..\variables.php';
require_once ("Logging.php");

// we asusme autoload has run to load in phpmailer
use PHPMailer\PHPMailer\PHPMailer;

class Email  {

    // sends an email based off of a template
    public static function sendEmail($body, $subject, $to_address) {
        global $smtp_host, $smtp_username, $smtp_password, $smtp_port, $smtp_security, $smtp_auth;
        global $email_from_address;
        $mail = new PHPMailer;
        $mail->CharSet = 'utf-8';
        $mail->SMTPDebug = false; // TODO: direct emailing option?
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = $smtp_auth;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->SMTPSecure = $smtp_security;
        $mail->Port = $smtp_port;

        $mail->From = $email_from_address;
        $mail->FromName = "Calid"; // fixed name

        $mail->addAddress($to_address);
        $mail->Subject = $subject;
        $mail->isHTML(True);
        $mail->MsgHTML($body);
        if ($mail->send()) {
            Logging::log('info', "Email sent. Details: To Address $to_address, Subject $subject"); // leave body out
            return True;
        } else {
            Logging::log('error', "Mail Error occured in Email/sendEmail. Details: $mail->ErrorInfo");
            return False;
        }
    }

    // reads the templates from the template files
    public static function readTemplates($email_type) {
        $template_path = __DIR__ . '\..\..\email-templates';
        if ($email_type == "alert") {
            $template_path .= '\alert.html';
            $myfile = fopen($template_path, "r") or die("Unable to open file!");
            $file_out = fread($myfile, filesize($template_path));
            fclose($myfile);
            return ($file_out);
        } elseif ($email_type == "verify-account") {
            $template_path .= '\verify-account.html';
            $myfile = fopen($template_path, "r") or die("Unable to open file!");
            $file_out = fread($myfile, filesize($template_path));
            fclose($myfile);
            return ($file_out);
        } elseif ($email_type == "reset-pass") {
            $template_path .= '\reset-pass.html';
            $myfile = fopen($template_path, "r") or die("Unable to open file!");
            $file_out = fread($myfile, filesize($template_path));
            fclose($myfile);
            return ($file_out);
        }
        Logging::log('error', "Error occured in Email/readTemplates. No template found (if statements exited)");
        return False;
    }

    // sends verification emails
    public static function sendVerificationEmail($to_address, $userid, $username, $verify_key) {
        global $directory_path;
        $template = self::readTemplates("verify-account");
        $variables = array();
        $variables['username'] = $username;
        $path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $path .= $directory_path;
        $image_path = $path;
        $path .= "/auth/verify_email.php?key=$verify_key";
        $variables['url'] = $path;
        $image_path .= "/assets/email/calid_logo_text.png"; // changed to imgur
        $variables['image_url'] = $image_path;
//        echo ($image_path);
        foreach($variables as $key => $value) {
            $template = str_replace('{{ '.$key.' }}', $value, $template);
        }
        if (self::sendEmail($template, "Verify your email address", $to_address)) {
            Account::verifyEmailSent($verify_key); // if to be here
            Logging::log('info', "Verification email sent. Details: UserID $userid, Username $username, To Address $to_address");
        } else {
            Logging::log('error', "Error occured in Email/sendVerificationEmail. sendEmail returned value other than True");
            return False;
        }
    }

    // sends password reset emails
    public static function sendResetEmail($to_address, $userid, $username, $reset_key) {
        global $directory_path;
        $template = self::readTemplates("reset-pass");
        $variables = array();
        $variables['username'] = $username;
        $path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $path .= $directory_path;
        $image_path = $path;
        $path .= "/auth/reset_pass.php?key=$reset_key";
        $variables['url'] = $path;
        $image_path .= "/assets/email/calid_logo_text.png"; // changed to imgur temporarily
        $variables['image_url'] = $image_path;
        foreach($variables as $key => $value) {
            $template = str_replace('{{ '.$key.' }}', $value, $template);
        }
        if (self::sendEmail($template, "Reset your password", $to_address)) {
            Logging::log('info', "Reset email sent. Details: UserID $userid, Username $username, To Address $to_address");
            return True;
        } else {
            Logging::log('error', "Error occured in Email/sendResetEmail. sendEmail returned value other than True");
            return False;
        }
    }

}