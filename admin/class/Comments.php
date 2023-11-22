<?php
class Comments { 
	
	private $categoryTable = 'cms_comments';	
	private $conn;
	
	public function __construct($db){
        $this->conn = $db;
    }	
	
	public function getCategoryListing(){	
		
		$sqlQuery = "
			SELECT id, user
			FROM ".$this->categoryTable."  
			 ";
		
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' user LIKE "%'.$_POST["search"]["value"].'%" ';				
		}
		
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$stmtTotal = $this->conn->prepare("SELECT * FROM ".$this->categoryTable);
		$stmtTotal->execute();
		$allResult = $stmtTotal->get_result();
		$allRecords = $allResult->num_rows;		
		
		$displayRecords = $result->num_rows;
		$categories = array();		
		while ($category = $result->fetch_assoc()) { 				
			$rows = array();				
			$rows[] = $category['id'];
			$rows[] = $category['name'];					
			$rows[] = '<a href="add_categories.php?id='.$category["id"].'" class="btn btn-warning btn-xs update">Edit</a>';
			$rows[] = '<button type="button" name="delete" id="'.$category["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
			$categories[] = $rows;
		}
		
		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$categories
		);
		
		echo json_encode($output);	
	}
	
	public function getCategory(){		
		if($this->id) {
			$sqlQuery = "
			SELECT id, name
			FROM ".$this->categoryTable." 			
			WHERE id = ? ";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->id);	
			$stmt->execute();
			$result = $stmt->get_result();
			$category = $result->fetch_assoc();
			return $category;
		}		
	}
	
	public function insert(){
		
		if($this->user) {

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->categoryTable."(`user`)
				VALUES(?)");
			$this->name = htmlspecialchars(strip_tags($this->user));						
			$stmt->bind_param("s", $this->user);

			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->categoryTable."(`comment`)
				VALUES(?)");
			$this->name = htmlspecialchars(strip_tags($this->comment));						
			$stmt->bind_param("s", $this->user);
			
            // $this->comment = htmlspecialchars(strip_tags($this->comment));						
			// $stmt->bind_param("s", $this->comment);
			
			if($stmt->execute()){
				return $stmt->insert_id;
			}		
		}
	}
	
	
	public function totalCategory(){		
		$sqlQuery = "SELECT * FROM ".$this->categoryTable;			
		$stmt = $this->conn->prepare($sqlQuery);			
		$stmt->execute();
		$result = $stmt->get_result();
		return $result->num_rows;	
	}	
}
?>