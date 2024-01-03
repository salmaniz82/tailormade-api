<?php

if (!defined('ABSPATH'))
    die('Forbidden Direct Access');

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';


class emailModule extends appCtrl
{


    public function message()
    {
        echo "welcome from email module";
    }


    public function getMailer()
    {

        $mail = new PHPMailer();
        return $mail;
    }



    public function getConfigMailer()
    {

        $mail = new PHPMailer(true);
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->CharSet = 'utf-8';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // PHPMailer::ENCRYPTION_STARTTLS / 587 |   PHPMailer::ENCRYPTION_SMTPS / 465
        $mail->Port = 587;
        /*
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465; // 25 or 465 or 587 or 2525
        */
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '82816a7abd0ca6';
        $mail->Password = '700bdb07b6ab80';
        $mail->isHTML(true);
        $mail->setFrom('no-reply@tailormadelondon.com', 'Tailormade London');
        $mail->SMTPDebug  = 0;
        return $mail;
    }
}
