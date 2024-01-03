<?php 
class emailCtrl extends appCtrl {

    public $emailModule;


    public function __construct(){

        $mailModule = $this->load('module', 'email');
        $phpMailerObject = $mailModule->getConfigMailer();       
        $this->emailModule = $phpMailerObject;
        
    }

    public function sendContactEmail(){
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
        if( isset($_POST['telephone']) ) {
            $telephone = $_POST['telephone'];
        }       
        $mail = $this->emailModule;
        $mail->Subject = $subject;
        $mail->FromName = $name;
        $mail->SMTPDebug = 0;
        $emailTemplatePath = ABSPATH.'/pages/contact-email-tmp.php';
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
			return View::responseJson($data, 200);
    
        } catch (Exception $e) {
            
            $data['status'] = 'Fail';
            $data['message'] = 'Error while sending email!';
    		return View::responseJson($data, 500);
        }

    }


    public function processModalForm(){


        /*
        serviceType, company, email,firstName, lastName, iam, message, serviceType, telephone, workPhone
        */

        $errorStatus = false;
        $validationErrors  = [];

        if( !isset($_POST['serviceType']) || $_POST['serviceType'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Service type is missing");
        }

        if( !isset($_POST['company']) || $_POST['company'] == ""){
            $errorStatus = true;
            array_push($validationErrors, "Company details is missing");
        }

        if( !isset($_POST['email']) || $_POST['email'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Email is missing");
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorStatus = true;
            array_push($validationErrors, "Invalid Email");
        }

        if( !isset($_POST['firstName']) || $_POST['firstName'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "First Name is missing");
        }

        if( !isset($_POST['lastName']) || $_POST['lastName'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Last name is missing");
        }

        if( !isset($_POST['message']) || $_POST['message'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "No message provided");
        }

        if( !isset($_POST['telephone']) || $_POST['telephone'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Telephone not provided");
        }

        if( !isset($_POST['workPhone']) || $_POST['workPhone'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Work phone not provided");
        }

        
        if( !isset($_POST['iam']) || $_POST['iam'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "I am - profession not provided");
        }


        if(sizeof($validationErrors) > 0) {

            $data['status'] = 'Error';
            $data['message'] = "In complete form submission please make sure all values are entered";
            $statusCode = 406;
            return View::responseJson($data, $statusCode);
        }

        $subField = ( isset($_POST['subField']) ) ? $_POST['subField'] : false;

        
        $serviceType = $_POST['serviceType'];
        $company = $_POST['company'];
        $email = $_POST['email'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $iam = $_POST['iam'];
        $message = $_POST['message'];       
        $telephone = $_POST['telephone']; 
        $workPhone= $_POST['workPhone'];

        /* start processing the form data */
        
        $subject = "Dilijent form " . $firstName ." - on : ". $serviceType;
        
        $mail = $this->emailModule;
        $mail->Subject = $subject;
        $mail->FromName = $firstName;
        $mail->SMTPDebug = 0;
        $emailTemplatePath = ABSPATH.'/pages/client-form-tmp.php';
        ob_start();
        $emailTemplate = include_once($emailTemplatePath);
        $bodyMessage = ob_get_contents();
        ob_end_clean();
        $mail->Body = $bodyMessage;

        
        $mail->addAddress('mawaiz.khan@dilijentsystems.com');
        /*
        $mail->addAddress('sam@webential.co.uk');
        */

        try {

            $mail->send();
            $data['status'] = 'Success';
            $data['message'] = 'You message has been sent!';
			return View::responseJson($data, 200);
   
        } catch (Exception $e) {
            
            $data['status'] = 'Fail';
            $data['message'] = 'Error while sending email!';
    		return View::responseJson($data, 500);
        }

    }



    public function processContactform(){

        /* name, email, telephone, message, subject */

        $errorStatus = false;
        $validationErrors  = [];

        if( !isset($_POST['name']) || $_POST['name'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Name not provided");
        }


        if( !isset($_POST['email']) || $_POST['email'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Email is missing");
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errorStatus = true;
            array_push($validationErrors, "Invalid Email");
        }

        if( !isset($_POST['telephone']) || $_POST['telephone'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Telephone not provided");
        }

        
        if( !isset($_POST['subject']) || $_POST['subject'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Subject not provided");
        }

        
        if( !isset($_POST['message']) || $_POST['message'] == "" ){
            $errorStatus = true;
            array_push($validationErrors, "Message not provided");
        }

        
        if(sizeof($validationErrors) > 0) {

            $data['status'] = 'Error';
            $data['message'] = "In complete form submission please make sure all values are entered";
            $statusCode = 406;
            return View::responseJson($data, $statusCode);
        }

        $subField = ( isset($_POST['subField']) ) ? $_POST['subField'] : false;

        
        $firstName = $_POST['name'];
        $email = $_POST['email'];
        $telephone = $_POST['telephone']; 
        $subject = $_POST['subject'];
        $message = $_POST['message'];
        
        /* start processing the form data */
        
        $subject = "Dilijent contact " . $firstName ." - : ". $subject;
        
        $mail = $this->emailModule;
        $mail->Subject = $subject;
        $mail->FromName = $firstName;
        $mail->SMTPDebug = 0;
        $emailTemplatePath = ABSPATH.'/pages/contact-email-tmp.php';
        ob_start();
        $emailTemplate = include_once($emailTemplatePath);
        $bodyMessage = ob_get_contents();
        ob_end_clean();
        $mail->Body = $bodyMessage;

        $mail->addAddress('mawaiz.khan@dilijentsystems.com');
        /*
        $mail->addAddress('sam@webential.co.uk');
        */

        try {

            $mail->send();
            $data['status'] = 'Success';
            $data['message'] = 'You message has been sent!';
			return View::responseJson($data, 200);
   
        } catch (Exception $e) {
            
            $data['status'] = 'Fail';
            $data['message'] = 'Error while sending email!';
    		return View::responseJson($data, 500);
        }



    }


}