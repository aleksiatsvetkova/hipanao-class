<?php
require_once(LIB_PATH.DS."config.php");
class Database {
	var $sql_string = '';
	var $error_no = 0;
	var $error_msg = '';
	var $query = '';
	public $conn;
	public $last_query;
	private $magic_quotes_active;
	private $real_escape_string_exists;
	
	function __construct() {
		$this->open_connection();
		$this->magic_quotes_active = function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc();
		$this->real_escape_string_exists = function_exists("mysql_real_escape_string");
	}
	
	public function open_connection() {

		/* "No connection could be made because the target machine
		   actively refused it" (SQLSTATE[HY000] [2002]) usually means
		   ONE of two things:
		     1. MySQL is simply not running in XAMPP - Control Panel >
		        MySQL > Start.
		     2. MySQL IS running, but the "localhost" host name tries a
		        named-pipe/socket connection on Windows that isn't set
		        up, while the TCP port (127.0.0.1) works fine. This is a
		        very common XAMPP-on-Windows gotcha.
		   So: try DB_SERVER as configured first, and if that specific
		   failure happens, automatically retry once over 127.0.0.1
		   before giving up - this alone fixes case #2 without anyone
		   having to edit config.php. */

		$hostsToTry = array(DB_SERVER);
		if (strtolower(DB_SERVER) === 'localhost' && !in_array('127.0.0.1', $hostsToTry)) {
			$hostsToTry[] = '127.0.0.1';
		}

		$lastError = null;
		foreach ($hostsToTry as $host) {
			try {
				$this->conn = new PDO("mysql:host=".$host.";dbname=".DB_NAME."", DB_USER, DB_PASS);
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				return; // connected successfully
			} catch (PDOException $e) {
				$lastError = $e;
				$this->conn = null;
			}
		}

		// Every host we tried failed - show one clear message and STOP.
		// (Previously the script kept running with $this->conn === null,
		// which just turned this into a second, uglier fatal error on
		// the very next query instead of a clean stop here.)
		http_response_code(500);
		echo '<div style="font-family: sans-serif; max-width: 640px; margin: 60px auto; padding: 20px 24px; border: 1px solid #f1b0b7; background: #fff3f3; border-radius: 6px; color: #58151c;">';
		echo '<h2 style="margin-top:0;">Could not connect to the database</h2>';
		echo '<p>Please check in XAMPP Control Panel:</p>';
		echo '<ol>';
		echo '<li>Is <strong>MySQL</strong> started (green/Running)? If not, click <strong>Start</strong>.</li>';
		echo '<li>Is Apache/MySQL logging a port conflict? Check the MySQL "Logs" button in XAMPP if it will not stay started.</li>';
		echo '<li>Was the <code>alumni_db</code> database imported yet (database/alumni_db.sql via phpMyAdmin)?</li>';
		echo '</ol>';
		echo '<p style="color:#888; font-size: 0.9em;">Technical detail: '.htmlspecialchars($lastError ? $lastError->getMessage() : 'Unknown connection error').'</p>';
		echo '</div>';
		exit;
	}
	
	function InsertThis($sql='') {
		$this->sql_string=$sql;
		$this->query = $this->conn->prepare($this->sql_string);
		
		if($this->query->execute()) {
	   	 	return true;
	 	 } else {
	    	return false;
	  	}
	}
	
	function setQuery($sql='') {
		$this->sql_string = $sql;
		$this->error_no  = 0;
		$this->error_msg = '';
		try {
			$this->query = $this->conn->prepare($this->sql_string);
			$this->query->execute();

		  } catch (PDOException $e) {
		    /* HIPANAO SOLUTIONS - Isang section lang dapat ang masisira
		       kapag may query na nabigo (hal. kulang/hindi pa na-import
		       na table), hindi dapat ang buong pahina. Kaya sa halip na
		       mag-exit dito, itatago na lang natin ang error at
		       iiwang null ang $this->query. Ang mga loadResultList(),
		       loadSingleResult(), atbp. sa ibaba ay may guard na para
		       hindi na mag-fatal error, blangkong resulta na lang ang
		       ibabalik nila kapag na-fail ang query na ito. */
		    $this->error_no  = (int) $e->getCode();
		    $this->error_msg = $e->getMessage();
		    $this->query = null;
		  }
		
	}

	/* True kapag na-fail ang pinaka-huling setQuery() call. Pwede itong
	   tignan ng ibang module para magpakita ng maliit, naka-loob na
	   babala sa loob lang ng section na iyon, sa halip na hayaang
	   blangko na lang o palusutin bilang zero/walang laman ang resulta. */
	public function hasError() {
		return $this->error_msg !== '';
	}

	public function getErrorMessage() {
		return $this->error_msg;
	}
	
	function loadResultList() {
		if (!$this->query) { return array(); }
		$results = $this->query->fetchAll(PDO::FETCH_OBJ);
		return $results;
	}	
	function loadSingleResultAssoc() {
		if (!$this->query) { return null; }
		$results = $this->query->fetch(PDO::FETCH_ASSOC);
		return $results;
	}	
	
	public function num_rows() {
		if (!$this->query) { return 0; }
		return $this->query->rowCount();
	}

	function loadSingleResult() {
		if (!$this->query) { return null; }
		$results = $this->query->fetch(PDO::FETCH_OBJ);
		return $results;
	}
	
	function getFieldsOnOneTable( $tbl_name ) {
	
		$this->setQuery("DESC ".$tbl_name);
		$rows = $this->loadResultList();
		
		$f = array();
		for ( $x=0; $x<count( $rows ); $x++ ) {
			$f[] = $rows[$x]->Field;
		}
		
		return $f;
	}	

	function describeTable( $tbl_name ) {
		$this->setQuery("DESC `".str_replace('`','',$tbl_name)."`");
		return $this->loadResultList();
	}

	function getPrimaryKey( $tbl_name ) {
		$cols = $this->describeTable($tbl_name);
		foreach ($cols as $c) {
			if ($c->Key === 'PRI') {
				return $c->Field;
			}
		}
		return isset($cols[0]) ? $cols[0]->Field : null;
	}

	function tableExists( $tbl_name ) {
		$this->setQuery("SHOW TABLES LIKE '".str_replace(array('`',"'"),'',$tbl_name)."'");
		return $this->num_rows() > 0;
	}

	public function fetch_array($result) {
		return mysqli_fetch_array($result);
	}
	
	public function insert_id() {
    
		return $this->conn->lastInsertId();
	}
  
	public function affected_rows() {
		return mysqli_affected_rows($this->conn);
	}
	
	 public function escape_value( $value ) {
		if( $this->real_escape_string_exists ) { 
			
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysql_real_escape_string( $value );
		} else { 
			
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			
		}
		return $value;
   	}
	
	public function close_connection() {
		$conn = null;
	}
	
} 
$mydb = new Database();

?>
