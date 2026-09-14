<?php

require_once ("../../include/initialize.php");

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
	doInsert();
	break;

	case 'edit' :
	doEdit();
	break;

	case 'delete' :
	doDelete();
	break;
}

/* Parehong logic ng module/user/controller.php > handleUserPhotoUpload() -
   kinukumpirma muna kung tunay na image ang na-upload bago i-move papunta
   sa module/announcement/images/. Blangko lang ang PICTURE kung walang
   pinili (opsyonal ang picture ng announcement). */
function handleAnnouncementPhotoUpload(){

	if (!isset($_FILES['PICTURE']) || $_FILES['PICTURE']['error'] === UPLOAD_ERR_NO_FILE) {
		return '';
	}
	if ($_FILES['PICTURE']['error'] !== UPLOAD_ERR_OK) {
		return false;
	}

	$tmpName   = $_FILES['PICTURE']['tmp_name'];
	$imageInfo = @getimagesize($tmpName);
	if ($imageInfo === false) {
		return false;
	}

	$allowedExt = array('jpg' => true, 'jpeg' => true, 'png' => true, 'gif' => true, 'webp' => true);
	$ext = strtolower(pathinfo($_FILES['PICTURE']['name'], PATHINFO_EXTENSION));
	if (!isset($allowedExt[$ext])) {
		return false;
	}

	$uploadDir = __DIR__ . DS . 'images' . DS;
	if (!is_dir($uploadDir)) {
		@mkdir($uploadDir, 0755, true);
	}

	$fileName = 'announcement_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;

	if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
		return 'images/' . $fileName;
	}
	return false;
}

/* Parehong pattern ng handleAnnouncementPhotoUpload() sa itaas, pero para
   sa VIDEO field - opsyonal din ito. Chineck ang tunay na mime type
   (gamit ang finfo, kasabay ng extension check) bago i-move papunta sa
   module/announcement/videos/, para hindi basta makapag-upload ng ibang
   klaseng file na pinalitan lang ng extension. */
function handleAnnouncementVideoUpload(){

	if (!isset($_FILES['VIDEO']) || $_FILES['VIDEO']['error'] === UPLOAD_ERR_NO_FILE) {
		return '';
	}
	if ($_FILES['VIDEO']['error'] !== UPLOAD_ERR_OK) {
		return false;
	}

	$tmpName = $_FILES['VIDEO']['tmp_name'];

	$allowedTypes = array(
		'mp4'  => array('video/mp4'),
		'webm' => array('video/webm'),
		'ogg'  => array('video/ogg'),
		'ogv'  => array('video/ogg'),
		'mov'  => array('video/quicktime'),
	);
	$ext = strtolower(pathinfo($_FILES['VIDEO']['name'], PATHINFO_EXTENSION));
	if (!isset($allowedTypes[$ext])) {
		return false;
	}

	if (function_exists('finfo_open')) {
		$finfo    = finfo_open(FILEINFO_MIME_TYPE);
		$mimeType = finfo_file($finfo, $tmpName);
		finfo_close($finfo);
		if (!in_array($mimeType, $allowedTypes[$ext])) {
			return false;
		}
	}

	$uploadDir = __DIR__ . DS . 'videos' . DS;
	if (!is_dir($uploadDir)) {
		@mkdir($uploadDir, 0755, true);
	}

	$fileName = 'announcement_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;

	if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
		return 'videos/' . $fileName;
	}
	return false;
}

function doInsert(){

	if (!isset($_POST['save'])) { return; }

	$announcement = new Announcement();

	$TITLE       = trim($_POST['TITLE']);
	$CONTENT     = trim($_POST['CONTENT']);
	$DATE_POSTED = $_POST['DATE_POSTED'];
	$STATUS      = $_POST['STATUS'];

	if ($TITLE == '' || $CONTENT == '' || $DATE_POSTED == '') {
		message("Title, Content, and Date Posted are required!", "error");
		redirect('index.php');
		return;
	}

	$photo = handleAnnouncementPhotoUpload();
	if ($photo === false) {
		message("Uploaded picture is not a valid image!", "error");
		redirect('index.php');
		return;
	}

	$video = handleAnnouncementVideoUpload();
	if ($video === false) {
		message("Uploaded video is not a valid video file! (MP4, WEBM, OGG, or MOV only.)", "error");
		redirect('index.php');
		return;
	}

	$announcement->TITLE        = $TITLE;
	$announcement->CONTENT      = $CONTENT;
	$announcement->DATE_POSTED  = $DATE_POSTED;
	$announcement->STATUS       = $STATUS;
	$announcement->DATEADDED    = date("Y-m-d H:i:s");
	$announcement->ADDEDBY      = isset($_SESSION['UID']) ? $_SESSION['UID'] : null;
	if ($photo !== '') {
		$announcement->PICTURE = $photo;
	}
	if ($video !== '') {
		$announcement->VIDEO = $video;
	}

	$istrue = $announcement->create();

	if ($istrue == true) {
		message("New announcement [". $TITLE ."] has been posted successfully!", "success");
		redirect('index.php');
	} else {
		message("No announcement has been created successfully!", "error");
		redirect('index.php');
	}
}

function doEdit(){

	if (!isset($_POST['edit'])) { return; }

	$announcement = new Announcement();
	$ANNOUNCEMENT_ID = intval($_POST['ANNOUNCEMENT_ID']);

	$TITLE       = trim($_POST['TITLE']);
	$CONTENT     = trim($_POST['CONTENT']);
	$DATE_POSTED = $_POST['DATE_POSTED'];
	$STATUS      = $_POST['STATUS'];

	if ($TITLE == '' || $CONTENT == '' || $DATE_POSTED == '') {
		message("Title, Content, and Date Posted are required!", "error");
		redirect('index.php');
		return;
	}

	$photo = handleAnnouncementPhotoUpload();
	if ($photo === false) {
		message("Uploaded picture is not a valid image!", "error");
		redirect('index.php');
		return;
	}

	$video = handleAnnouncementVideoUpload();
	if ($video === false) {
		message("Uploaded video is not a valid video file! (MP4, WEBM, OGG, or MOV only.)", "error");
		redirect('index.php');
		return;
	}

	$announcement->TITLE         = $TITLE;
	$announcement->CONTENT       = $CONTENT;
	$announcement->DATE_POSTED   = $DATE_POSTED;
	$announcement->STATUS        = $STATUS;
	$announcement->DATEMODIFIED  = date("Y-m-d H:i:s");
	$announcement->MODIFIEDBY    = isset($_SESSION['UID']) ? $_SESSION['UID'] : null;
	if ($photo !== '') {
		$announcement->PICTURE = $photo;
	}
	if ($video !== '') {
		$announcement->VIDEO = $video;
	}

	$istrue = $announcement->update($ANNOUNCEMENT_ID);

	if ($istrue == true) {
		message("Announcement has been updated successfully!", "success");
		redirect('index.php');
	} else {
		message("No announcement has been updated successfully!", "error");
		redirect('index.php');
	}
}

function doDelete(){
	$id = intval($_GET['id']);
	$announcement = new Announcement();
	$announcement->delete($id);

	message("Announcement has been deleted!", "success");
	redirect('index.php');
}

?>
