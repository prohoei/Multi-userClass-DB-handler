<?php
/*
 * Multiple database Class
 * This class is used for handling database relations (PDO)
 * @author    Alexander Høi Nielsen
 * @url       http://Prohoei.dk
 * Version	  1.0
 */
class dbHandler{
	
	/*
     * If you dont wanna call DB connection in your config file
     * You can connect here!

    private $dbHost     = "";
    private $dbUsername = "";
    private $dbPassword = "";
    private $dbName     = "";

    public function __construct(){
        if(!isset($this->db)){
            // Connect to the database
            try{
                $conn = new PDO("mysql:host=".$this->dbHost.";dbname=".$this->dbName.";charset=UTF8", $this->dbUsername, $this->dbPassword);
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->db = $conn;
            }catch(PDOException $e){
                die("Failed to connect with MySQL: " . $e->getMessage());
            }
        }
    }*/
 
    public function __construct($conn){
		if(!isset($this->db)){
            // Connect to the database
            $this->db = $conn;
        }
    }

    /*
     * Returns rows from database based on the conditions
     * @param string name of the table
     * @param array select, where, order_by, limit and return_type conditions
     */
    public function getRows($table,$conditions = array()){
		try
		{
			$sql = 'SELECT ';
			$sql .= array_key_exists("select",$conditions)?$conditions['select']:'*';
			$sql .= ' FROM '.$table;
			if(array_key_exists("where",$conditions)){
				$sql .= ' WHERE ';
				$i = 0;
				foreach($conditions['where'] as $key => $value){
					$pre = ($i > 0)?' AND ':'';
					$sql .= $pre.$key." = '".$value."'"; // Where by condition
					$i++;
				}
			}			
			
			if(array_key_exists("like",$conditions)){
				$sql .= ' WHERE ';
				$i = 0;
				foreach($conditions['like'] as $key => $value){
					$pre = ($i > 0)?' OR ':'';
					$sql .= $pre.$key." LIKE '%".$value."%'"; // Search condition
					$i++;
				}
			}
			
			if(array_key_exists("order_by",$conditions)){
				$sql .= ' ORDER BY '.$conditions['order_by']; // Order by condition
			}

			if(array_key_exists("start",$conditions) && array_key_exists("limit",$conditions)){
				$sql .= ' LIMIT '.$conditions['start'].','.$conditions['limit']; 
			}elseif(array_key_exists("limit",$conditions)){
				$sql .= ' LIMIT '.$conditions['limit']; 
			}			
			
			$query = $this->db->prepare($sql);
			$query->execute();
			
			if(array_key_exists("return_type",$conditions) && $conditions['return_type'] != 'all'){
				switch($conditions['return_type']){
					case 'count':
						$result = $query->rowCount();
						break;
					case 'single':
						$result = $query->fetch(PDO::FETCH_ASSOC);
						break;
					default:
						$result = '';
				}
			}else{
				if($query->rowCount() > 0){
					$result = $query->fetchAll();
				}
			}
			return !empty($result)?$result:false;
		}
		catch(PDOException $e)
        {
            echo $e->getMessage();
        }
    }
	
	/*
	 * Show pagination links where table output
	 * @param total number of records from table
     * @param number of records per page
     */
	public function pagingLink($records,$records_per_page){ 
		$self = $_SERVER['PHP_SELF'];
		$total_records = $records;
		if($total_records > 0)
		{
			$total_pages=ceil($total_records/$records_per_page);
			$current_page=1;
			if(isset($_GET["page_no"]))
			{
			    $current_page=$_GET["page_no"];
			}
			if($current_page!=1)
			{
				$previous =$current_page-1;
				echo "<a href='".$self."?page_no=1' >First</a>&nbsp;&nbsp;";
				echo "<a href='".$self."?page_no=".$previous."'>Previous</a>&nbsp;&nbsp;";;
			}
				
			$x='';
			for($i=1;$i<=$total_pages;$i++)
			{
				if($i==$current_page){
					$x.= "<a href='".$self."?page_no=".$i."'><b>".$i."</b></a>&nbsp;&nbsp;";
				}
				elseif ($i>4 && $i!=$total_pages){
					$x.= ".";
				}
				else{
					$x.= "<a href='".$self."?page_no=".$i."'>".$i."</a>&nbsp;&nbsp;";
				}
			}
			echo $x;

		   if($current_page!=$total_pages)
		   {
				$next=$current_page+1;
				echo "<a href='".$self."?page_no=".$next."'>Next</a>&nbsp;&nbsp;";
				echo "<a href='".$self."?page_no=".$total_pages."'>Last</a>&nbsp;&nbsp;";
		   }
		}
	}

