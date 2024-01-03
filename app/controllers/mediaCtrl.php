<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');

class mediaCtrl extends appCtrl{

	
	public $module;
	public $layout;
	
	public function __construct()
	{
		
		$this->module = $this->load('module', 'media');
		$template = new Template();
		$this->layout = $template->layout('dashboard.php');

	}



	public function index()
	{

		
		/*
			ACL PENDING
		*/

		
		$data['title'] = 'Dashboard Media';
		$user_id = Auth::User()['id'];
		$role_id = Auth::User()['role_id'];


		if( $media = $this->module->listall($user_id, $role_id) )
		{
			$data['media'] = $media;
			$statusCode = 200;


		}

		else {

			$data['message'] = "Media not found";
			$statusCode = 500;
		}

		$this->layout->compile('dashboard/media.php', $data);

		/*
		return View::responseJson($data, $statusCode);
		*/

	}

	public function uploadMedia(){

		/*
		should be making this dedicated to the blog image 
		so that we can be sure that we need to create a thumnails for this image
		*/

           
        if($mediaUrl = $this->module->handleMediaUpload($_FILES['file'], Auth::User()['id'], 'featured image')){
            $data['message'] = 'File uploaded successfully!';
            $data['uploadedFilePath'] = $mediaUrl;

			/* front end should get response and server can carry on background */	
			View::responseJson($data, 200);

			/*
			you can decide here if you want to create a resample of that image 
			*/

			/* start resample */

			$imageTransformer = new ImageTransformer();

			if (!$imageTransformer->loadImage('/uploads/'.$mediaUrl)) {
				/*
				echo "cannot continue further";
				*/
				exit();
	
			} else {
	
				$imageTransformer->prepareDimension(600, 'auto');
	
				$args = [
					'location'=> '/thumbnails/output/',
					'filename'=> 'customname',
					'quality'=> 100
				];
	
				$imageTransformer->reproduceSaveImage($args);
	
			}

			/* end resample */

            return 
            die();
            
        }

		if($this->module->fileerror == 1){
			$message = 'Reached upload_max_filesize on server';
		}
		else {
			$message = 'Unexpected error encountered while upload file';
		}

        $data['message'] = $message;
		$data['fileerror'] = $this->module->fileerror;
        return View::responseJson($data, 500);
        die();


    }


	public function singleItemById()
	{

	


		$id = $this->getID();

		if($media = $this->module->getbyItem($id))
		{
			$data['media'] = $media;
			$statusCode = 200;
		}
		else {

			$data['message'] = "Item not found";
			$statusCode = 404;
		}

		return View::responseJson($data, $statusCode);


	}

	public function save()
	{
	
		/*
			ACL IS LOGGED IN 
			HAS PERMISSION TO UPLOAD MEDIA
			PENDING
		*/
		
		$allowedImages = array('image/jpeg', 'image/jpg', 'image/png');
		$allowedDocuments = array('application/pdf');
		$allowedVideos = array('video/mp4');
		$imageMaxFileSize = (1048576 * 3);
		$docsMaxFileSize = (1048576 * 5);
		$videoMaxFileSize = (1048576 * 10);

		$maxReachedFileSize = false;

		if(!isset($_FILES['file']))
		{
			
			$data['message'] = "No File Provided cannot proceed further";
			$statusCode = 500;
		}
		else {

			if(in_array($_FILES["file"]["type"], $allowedImages) )
			{
				$filetype = "image";
				$maxfileSize = $imageMaxFileSize;
			}

			else if(in_array($_FILES["file"]["type"], $allowedDocuments) )
			{
				$filetype = "document";
				$maxfileSize = $docsMaxFileSize;

			}

			else if(in_array($_FILES["file"]["type"], $allowedVideos) )
			{
				$filetype = "video";
				$maxfileSize = $videoMaxFileSize;
			}

			else {

				$filetype = "unsupported";

			}

			if( $_FILES["file"]["size"] < $maxfileSize)
			{
				$maxReachedFileSize = false;
			}
			else {
				$maxReachedFileSize = true;	
			}

			if($filetype == "unsupported" || $maxReachedFileSize == true)
			{
				$data['message'] = $filetype . "File is either unsupported or reached max file size";
				$data['maxSize'] = $maxfileSize;
				$data['filesize'] = $_FILES["file"]["size"];
				$statusCode = 500;
			}
			else {

				$target_dir = "uploads/";
				$filename = sanitizeFilename(basename($_FILES["file"]["name"]));
				$target_file = $target_dir.$filename;
			if(!file_exists($target_file))
			{
				
				if(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) 
				{

					$payloadData['title'] = $_POST['title'];
					$payloadData['user_id'] = $this->jwtUserId();
					$payloadData['category_id'] = $_POST['category_id'];
					$payloadData['size'] = $_FILES["file"]["size"];
					$payloadData['filepathurl'] = $target_file;
					$payloadData['type'] = $filetype;
					if($last_id = $this->module->save($payloadData))
					{
						$data['message'] = "File added to media library";
						$data['last_id'] = $last_id;

						if($lastItem = $this->module->getbyItem($last_id))
						{
							$data['lastItem'] = $lastItem;
						}
						
						$statusCode = 200;	
					}
					else {

						$data['message'] = "Data cannot be saved to the data base";
						$statusCode = 500;	
						$data['debug'] = $this->module->DB;
					}				
				}
				else {
					$data['message'] = "failed while uploading a file";
					$statusCode = 500;
				}	

			}

			else {
				$data['message'] = "file with duplciate name already exists";
				$statusCode = 500;
			}



			}

		}


		return View::responseJson($data, $statusCode);

	}


	public function removeMedia(){

		$id = $this->getID();
		if($media = $this->module->getbyItem($id)){
			$mediaFilePath = $media[0]['filepathurl'];
			if($dboperation = $this->module->removeMedia($id)) {

				$data['message'] = $id . '- Record removed ';
				
				if(file_exists('/uploads/'.$mediaFilePath)) {
					$fileOperation = unlink('/uploads/'.$mediaFilePath);
					$data['message'] = $data['message'] .= '& file removed';
				}

				if(file_exists('/uploads/thumbnails/'.$mediaFilePath)) {
					$fileOperation = unlink('/uploads/thumbnails/'.$mediaFilePath);
					$data['message'] = $data['message'] .= '& file removed';
				}

				return View::responseJson($data, 200);
			}
			
			$data['message'] = 'Error found while removing media';
			return View::responseJson($data, 500);


		}


		

	}

	

	

}