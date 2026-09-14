<?php

require_once("../../include/initialize.php");
global $mydb;

if (isset($_POST['COURSE_ID'])) {
	$output = array();
	$query =	"SELECT * FROM tblcourses
		WHERE COURSE_ID = '".$_POST["COURSE_ID"]."'
		LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach($result as $row)
	{
		$output["COURSE_ID"] = $row->COURSE_ID;
		$output["COURSE_CODE"] = $row->COURSE_CODE;
		$output["COURSE_NAME"] = $row->COURSE_NAME;
		$output["COURSE_DESC"] = $row->COURSE_DESC;
		$output["STATUS"] = $row->STATUS;
		$output["PROGRAM_HEAD_ID"] = $row->PROGRAM_HEAD_ID;
	}
	echo json_encode($output);
}else{
	$output = array();
	// HIPANAO SOLUTIONS - LEFT JOIN papunta sa tblusers para makuha
	// ang pangalan ng Program Head na naka-assign sa course (kung
	// meron). LEFT JOIN para lumabas pa rin ang course kahit wala
	// pang napiling Program Head.
	$query = "SELECT tblcourses.*, tblusers.DISPLAYNAME AS PROGRAM_HEAD_NAME
			FROM `tblcourses`
			LEFT JOIN `tblusers` ON tblusers.UID = tblcourses.PROGRAM_HEAD_ID ";

	if(isset($_POST["search"]["value"]))
	{
	$query .= " where `COURSE_NAME` LIKE '%".$_POST["search"]["value"]."%' OR `COURSE_CODE` LIKE '%".$_POST["search"]["value"]."%' OR tblusers.DISPLAYNAME LIKE '%".$_POST["search"]["value"]."%' ";
	}
	if(isset($_POST["order"]))
	{
		$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
	}
	else
	{
		$query .= 'ORDER BY `COURSE_NAME` ASC ';
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

		$sub_array[] = $i;
		$sub_array[] = $result->COURSE_CODE;
		$sub_array[] = $result->COURSE_NAME;
		$sub_array[] = $result->COURSE_DESC;
		$sub_array[] = hipanao_badge($result->STATUS);
		$sub_array[] = !empty($result->PROGRAM_HEAD_NAME) ? $result->PROGRAM_HEAD_NAME : '<span class="text-muted">— Not assigned —</span>';
		$sub_array[] = '<div class="text-nowrap">'
			.hipanao_action_btn('edit', 'COURSE_ID', $result->COURSE_ID, 'editEntry')
			.hipanao_action_btn('delete', 'COURSE_ID', $result->COURSE_ID, 'deleteEntry')
			.'</div>';
		$data[] = $sub_array;
	$i = $i + 1;
	}
	function get_total_all_records()
	{
		global $mydb;
		$statement = "SELECT * FROM `tblcourses`";
		$mydb->setQuery($statement);
		return $mydb->num_rows();
	}

	$output = array('data' 			   => $data,
					"recordsTotal"	   => $filtered_rows,
					"recordsFiltered"	=>	get_total_all_records() );
	echo json_encode($output);
}
?>
