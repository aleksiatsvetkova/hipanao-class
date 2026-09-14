<?php

require_once("../../include/initialize.php");
global $mydb;

/* HIPANAO SOLUTIONS - "Invalid JSON response" fix.
   Kapag may kahit anong PHP warning/notice na na-print BAGO ang
   json_encode() (halimbawa: hindi pa na-import ang tblannouncements.sql,
   o may PHP notice), nasisira ang buong JSON response at doon lumalabas
   ang "DataTables warning: Invalid JSON response" sa browser.
   Kaya dito, kino-CAPTURE muna natin ang kahit anong output gamit ang
   ob_start(), at bago mag-echo ng totoong JSON, dini-DISCARD muna ang
   anumang stray text (ob_end_clean()) - laging malinis na JSON lang
   ang naka-echo palabas. */
ob_start();
error_reporting(0);
ini_set('display_errors', '0');

/* Kung hindi pa na-import ang database/tblannouncements.sql, huwag
   nang subukang mag-query sa table na wala pa - direkta nang mag-
   return ng malinis na JSON error (may makakalaman message) sa halip
   na hayaang mag-exit ng raw text ang database layer (na siyang
   dahilan ng "Invalid JSON response"). */
if (!$mydb->tableExists('tblannouncements')) {
	ob_end_clean();
	header('Content-Type: application/json');
	echo json_encode(array(
		'data'            => array(),
		'recordsTotal'    => 0,
		'recordsFiltered' => 0,
		'error'           => 'Hindi pa na-import ang database/tblannouncements.sql. I-import muna ito sa phpMyAdmin (piliin ang alumni_db database bago i-import).'
	));
	exit;
}

if (isset($_POST['ANNOUNCEMENT_ID'])) {
	$output = array();
	$id = intval($_POST['ANNOUNCEMENT_ID']);
	$query = "SELECT * FROM tblannouncements WHERE ANNOUNCEMENT_ID = '{$id}' LIMIT 1";
	$mydb->setQuery($query);
	$result = $mydb->loadResultList();

	foreach ($result as $row) {
		$output["ANNOUNCEMENT_ID"] = $row->ANNOUNCEMENT_ID;
		$output["TITLE"]           = $row->TITLE;
		$output["CONTENT"]         = $row->CONTENT;
		$output["DATE_POSTED"]     = $row->DATE_POSTED;
		$output["STATUS"]          = $row->STATUS;
		$output["PICTURE_URL"]     = (isset($row->PICTURE) && $row->PICTURE != '')
			? WEB_ROOT.'module/announcement/'.$row->PICTURE
			: WEB_ROOT.'no.png';
		$output["VIDEO_URL"]       = (isset($row->VIDEO) && $row->VIDEO != '')
			? WEB_ROOT.'module/announcement/'.$row->VIDEO
			: '';
	}
	ob_end_clean();
	header('Content-Type: application/json');
	echo json_encode($output);

} else {

	$output = array();
	$query = "SELECT * FROM `tblannouncements` ";

	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$kw = $mydb->escape_value($_POST["search"]["value"]);
		$query .= " WHERE `TITLE` LIKE '%".$kw."%' ";
	}
	if (isset($_POST["order"])) {
		$columns = array(0 => 'ANNOUNCEMENT_ID', 1 => 'PICTURE', 2 => 'TITLE', 3 => 'STATUS', 4 => 'DATE_POSTED', 5 => 'ANNOUNCEMENT_ID');
		$colIdx  = intval($_POST['order']['0']['column']);
		$colName = isset($columns[$colIdx]) ? $columns[$colIdx] : 'DATE_POSTED';
		$dir     = ($_POST['order']['0']['dir'] == 'asc') ? 'ASC' : 'DESC';
		$query .= "ORDER BY `{$colName}` {$dir} ";
	} else {
		$query .= 'ORDER BY `DATE_POSTED` DESC ';
	}
	if ($_POST["length"] != -1) {
		$query .= " LIMIT " . intval($_POST['start']) . ", " . intval($_POST['length']) . "";
	}

	$mydb->setQuery($query);
	$cur = $mydb->loadResultList();
	$data = array();
	$filtered_rows = $mydb->num_rows();
	$i = 1;
	foreach ($cur as $result) {

		$sub_array = array();

		$photoFile = (isset($result->PICTURE) && $result->PICTURE != '')
			? WEB_ROOT.'module/announcement/'.$result->PICTURE
			: WEB_ROOT.'no.png';

		$hasVideo = (isset($result->VIDEO) && $result->VIDEO != '');

		$sub_array[] = $i;
		$sub_array[] = '<div style="position:relative;width:60px;height:45px;">'
			.'<img src="'.$photoFile.'" style="width:60px;height:45px;object-fit:cover;border-radius:4px;" onerror="this.src=\''.WEB_ROOT.'no.png\'">'
			.($hasVideo ? '<i class="fa fa-play-circle" title="Has video" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;text-shadow:0 0 4px rgba(0,0,0,.8);font-size:18px;"></i>' : '')
			.'</div>';
		$sub_array[] = htmlspecialchars($result->TITLE);
		$sub_array[] = hipanao_badge($result->STATUS);
		$sub_array[] = date("F d, Y", strtotime($result->DATE_POSTED));
		$sub_array[] = '<div class="text-nowrap">'
			.hipanao_action_btn('edit', 'ANNOUNCEMENT_ID', $result->ANNOUNCEMENT_ID, 'editEntry')
			.hipanao_action_btn('delete', 'ANNOUNCEMENT_ID', $result->ANNOUNCEMENT_ID, 'deleteEntry')
			.'</div>';
		$data[] = $sub_array;
		$i = $i + 1;
	}

	function get_total_all_announcements() {
		global $mydb;
		$mydb->setQuery("SELECT * FROM `tblannouncements`");
		return $mydb->num_rows();
	}

	$output = array(
		'data'            => $data,
		"recordsTotal"    => $filtered_rows,
		"recordsFiltered" => get_total_all_announcements()
	);
	ob_end_clean();
	header('Content-Type: application/json');
	echo json_encode($output);
}
?>
