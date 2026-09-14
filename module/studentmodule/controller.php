<?php

/* HIPANAO SOLUTIONS - Student Module > My Profile / My Account.
   ==========================================================
   Hiwalay na maliit na controller ito (kagaya ng module/student at
   module/user), pero para lang sa mga bagay na pwedeng gawin ng isang
   naka-login na Student sa sarili niyang login account:

     - editphoto       -> palitan ang sarili niyang profile photo
                           (tinatawag mula sa "My Profile" AT "My Account").
     - changepassword   -> palitan ang sarili niyang password
                           (tinatawag mula sa "My Account" LANG).

   Security: HINDI kinukuha ang S_ID/UID mula sa POST/GET - laging galing
   ito sa SESSION (STUDENT_SID / UID, itinakda noong pag-login - tingnan
   include/user.php AuthenticateUser()). Kaya't hindi kayang i-edit ng
   isang estudyante ang litrato o password ng ibang account kahit anong
   gawin niya sa request.

   Pagkatapos ma-save sa tblstudent.COMPANYIDNO, tinatawag ang parehong
   hipanao_sync_student_photo_to_user() (include/photo_sync.php) na
   ginagamit din ng module/student - kaya awtomatiko itong sasalamin
   din sa naka-link na login account (tblusers.PICTURE) pati sa
   $_SESSION['PICTURE'], para makita agad sa dashboard header nang hindi
   na kailangang mag-logout/login pa.
   ========================================================== */

require_once("../../include/initialize.php");

if (!isset($_SESSION['UID']) || !isset($_SESSION['TYPE']) || $_SESSION['TYPE'] !== 'Student') {
	redirect(WEB_ROOT."login.php");
	exit;
}

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'editphoto' :
	doEditPhoto();
	break;
	case 'changepassword' :
	doChangePassword();
	break;
}

/* Saan babalik pagkatapos ng editphoto/changepassword - "profile" o
   "account" lang ang pinapayagan (whitelist), kaya hindi ito magagamit
   para mag-redirect papunta sa labas ng module na ito. */
function studentmodule_safe_redirect_view($default = 'profile'){
	$allowed = array('profile' => true, 'account' => true);
	$view = isset($_POST['REDIRECT_TO']) ? $_POST['REDIRECT_TO'] : $default;
	return isset($allowed[$view]) ? $view : $default;
}

function doEditPhoto(){

	global $mydb;

	$sid = isset($_SESSION['STUDENT_SID']) ? intval($_SESSION['STUDENT_SID']) : 0;
	$backTo = studentmodule_safe_redirect_view('profile');

	if ($sid <= 0) {
		message("We could not find your student record. Please contact the Registrar Office.", "error");
		redirect('index.php?view='.$backTo);
		return;
	}

	if (!isset($_FILES['PICTURE']) || $_FILES['PICTURE']['error'] === UPLOAD_ERR_NO_FILE) {
		message("Please choose a photo to upload.", "error");
		redirect('index.php?view='.$backTo);
		return;
	}

	if ($_FILES['PICTURE']['error'] !== UPLOAD_ERR_OK) {
		message("The photo upload failed. Please try again.", "error");
		redirect('index.php?view='.$backTo);
		return;
	}

	$tmpName   = $_FILES['PICTURE']['tmp_name'];
	$imageInfo = @getimagesize($tmpName);
	if ($imageInfo === false) {
		message("Uploaded file is not a valid image!", "error");
		redirect('index.php?view='.$backTo);
		return;
	}

	$allowedExt = array('jpg' => true, 'jpeg' => true, 'png' => true, 'gif' => true, 'webp' => true);
	$ext = strtolower(pathinfo($_FILES['PICTURE']['name'], PATHINFO_EXTENSION));
	if (!isset($allowedExt[$ext])) {
		message("Unsupported image type. Please use JPG, PNG, GIF, or WEBP.", "error");
		redirect('index.php?view='.$backTo);
		return;
	}

	$uploadDir = SITE_ROOT.DS.'module'.DS.'student'.DS.'image'.DS;
	if (!is_dir($uploadDir)) {
		@mkdir($uploadDir, 0755, true);
	}

	$fileName = 'student_'.date('YmdHis').'_'.mt_rand(1000, 9999).'.'.$ext;

	if (!move_uploaded_file($tmpName, $uploadDir.$fileName)) {
		message("The photo could not be saved. Please try again.", "error");
		redirect('index.php?view='.$backTo);
		return;
	}

	$mydb->InsertThis("UPDATE `tblstudent` SET `COMPANYIDNO` = '".$mydb->escape_value($fileName)."' WHERE `S_ID` = '".$sid."'");

	/* I-sync din agad papunta sa sariling login account (tblusers.PICTURE)
	   + $_SESSION['PICTURE'] - HIPANAO SOLUTIONS. */
	hipanao_sync_student_photo_to_user($sid);

	message("Profile photo updated!", "success");
	redirect('index.php?view='.$backTo);
}

/* HIPANAO SOLUTIONS - Student Module > My Account > Change Password.
   Ang UID ay LAGING galing sa SESSION (hindi sa POST) - kaya't hindi
   makakabago ang estudyante ng password ng ibang account. Kailangan
   munang itugma ang "current password" (sha1 compare) sa tblusers.PASSWORD
   bago tanggapin ang bago. */
function doChangePassword(){

	global $mydb;

	$uid = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : 0;

	$currentPassword = isset($_POST['CURRENT_PASSWORD']) ? $_POST['CURRENT_PASSWORD'] : '';
	$newPassword      = isset($_POST['NEW_PASSWORD']) ? $_POST['NEW_PASSWORD'] : '';
	$confirmPassword  = isset($_POST['CONFIRM_PASSWORD']) ? $_POST['CONFIRM_PASSWORD'] : '';

	if ($uid <= 0) {
		message("We could not find your account. Please log in again.", "error");
		redirect('index.php?view=account');
		return;
	}

	if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
		message("Please fill out all password fields.", "error");
		redirect('index.php?view=account');
		return;
	}

	if (strlen($newPassword) < 6) {
		message("New password must be at least 6 characters long.", "error");
		redirect('index.php?view=account');
		return;
	}

	if ($newPassword !== $confirmPassword) {
		message("New password and confirmation do not match.", "error");
		redirect('index.php?view=account');
		return;
	}

	$mydb->setQuery("SELECT `UID` FROM `tblusers` WHERE `UID` = '".$uid."' AND `PASSWORD` = '".sha1($currentPassword)."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) < 1) {
		message("Your current password is incorrect.", "error");
		redirect('index.php?view=account');
		return;
	}

	$mydb->InsertThis("UPDATE `tblusers` SET `PASSWORD` = '".$mydb->escape_value(sha1($newPassword))."', `DATEMODIFIED` = '".date('Y-m-d')."', `MODIFIEDBY` = '".$uid."' WHERE `UID` = '".$uid."'");

	message("Password updated successfully!", "success");
	redirect('index.php?view=account');
}

?>
