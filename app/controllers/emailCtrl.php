<?php

namespace App\Controllers;


class emailCtrl extends BaseController
{

    public $emailModule;


    public function __construct()
    {

        $mailModule = new \App\Modules\emailModule();
        $phpMailerObject = $mailModule->getConfigMailer();
        $this->emailModule = $phpMailerObject;
    }

    public function sendContactEmail()
    {
        /*
        $emailTemplatePath = get_template_directory().'/email-template.php';
        ob_start();
        $emailTemplate = include_once($emailTemplatePath);
        $message = ob_get_contents();
        ob_end_clean();
        */
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        if (isset($_POST['telephone'])) {
            $telephone = $_POST['telephone'];
        }
        $mail = $this->emailModule;
        $mail->Subject = $subject;
        $mail->FromName = $name;
        $mail->SMTPDebug = 0;
        $emailTemplatePath = ABSPATH . '/pages/contact-email-tmp.php';
        ob_start();
        $emailTemplate = include_once($emailTemplatePath);
        $bodyMessage = ob_get_contents();
        ob_end_clean();

        $mail->Body = $bodyMessage;

        $mail->addAddress('mawaiz.khan@dilijentsystems.com');

        try {

            $mail->send();
            $data['status'] = 'Success';
            $data['message'] = 'You message has been sent!';
            return \Framework\View::responseJson($data, 200);
        } catch (\Exception $e) {

            $data['status'] = 'Fail';
            $data['message'] = 'Error while sending email!';
            return \Framework\View::responseJson($data, 500);
        }
    }
}