    /*
     * Insert data into the database
     * @param string name of the table
     * @param array the data for inserting into the table
     */
    public function insert($table,$data){
		if(!empty($data) && is_array($data)){
		  try
		  {
			date_default_timezone_set("Europe/Copenhagen"); // Changing default timezone
            if(!array_key_exists('created',$data)){
                $data['created'] = date("Y-m-d H:i:s");
            }
            if(!array_key_exists('edited',$data)){
                $data['edited'] = date("Y-m-d H:i:s");
            }

            $columnString = implode(',', array_keys($data));
            $valueString = ":".implode(',:', array_keys($data));
            $sql = "INSERT INTO ".$table." (".$columnString.") VALUES (".$valueString.")";
            $query = $this->db->prepare($sql);
            foreach($data as $key=>$val){
                 $query->bindValue(':'.$key, $val);
            }
            $insert = $query->execute();
            return $insert?$this->db->lastInsertId():false;
		  }
		  catch(PDOException $e)
          {
             echo $e->getMessage();
          }
		}else{
			return false;
		}
    }
    
    /*
     * Update data into the database
     * @param string name of the table
     * @param array the data for updating into the table
     * @param array where condition on updating data
     */
    public function update($table,$data,$conditions){
        if(!empty($data) && is_array($data)){
          try
		  {			
			$valSet = '';
            $whereSql = '';
            $i = 0;
			date_default_timezone_set("Europe/Copenhagen"); // Changing default timezone
            if(!array_key_exists('edited',$data)){
                $data['edited'] = date("Y-m-d H:i:s");
            }
            foreach($data as $key=>$val){
                $pre = ($i > 0)?', ':'';
                $valSet .= $pre.$key."='".$val."'";
                $i++;
            }
            if(!empty($conditions)&& is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $value){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$value."'";
                    $i++;
                }
            }
            $sql = "UPDATE ".$table." SET ".$valSet.$whereSql;
            $query = $this->db->prepare($sql);
            $update = $query->execute();
            return $update?$query->rowCount():false;
		  }
		  catch(PDOException $e)
          {
             echo $e->getMessage();
          }
        }else{
            return false;
        }
    }
    
    /*
     * Delete data from the database
     * @param string name of the table
     * @param array where condition on deleting data
     */
    public function delete($table,$conditions){
      try
	  {
		$whereSql = '';
        if(!empty($conditions)&& is_array($conditions)){
            $whereSql .= ' WHERE ';
            $i = 0;
            foreach($conditions as $key => $value){
                $pre = ($i > 0)?' AND ':'';
                $whereSql .= $pre.$key." = '".$value."'";
                $i++;
            }
        }
        $delete = $this->db->exec("DELETE FROM ".$table.$whereSql);
        return $delete?$delete:false;
	  }
	  catch(PDOException $e)
      {
        echo $e->getMessage();
      }
    }
	
	/*
     * Check if Exist
     * @param string name of the table
	 * @param array where condition check if exists
     */
	public function checkExist($tblName,$checkData) {
		$whereSql = '';
            if(!empty($checkData)&& is_array($checkData)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($checkData as $key => $val){
                    $pre = ($i > 0)?' AND ':'';
                    $whereSql .= $pre.$key." = '".$val."'";					
                    $i++;
                }
            }
			
		$stmt = $this->db->prepare("SELECT * FROM ".$tblName.$whereSql);
			foreach($checkData as $key=>$val){
                $stmt->bindValue(':'.$key, $val);
            }
		
		$stmt->execute();
		
		$count = '';
		if($stmt->rowCount() > 0){
			$count = 1;
			return true;
		} else {
			$count = 0;
			return false;
		}
	}
	
 	/*
     * User login
     * @param string name of the table
	 * @param array where condition check if exists to login
     */
    public function login($table,$pass,$conditions){
        try
		{   
			$whereSql = '';
            if(!empty($conditions)&& is_array($conditions)){
                $whereSql .= ' WHERE ';
                $i = 0;
                foreach($conditions as $key => $val){
                    $pre = ($i > 0)?' OR ':'';
                    $whereSql .= $pre.$key." = '".$val."'";					
                    $i++;
                }
            }
			
			$stmt = $this->db->prepare("SELECT * FROM ".$table.$whereSql);
			foreach($conditions as $key=>$val){
                $stmt->bindValue(':'.$key, $val);
            }
			$stmt->execute();
			$userRow=$stmt->fetch(PDO::FETCH_ASSOC);
  			if($stmt->rowCount() > 0)
			{
			  if(password_verify($pass, $userRow['password'])) // Vertify pass with hashed pass
			  {
				$_SESSION['user_session'] = array(
					'user' => $userRow['id']
				);
                return true;
              }
			  else { return false; } // If $pass doesn't match return false
			}
		}
		catch(PDOException $e)
        {
           echo $e->getMessage();
        }
    }
	
	/*
     * Unset user Session
     */ 
    public function logout(){
        unset($_SESSION['user_session']);
        return true;
    }
	
    /*
     * Check if user_session retuns true
     */
    public function is_loggedin(){
      if(isset($_SESSION['user_session']))
      {
        return true;
      }
    }
	
    /*
     * Redirect user
     */
    public function redirect($url){
       header('Location: '.$url);
    }
}