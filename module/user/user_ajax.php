<?php 

require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['UID'])) {
	$output = array();
	$query =	"SELECT * FROM tblusers 
		WHERE UID = '".$_POST["UID"]."' 
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{ 
		$output["UID"] = $row->UID;
		$output["DISPLAYNAME"] = $row->DISPLAYNAME;
		$output["USERNAME"] = $row->USERNAME;
		$output["TYPE"] = $row->TYPE;
		$output["STATUSACTIVE"] = $row->STATUSACTIVE;
		$output["PICTURE_URL"] = (isset($row->PICTURE) && $row->PICTURE != '')
			? WEB_ROOT.'module/user/'.$row->PICTURE
			: WEB_ROOT.'module/user/images/default.png';
				
	}
	echo json_encode($output);
}else{
	$output = array();
	$query = "SELECT * FROM `tblusers` ";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where `USERNAME` LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY `UID` DESC ';
	}
	if($_POST["length"] != -1)
	{
		$query .= " LIMIT " . $_POST['start'] . ", " . $_POST['length'] . "";
	}
	$mydb->setQuery($query);
	$cur = $mydb->loadResultList();
	$data = array();
	$filtered_rows = $mydb->num_rows();
	$i = 1;	
	foreach ($cur as $result) {
				
	$sub_array = array();

		$photoFile = (isset($result->PICTURE) && $result->PICTURE != '') ? WEB_ROOT.'module/user/'.$result->PICTURE : WEB_ROOT.'module/user/images/default.png';

		$sub_array[] =$i;
		$sub_array[] = '<div class="hipanao-cell-user"><img src="'.$photoFile.'" class="hipanao-avatar" onerror="this.src=\''.WEB_ROOT.'module/user/images/default.png\'"><span>'.htmlspecialchars($result->DISPLAYNAME).'</span></div>';
		$sub_array[] = htmlspecialchars($result->USERNAME);
		$sub_array[] = hipanao_badge($result->TYPE);
		$sub_array[] = $result->DATEADDED;
		$sub_array[] = $result->DATEMODIFIED;
		$sub_array[] = '<div class="text-nowrap">'
			.hipanao_action_btn('edit', 'UID', $result->UID, 'editEntry')
			.hipanao_action_btn('key', 'UID', $result->UID, 'changepass')
			.hipanao_action_btn('delete', 'UID', $result->UID, 'deleteEntry')
			.'</div>';
		$data[] = $sub_array;
	$i = $i + 1;		
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT * FROM `tblusers`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data, 
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>