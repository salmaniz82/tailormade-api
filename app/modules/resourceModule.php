<?php 
class resourceModule {


	public $DB;


	public function __construct()
	{
		$this->DB = new Database();
		$this->DB->table = 'resource';
	}


	public function returnAllResource()
	{
		return $this->DB->listall()->returnData();
	}


	public function pluckByName($resource)
	{

		return $this->DB->pluck('name')->where("name = '".$resource."'");

	}

	public function insert($resource)
	{
	
		$data['name'] = trim($resource);
		if($this->DB->insert($data))
		{
			return true;
		}
		else {
			return false;
		}
	}


	public function pluckIdByName($resource)
	{

		$resource = trim($resource);
		return $this->DB->pluck('id')->where("name = '".$resource."'");

		
	}

	public function remove($id){

		$query = "DELETE FROM resource where id = ? LIMIT 1";

        $stmt = $this->DB->connection->prepare($query);       
        $stmt->bind_param('d', $id);
        if($stmt->execute()){
            return true;
        }
        return false;
	}

	public function isAssingedToRole($id){

		

	}


}