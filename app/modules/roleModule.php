<?php 
class roleModule {


	public $DB;


	public function __construct()
	{

		$this->DB = new Database();
		$this->DB->table = 'roles';		
	}


	public function returnAllRoles()
	{
		return $roles =  $this->DB->listall()->returnData();
	}


	public function pluckByRole($rolename)
	{

		return $this->DB->pluck('role')->where("role = '".$rolename."'");

	}

    public function pluckIdRole($rolename)
    {

        return $this->DB->pluck('id')->where("role = '".$rolename."'");

    }

	public function insert($rolename)
	{
	
		$data['role'] = trim($rolename);
		if($this->DB->insert($data))
		{
			return true;
		}
		else {
			return false;
		}
	}


	public function roleHasPermissions($roleId){

		$query = "SELECT id FROM permissions WHERE role_id = ?";


		$stmt = $this->DB->connection->prepare($query);
        
        $stmt->bind_param('d', $roleId);
        
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();

	}

	public function remove($roleId){

		$query = "DELETE FROM roles where id = ? LIMIT 1";

        $stmt = $this->DB->connection->prepare($query);       
        $stmt->bind_param('d', $roleId);
        if($stmt->execute()){
            return true;
        }
        return false;
	}




}