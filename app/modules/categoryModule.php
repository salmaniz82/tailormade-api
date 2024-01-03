<?php 

if ( !defined('ABSPATH') )
	die('Forbidden Direct Access');


class categoryModule extends appCtrl {

	public $DB;


	public function __construct()
	{
		$this->DB = new Database();
		$this->DB->table = 'categories';
	} 


	public function getList()
	{
		if(!$catList  = $this->DB->listall()->returnData())
		{
			return false;
		}


		return $catList;
	}


	public function catTree()
	{

		$sql = "SELECT
	    c.id,
	    CONCAT(
	    REPEAT('- ',
	       LENGTH(p.path) - LENGTH(REPLACE(p.path,'/','')) - 2  
	      ),
	    c.name
	    ) AS tree, pcat_id 
	    FROM categories c
	    LEFT JOIN category_path p ON c.id = p.cat_id
	    ORDER BY p.path ASC;";

		 if($tree = $this->DB->rawSql($sql)->returnData())
		 {
		 	return $tree;
		 }

		 else {
		 	return false;
		 }
		
			
		

	}


	public function flatJoinList()
	{

		$sql = "select t2.id as 'id', t2.pcat_id, t1.name as 'parent', t2.name as 'category' from categories t1
			right join categories t2 on t1.id = t2.pcat_id ORDER BY t2.pcat_id";
		return $this->DB->rawSql($sql)->returnData();
	}

	public function flatJoinSingle($id)
	{

		$sql = "select t2.id as 'id', t2.pcat_id, t1.name as 'parent', t2.name as 'category' from categories t1
			right join categories t2 on t1.id = t2.pcat_id WHERE t2.id = $id ORDER BY t2.pcat_id ";
		return $this->DB->rawSql($sql)->returnData();
	}


	public function singleItemById($id)
	{
		
		if(!$data = $this->DB->getbyId($id)->returnData())
		{
			return false;

		}

		return $data;

	}


	public function flatRootList()
	{

		$sql = "SELECT id, name from categories where pcat_id IS NULL";	

		return 	$this->DB->rawSql($sql)->returnData();

	}




	public function checkDuplicate(array $dataPayload)
	{


		$category = $dataPayload['name'];
		$parent_id = (isset($dataPayload['pcat_id'])) ?  $dataPayload['pcat_id'] : NULL;

		if($parent_id != NULL)
		{
			$data = $this->DB->build('S')->Colums()->Where("name = '".$category."'")->Where("pcat_id = '".$parent_id."'")->go()->returnData();
		}
		else {
			$data = $this->DB->build('S')->Colums()->Where("name = '".$category."'")->Where("pcat_id IS NULL")->go()->returnData();	
		}
		
		if($data)
		{
			return $data;
		}
		else {
			return false;
		}

	}


	public function save($data)
	{

		if($last_id = $this->DB->insert($data))
		{
			return $last_id;	
		}
		else {
			return false;
		}


	}


	public function destroyById($id)
	{

		if($this->DB->build('D')->where("id = '".$id."'")->go())
		{
			return true;
		}

		else {
			return false;
		}
	}


	public function hasChildren($catId)
	{

		

		if($row = $this->DB->build('S')->Colums('id')->where("pcat_id = '".$catId."'")->go()->returnData())
		{
			return true;
		}

		return false;

	}


	public function destroyCategoryPath($cat_id)
	{

		$this->DB->table = 'category_path';

		if($this->DB->build('D')->where("cat_id = '". $cat_id."'")->go())
		{
			return true;
		}

		else {
			return false;
		}


	}


	public function updateCategory($dataPayload, $id)
	{
		if($this->DB->update($dataPayload, $id))
		{
			return true;
		}
		else {
			return false;
		}
	}


	public function pluckCatIdByCategoryName($categoryName)
	{

		if(!$cat_id = $this->DB->pluck('id')->Where("name = '".$categoryName."'"))
		{
			return false;
		}

		return $cat_id;

	}

	public function setParentZeroToNull($id)
	{

		$sql = "UPDATE categories SET pcat_id = NULL where pcat_id = $id";

		if($this->DB->rawSql($sql)->go())
		{
			return true;
		}
		else {
			return false;
		}
	}


	public function getCatIdsByParent($decipline)
	{


		$sql = "SELECT id FROM categories WHERE pcat_id IN ($decipline)";

		if($rows = $this->DB->rawSql($sql)->returnData())
		{
			return $rows;
		}
		else {
			return false;
		}


	}



	public function verifySubDeciplines($decipline, $subdecipline)
	{

		$dbcatIdsSanitized = [];
		$parent_id = implode(',', $decipline);
		$catIdsFromDB = $this->getCatIdsByParent($parent_id);

		for($i=0; $i<sizeof($catIdsFromDB); $i++)
		{		 
			array_push($dbcatIdsSanitized, $catIdsFromDB[$i]['id']);

		}

		return array_intersect($dbcatIdsSanitized, $subdecipline);


	}

}