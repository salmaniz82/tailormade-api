<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class mediaModule 
{
	
	public $DB;
	public $fileerror;

	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'media';

	}

	

	public function listall($user_id = null, $role_id = null)
	{


		$sql = "SELECT m.id, m.title, m.filepathurl, m.type, m.size as 'size', m.user_id from media m order by id desc";
		
		$media = $this->DB->rawsql($sql)->returnData();
		

		if($media != null)
		{
			return $media;
		}
		else {
			return false;
		}

	}


	public function getbyItem($id)
	{


		$sql = $sql = "SELECT m.id, m.title, m.filepathurl, m.type, m.size as 'size' , m.user_id from media m
		where m.id = $id";


		if($data = $this->DB->rawsql($sql)->returnData())
		{
			return $data;
		}

		return false;


	}


	public function save($dataPayload)
	{


		if($last_id = $this->DB->insert($dataPayload))
		{
			return $last_id;
		}

		return false;

	}


	public function handleMediaUpload($file, $user_id, $mediaLabel){

        
        if($file['error'] != 0){
			$this->fileerror = $file['error'];
            return false;
        }

        /*
        'name' => string 'work-2.jpg' (length=10)
        'type' => string 'image/jpeg' (length=10)
        'tmp_name' => string 'D:\wamp\tmp\phpC2F1.tmp' (length=23)
        'error' => int 0
        'size' => int 175629
        */

	


        $allowedImages = array('image/jpeg', 'image/jpg', 'image/png');
		$allowedDocuments = array('application/pdf');
		$allowedVideos = array('video/mp4');

        if( in_array($file["type"], $allowedImages)) {           
            $fileType = 'image';
        }
        if( in_array($file["type"], $allowedDocuments)) {           
            $fileType = 'pdf';
        }

        if( in_array($file["type"], $allowedVideos)) {           
            $fileType = 'video';
        }

        
        $payloadData['title'] = $mediaLabel;
		$payloadData['user_id'] = $user_id;
		
        $payloadData['size'] = $file["size"];
		$payloadData['type'] = $file["type"];



        if($lastId = $this->save($payloadData)){

            $target_dir = "uploads/";
		    $filename = sanitizeFilename(basename($file["name"]));
		    $target_file = $target_dir.$lastId.'_'.$filename;

			$filenameDB = $lastId.'_'.$filename; 
            
             if(move_uploaded_file($file["tmp_name"], $target_file)){
                $updatePayload = array('filepathurl' => $filenameDB);
                if($this->update($updatePayload, $lastId)){
                    return $filenameDB;
                }
                else {
					$this->fileerror = 'getting error';
                    return false;
					
                }
             }
        }

        else {

            return false;

        }

    }

	

	
	public function update($payload, $id){

		if($this->DB->update($payload, $id))
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public function removeMedia($id) {

        $query = "DELETE FROM {$this->DB->table} where id = ? LIMIT 1";

        $stmt = $this->DB->connection->prepare($query);       
        $stmt->bind_param('d', $id);
        if($stmt->execute()){
            return true;
        }
        return false;
    }


	

}