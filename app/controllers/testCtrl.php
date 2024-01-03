<?php 
class testCtrl extends appCtrl {

    public $emailModule;


    public function __construct(){

        $mailModule = $this->load('module', 'email');
        $phpMailerObject = $mailModule->getConfigMailer();       
        $this->emailModule = $phpMailerObject;
        
    }

    public function emailTemplate(){

        return View::render('contact-email-tmp.php');
        
    }

    public function imageWork()
    {

        $imageTransformer = new ImageTransformer();

        // make sure the path start from the base root directory by leaving the root itself

        if (!$imageTransformer->loadImage('/uploads/test-club.jpg')) {

            echo "cannot continue further";
            exit();

        } else {

            $imageTransformer->prepareDimension(600, 'auto');

            $args = [

                'location'=> '/thumbnails',
                'filename'=> 'customname',
                'quality'=> 100

            ];

            var_dump($imageTransformer->reproduceSaveImage($args));

        }


    }


    public function testJunction(){

        $roleModule = $this->load('module', 'role');
        var_dump( $roleModule->pluckIdRole('content creator') );

    }


 

}