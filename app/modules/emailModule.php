<?php

namespace App\Modules;

if (!defined('ABSPATH')) die('Direct Access File is not allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if (!defined('ABSPATH'))
    die('Forbidden Direct Access');


class emailModule
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
