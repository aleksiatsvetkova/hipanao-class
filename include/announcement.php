<?php
require_once(LIB_PATH.DS.'database.php');

/* HIPANAO SOLUTIONS - Announcement Module.
   Sinusunod nito ang parehong pattern ng include/course.php (simpleng
   ActiveRecord-style class) - ginagamit ito ng module/announcement/
   (Admin CRUD) at ng homepage.php (public homepage, read-only). */

class Announcement {

	protected static $tbl_name = "tblannouncements";

	public $ANNOUNCEMENT_ID;
	public $TITLE;
	public $CONTENT;
	public $PICTURE;
	public $VIDEO;
	public $DATE_POSTED;
	public $STATUS;
	public $DATEADDED;
	public $ADDEDBY;
	public $DATEMODIFIED;
	public $MODIFIEDBY;

	function db_fields(){
		global $mydb;
		return $mydb->getFieldsOnOneTable(self::$tbl_name);
	}

	function listOfAnnouncements(){
		global $mydb;
		$mydb->setQuery("SELECT * FROM ".self::$tbl_name." ORDER BY `DATE_POSTED` DESC, `ANNOUNCEMENT_ID` DESC");
		return $mydb->loadResultList();
	}

	/* Ginagamit ng public homepage - 3 pinaka-bagong Published lang
	   ang ipinapakita, base sa DATE_POSTED. */
	function latestPublished($limit = 3){
		global $mydb;
		if (!$mydb->tableExists(self::$tbl_name)) { return array(); }
		$limit = intval($limit);
		$mydb->setQuery("SELECT * FROM ".self::$tbl_name."
			WHERE `STATUS` = 'Published'
			ORDER BY `DATE_POSTED` DESC, `ANNOUNCEMENT_ID` DESC
			LIMIT {$limit}");
		return $mydb->loadResultList();
	}

	/* Ginagamit ng public homepage - LAHAT ng Published announcements,
	   pinaka-bago sa taas (walang LIMIT), base sa DATE_POSTED. */
	function allPublished(){
		global $mydb;
		if (!$mydb->tableExists(self::$tbl_name)) { return array(); }
		$mydb->setQuery("SELECT * FROM ".self::$tbl_name."
			WHERE `STATUS` = 'Published'
			ORDER BY `DATE_POSTED` DESC, `ANNOUNCEMENT_ID` DESC");
		return $mydb->loadResultList();
	}

	function single_announcement($id=0){
		global $mydb;
		$id = intval($id);
		$mydb->setQuery("SELECT * FROM ".self::$tbl_name." WHERE ANNOUNCEMENT_ID = {$id} LIMIT 1");
		return $mydb->loadSingleResult();
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
		$attributes = array();
		foreach($this->db_fields() as $field) {
			if(property_exists($this, $field)) {
				if($this->$field === null){
					continue;
				}
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

	public function create() {
		global $mydb;
		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$tbl_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		return $mydb->InsertThis($sql);
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
		$sql .= " WHERE ANNOUNCEMENT_ID = ". intval($id);
		return $mydb->InsertThis($sql);
	}

	public function delete($id=0) {
		global $mydb;
		$sql = "DELETE FROM ".self::$tbl_name;
		$sql .= " WHERE ANNOUNCEMENT_ID = ". intval($id);
		$sql .= " LIMIT 1 ";
		return $mydb->InsertThis($sql);
	}
}
?>
