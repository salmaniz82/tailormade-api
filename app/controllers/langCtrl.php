<?php 
class langCtrl extends appCtrl {


	public function listall()
	{
		
		$db = new Database();
		$db->table = 'lang';

		$data['list'] = $db->listAll()->returnData();

		View::responseJson($data);
	}


	public function addInterface()
	{
		$data['title'] = 'Add Names';

		View::render('lang-add', $data);

	}

	public function save()
	{
		
		$db = new Database();
		$db->table = 'lang';

		$inputFields = array('name_en', 'name_ar');

		$langdata = $db->sanitize($inputFields);
		

		if( $db->insert($langdata) )
		{
			
			$data['message'] = 'new row added';
			
		}
		else{
			
			$data['message'] = 'failed';

		}
	

		View::responseJson($data);

	}


	public function debugpost()
	{
		
		print_r($_FILES['thumb']);

	}
}