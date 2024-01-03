<?php 
class permissionsModule {


	public $DB;


	public function __construct()
	{
		$this->DB = new Database();
		$this->DB->table = 'permissions';
	}


	public function returnAll()
	{
		$sql =  "select per.id, per.role_id, per.resource_id, roles.role, res.name, per.private, public from permissions per
    			inner join roles on roles.id = per.role_id
     			inner join resource res on per.resource_id = res.id";
     			return $this->DB->rawSql($sql)->returnData();
	}


	public function checkDuplicate($data)
	{

		$role_id = 	$data['role_id'];
		$resource_id = 	$data['resource_id'];
		if($data = $this->DB->build('S')->Colums()->Where("role_id = '".$role_id."'")->Where("resource_id = '".$resource_id."'")->go()->returnData())
		{
			return $data;
		}
		else {
			return false;
		}

	}

	public function insert($data)
	{
	
		
		if($this->DB->insert($data))
		{
			return true;
		}
		else {
			return false;
		}
	}


	public function actionHasPermission($actionId){

		$query = "SELECT id from permissions where resource_id = ?";

		$stmt = $this->DB->connection->prepare($query);
        
        $stmt->bind_param('d', $actionId);
        
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();

	}

	


	public function removeRolePermission($roleId){

		$query = "DELETE FROM permissions where id = ? LIMIT 1";

        $stmt = $this->DB->connection->prepare($query);       
        $stmt->bind_param('d', $roleId);
        if($stmt->execute()){
            return true;
        }
        return false;
	}




}