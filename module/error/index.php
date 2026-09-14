<?php

require_once("../../include/initialize.php");

$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : '';
 $title="404 Error Page"; 
 $header=$view; 
switch ($view) {
	case 'list' :
		$content    = 'list.php';		
		break;

	default :
		$content    = 'list.php';		
}
require_once ("../../theme/template.php");

?>
  