<?php
require_once(LIB_PATH.DS.'database.php');
class User{
	
	protected static $tbl_name = "tblusers";
	function db_fields(){
		global $mydb;
		return $mydb->getFieldsOnOneTable(self::$tbl_name);
	}
	function listOfUsers(){
		global $mydb;
		$mydb->setQuery("Select * from ".self::$tbl_name);
		$cur = $mydb->loadResultList();
		return $cur;
	
	}
	/* HIPANAO SOLUTIONS - Listahan ng mga account na ang usertype
	   (TYPE) ay "Program Head" - gagamitin ito bilang pagpipilian
	   sa Program Head ng isang Course (module/course). */
	function listProgramHeads(){
		global $mydb;
		$mydb->setQuery("SELECT * FROM ".self::$tbl_name." WHERE `TYPE` = 'Program Head' AND `STATUSACTIVE` = 1 ORDER BY `DISPLAYNAME` ASC");
		$cur = $mydb->loadResultList();
		return $cur;
	}
	function single_user($id=''){
			global $mydb;
			$mydb->setQuery("SELECT * FROM ".self::$tbl_name." Where PK= '{$id}' LIMIT 1");
			$cur = $mydb->loadSingleResult();
			return $cur;
	}
	function find_all_user($name=""){
			global $mydb;
			$mydb->setQuery("SELECT * 
							FROM  ".self::$tbl_name." 
							WHERE  `USERNAME` ='{$name}'");
			$row_count = $mydb->num_rows();
			return $row_count;
	}
	function find_all_user_notthis($name="", $uid=0){
			global $mydb;
			$mydb->setQuery("SELECT * 
							FROM  ".self::$tbl_name." 
							WHERE  `USERNAME` ='{$name}' and UID != ". $uid. " ");
			$row_count = $mydb->num_rows();
			return $row_count;
	}
	function find_oldpass($pk, $oldpass){
			global $mydb;
			$mydb->setQuery("SELECT * FROM ".self::$tbl_name." Where 
				`PK`=" . $pk . " and Password = sha1('{$oldpass}') LIMIT 1");
		return	$row_count = $mydb->num_rows();
			
	}

	 static function AuthenticateUser($username="", $h_upass=""){
		global $mydb;
		$mydb->setQuery("SELECT * FROM `tblusers` WHERE `USERNAME`='" . $username . "' and `PASSWORD`='" . $h_upass ."' AND STATUSACTIVE = 1 LIMIT 1");
		$row_count = $mydb->num_rows();
		 if ($row_count == 1){
		 $found_user = $mydb->loadSingleResult();
		 
		 $cur1 = $mydb->loadSingleResult();
		    $_SESSION['UID'] 	 	= $found_user->UID;
            $_SESSION['DISPLAYNAME'] = $found_user->DISPLAYNAME;
            $_SESSION['USERNAME']	= $found_user->USERNAME;
            $_SESSION['TYPE']    	= $found_user->TYPE;
            
            $_SESSION['PICTURE']   = isset($found_user->PICTURE) ? $found_user->PICTURE : '';

            /* HIPANAO SOLUTIONS - Student Login Accounts. Kapag ang
               nag-login ay isang Student account (ginawa sa
               module/student > "Create Login Account"), kunin agad
               dito ang kanyang sariling S_ID (tblstudent.ACCOUNT_UID
               ang naka-link papunta sa UID na ito) para magamit sa
               student-only dashboard (home.php) at sa access guard
               (theme/template.php) - iisang query lang dito sa
               login, hindi na kailangang ulit-uliting i-lookup. */
            $_SESSION['STUDENT_SID'] = null;
            if ($found_user->TYPE === 'Student') {
                $mydb->setQuery("SELECT S_ID FROM `tblstudent` WHERE ACCOUNT_UID = '".intval($found_user->UID)."' LIMIT 1");
                $studentRows = $mydb->loadResultList();
                if (count($studentRows) >= 1) {
                    $_SESSION['STUDENT_SID'] = intval($studentRows[0]->S_ID);
                }
            }
            
        	return true;
			}else{
				return false;
			}	
				
	} 	
	
	static function instantiate($record) {
		$object = new self;

		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		} 
		return $object;
	}
	
	private function has_attribute($attribute) {
	  
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		
	  global $mydb;
	  $attributes = array();
	  foreach($this->db_fields() as $field) {
	    if(property_exists($this, $field)) {
			$attributes[$field] = $this->$field;
		}
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $mydb;
	  $clean_attributes = array();
	  
	  foreach($this->attributes() as $key => $value){
	    $clean_attributes[$key] = $mydb->escape_value($value);
	  }
	  return $clean_attributes;
	}
	
	public function save() {
	  
	  return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $mydb;
		
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$tbl_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		return	$mydb->InsertThis($sql);
	}

	public function update($id=0) {
	  global $mydb;
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
		  $attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$tbl_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE UID =". $id;
		return  $mydb->InsertThis($sql);
	 	
	}

	public function delete($id=0) {
		global $mydb;
		  $sql = "DELETE FROM ".self::$tbl_name;
		  $sql .= " WHERE UID =". $id;
		  $sql .= " LIMIT 1 ";
		return  $mydb->InsertThis($sql);
		  
	}
		
}
?>