<?php

/* HIPANAO SOLUTIONS - Program Head Module > controller.php
   ==========================================================
   Lahat ng aksyon dito ay para lang sa mga bagay na dapat kayang
   gawin ng isang Program Head:
     - addsection      -> gumawa ng bagong Section PARA SA SARILI
                           niyang course.
     - assignsection   -> i-set ang Section ng isang estudyante -
                           dapat ang estudyante ay naka-enroll sa
                           SARILI niyang course, AT ang section ay
                           SARILI ring niyang course.
     - savegrades      -> i-encode ang grades ng isang estudyante -
                           dapat ang enrollment ay SARILI niyang
                           course.
     - editphoto       -> palitan ang SARILI niyang profile photo
                           (tblusers.PICTURE, WHERE UID = SESSION).
     - changepassword  -> palitan ang SARILI niyang password
                           (tblusers.PASSWORD, WHERE UID = SESSION).

   Ang COURSE_ID niya ay LAGING kinukuha via lookup gamit ang
   SESSION['UID'] (tblcourses.PROGRAM_HEAD_ID) - hindi kailanman sa
   POST - kaya hindi niya kayang galawin ang datos ng ibang course.
   ========================================================== */

require_once("../../include/initialize.php");
global $mydb;

if (!isset($_SESSION['UID']) || !isset($_SESSION['TYPE']) || $_SESSION['TYPE'] !== 'Program Head') {
	redirect(WEB_ROOT."login.php");
	exit;
}

$phUid = intval($_SESSION['UID']);

function ph_get_course_id() {
	global $mydb, $phUid;
	$mydb->setQuery("SELECT COURSE_ID FROM `tblcourses` WHERE PROGRAM_HEAD_ID = '".$phUid."' LIMIT 1");
	$rows = $mydb->loadResultList();
	return (count($rows) >= 1) ? intval($rows[0]->COURSE_ID) : 0;
}

$action = isset($_GET['action']) && $_GET['action'] != '' ? $_GET['action'] : '';

switch ($action) {
	case 'addsection':
		doAddSection();
		break;
	case 'addsubject':
		doAddSubject();
		break;
	case 'editsubject':
		doEditSubject();
		break;
	case 'deletesubject':
		doDeleteSubject();
		break;
	case 'managesubjects':
		doManageSubjects();
		break;
	case 'assignsection':
		doAssignSection();
		break;
	case 'savegrades':
		doSaveGrades();
		break;
	case 'editphoto':
		doEditPhoto();
		break;
	case 'changepassword':
		doChangePassword();
		break;
	default:
		redirect('index.php');
}

/* ---------------------------------------------------------
   ADD SECTION - COURSE_ID ay laging sarili niyang course
   (ph_get_course_id()), hindi galing sa POST.
   --------------------------------------------------------- */
function doAddSection() {
	global $mydb;

	$courseId = ph_get_course_id();
	if ($courseId <= 0) {
		message("You are not yet assigned as Program Head of any course.", "error");
		redirect('index.php?view=sections');
		return;
	}

	$name      = isset($_POST['SEC_NAME']) ? trim($_POST['SEC_NAME']) : '';
	$yearLevel = isset($_POST['SEC_YEARLEVEL']) ? trim($_POST['SEC_YEARLEVEL']) : '';
	$syId      = isset($_POST['SEC_SY']) ? intval($_POST['SEC_SY']) : 0;

	if ($name === '' || $yearLevel === '' || $syId <= 0) {
		message("Please complete all the required fields.", "error");
		redirect('index.php?view=sections');
		return;
	}

	$mydb->setQuery("SELECT SECTION_ID FROM `tblsections`
		WHERE COURSE_ID = '".$courseId."' AND SY_ID = '".$syId."'
		  AND YEAR_LEVEL = '".$mydb->escape_value($yearLevel)."'
		  AND SECTION_NAME = '".$mydb->escape_value($name)."' LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("That section already exists for this course, year level, and school year.", "error");
		redirect('index.php?view=sections');
		return;
	}

	$ok = $mydb->InsertThis("INSERT INTO `tblsections` (SECTION_NAME, COURSE_ID, SY_ID, YEAR_LEVEL)
		VALUES ('".$mydb->escape_value($name)."', '".$courseId."', '".$syId."', '".$mydb->escape_value($yearLevel)."')");

	if ($ok) {
		message("Section added successfully!", "success");
	} else {
		message("The section could not be added.", "error");
	}
	redirect('index.php?view=sections');
}

/* ---------------------------------------------------------
   ADD SUBJECT - "Subjects" tab. Kagaya ng doAddSection(), ang
   COURSE_ID ay LAGING sarili niyang course (ph_get_course_id()),
   hindi galing sa POST. Ang subject na ginagawa dito ay LAGING
   SUBJECT_TYPE = 'Major' - ang Minor subject (available sa lahat
   ng course, COURSE_ID = NULL) ay Admin-only pa rin, tingnan
   module/subject/.
   --------------------------------------------------------- */
function doAddSubject() {
	global $mydb;

	$courseId = ph_get_course_id();
	if ($courseId <= 0) {
		message("You are not yet assigned as Program Head of any course.", "error");
		redirect('index.php?view=subjects');
		return;
	}

	$code      = isset($_POST['SUBJECT_CODE']) ? trim($_POST['SUBJECT_CODE']) : '';
	$name      = isset($_POST['SUBJECT_NAME']) ? trim($_POST['SUBJECT_NAME']) : '';
	$units     = isset($_POST['UNITS']) ? intval($_POST['UNITS']) : 0;
	$yearLevel = isset($_POST['YEAR_LEVEL']) ? trim($_POST['YEAR_LEVEL']) : '';
	$semester  = isset($_POST['SEMESTER']) ? trim($_POST['SEMESTER']) : '';

	if ($code === '' || $name === '' || $units <= 0 || $yearLevel === '' || $semester === '') {
		message("Please complete all the required fields.", "error");
		redirect('index.php?view=subjects');
		return;
	}

	$mydb->setQuery("SELECT SUBJECT_ID FROM `tblsubjects` WHERE SUBJECT_CODE = '".$mydb->escape_value($code)."' LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("Subject Code already exist!", "error");
		redirect('index.php?view=subjects');
		return;
	}

	$ok = $mydb->InsertThis("INSERT INTO `tblsubjects`
		(SUBJECT_CODE, SUBJECT_NAME, UNITS, COURSE_ID, YEAR_LEVEL, SEMESTER, SUBJECT_TYPE)
		VALUES (
			'".$mydb->escape_value($code)."',
			'".$mydb->escape_value($name)."',
			'".$units."',
			'".$courseId."',
			'".$mydb->escape_value($yearLevel)."',
			'".$mydb->escape_value($semester)."',
			'Major'
		)");

	if ($ok) {
		message("New Subject [".$code."] has been created successfully!", "success");
	} else {
		message("No subject has been created successfully!", "error");
	}
	redirect('index.php?view=subjects');
}

/* ---------------------------------------------------------
   EDIT SUBJECT - dapat sariling course niya (COURSE_ID =
   ph_get_course_id()) ang subject na ie-edit, kaya kasama ang
   "AND COURSE_ID = ..." sa WHERE ng UPDATE - kahit anong
   SUBJECT_ID ang ipasa sa POST, hindi niya kayang galawin ang
   Minor subject o subject ng ibang course.
   --------------------------------------------------------- */
function doEditSubject() {
	global $mydb;

	$courseId  = ph_get_course_id();
	$subjectId = isset($_POST['SUBJECT_ID']) ? intval($_POST['SUBJECT_ID']) : 0;

	if ($courseId <= 0 || $subjectId <= 0) {
		message("Invalid request.", "error");
		redirect('index.php?view=subjects');
		return;
	}

	$mydb->setQuery("SELECT SUBJECT_ID FROM `tblsubjects`
		WHERE SUBJECT_ID = '".$subjectId."' AND COURSE_ID = '".$courseId."' LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("That subject does not belong to your course.", "error");
		redirect('index.php?view=subjects');
		return;
	}

	$code      = isset($_POST['SUBJECT_CODE']) ? trim($_POST['SUBJECT_CODE']) : '';
	$name      = isset($_POST['SUBJECT_NAME']) ? trim($_POST['SUBJECT_NAME']) : '';
	$units     = isset($_POST['UNITS']) ? intval($_POST['UNITS']) : 0;
	$yearLevel = isset($_POST['YEAR_LEVEL']) ? trim($_POST['YEAR_LEVEL']) : '';
	$semester  = isset($_POST['SEMESTER']) ? trim($_POST['SEMESTER']) : '';

	if ($code === '' || $name === '' || $units <= 0 || $yearLevel === '' || $semester === '') {
		message("Please complete all the required fields.", "error");
		redirect('index.php?view=subjects');
		return;
	}

	$mydb->setQuery("SELECT SUBJECT_ID FROM `tblsubjects`
		WHERE SUBJECT_CODE = '".$mydb->escape_value($code)."' AND SUBJECT_ID != '".$subjectId."' LIMIT 1");
	if ($mydb->num_rows() >= 1) {
		message("Subject Code already exist!", "error");
		redirect('index.php?view=subjects');
		return;
	}

	$ok = $mydb->InsertThis("UPDATE `tblsubjects` SET
			SUBJECT_CODE = '".$mydb->escape_value($code)."',
			SUBJECT_NAME = '".$mydb->escape_value($name)."',
			UNITS        = '".$units."',
			YEAR_LEVEL   = '".$mydb->escape_value($yearLevel)."',
			SEMESTER     = '".$mydb->escape_value($semester)."'
		WHERE SUBJECT_ID = '".$subjectId."' AND COURSE_ID = '".$courseId."'");

	if ($ok) {
		message("Subject [".$code."] has been updated successfully!", "success");
	} else {
		message("No subject has been updated successfully!", "error");
	}
	redirect('index.php?view=subjects');
}

/* ---------------------------------------------------------
   DELETE SUBJECT - kagaya ng doEditSubject(), sariling course
   lang niya ang puwedeng tanggalin (COURSE_ID sa WHERE).
   --------------------------------------------------------- */
function doDeleteSubject() {
	global $mydb;

	$courseId  = ph_get_course_id();
	$subjectId = isset($_GET['id']) ? intval($_GET['id']) : 0;

	if ($courseId > 0 && $subjectId > 0) {
		$mydb->InsertThis("DELETE FROM `tblsubjects` WHERE SUBJECT_ID = '".$subjectId."' AND COURSE_ID = '".$courseId."'");
	}

	message("Subject already Deleted!", "success");
	redirect('index.php?view=subjects');
}

/* ---------------------------------------------------------
   MANAGE SUBJECTS ("Add Subject" sa Students list) - dapat
   sarili niyang course ang enrollment (Kagaya ng doAssignSection()
   sa itaas). Ang aktwal na pag-save (add/remove ng subjects sa
   tblenrollmentdetails) ay ginagawa ng saveEnrollmentSubjects()
   (include/functions.php) - iisa lang ito na function na ginagamit
   din ng Admin's Enrollment > Subjects at Enrollment Details >
   Manage Subjects, kaya iisa lang ding lugar ang nagde-decide kung
   anong subjects ang bilang sa isang enrollment. Ang function na
   iyon mismo ay awtomatiko nang na-lilimitahan sa Major subjects
   ng course ng ENROLLMENT (COURSE_ID nito) + lahat ng Minor
   subjects - dagdag pang proteksyon lang dito ang pag-check muna
   na sarili niyang course ang may-ari ng enrollment na ito.
   --------------------------------------------------------- */
function doManageSubjects() {
	global $mydb;

	$courseId = ph_get_course_id();
	$eid      = isset($_POST['MS_EID']) ? intval($_POST['MS_EID']) : 0;
	$posted   = isset($_POST['MS_SUBJECTS']) && is_array($_POST['MS_SUBJECTS']) ? $_POST['MS_SUBJECTS'] : array();

	if ($courseId <= 0 || $eid <= 0) {
		message("Invalid request.", "error");
		redirect('index.php');
		return;
	}

	// Ang enrollment record ay dapat SARILI niyang course.
	$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment`
		WHERE ENROLLMENT_ID = '".$eid."' AND COURSE_ID = '".$courseId."' LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("That student is not enrolled under your course.", "error");
		redirect('index.php');
		return;
	}

	$kept = saveEnrollmentSubjects($eid, $posted);

	if ($kept > 0) {
		message("Subjects updated (".$kept." subject(s) kept for this student).", "success");
	} else {
		message("All subjects were removed from this student's enrollment.", "info");
	}
	redirect('index.php');
}

/* ---------------------------------------------------------
   ASSIGN SECTION - dapat parehong sarili niyang course ang
   Enrollment record AT ang Section na pipiliin.
   --------------------------------------------------------- */
function doAssignSection() {
	global $mydb;

	$courseId   = ph_get_course_id();
	$eid        = isset($_POST['AS_EID']) ? intval($_POST['AS_EID']) : 0;
	$sectionId  = isset($_POST['AS_SECTION']) ? intval($_POST['AS_SECTION']) : 0;

	if ($courseId <= 0 || $eid <= 0) {
		message("Invalid request.", "error");
		redirect('index.php');
		return;
	}

	// Ang enrollment record ay dapat SARILI niyang course.
	$mydb->setQuery("SELECT ENROLLMENT_ID, SY_ID FROM `tblenrollment`
		WHERE ENROLLMENT_ID = '".$eid."' AND COURSE_ID = '".$courseId."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That student is not enrolled under your course.", "error");
		redirect('index.php');
		return;
	}
	$syId = intval($rows[0]->SY_ID);

	// Ang section (kung meron) ay dapat din SARILI niyang course + parehong school year.
	if ($sectionId > 0) {
		$mydb->setQuery("SELECT SECTION_ID FROM `tblsections`
			WHERE SECTION_ID = '".$sectionId."' AND COURSE_ID = '".$courseId."' AND SY_ID = '".$syId."' LIMIT 1");
		if ($mydb->num_rows() < 1) {
			message("That section does not belong to your course for this school year.", "error");
			redirect('index.php');
			return;
		}
	}

	$sectionSql = ($sectionId > 0) ? "'".$sectionId."'" : "NULL";

	if ($mydb->InsertThis("UPDATE `tblenrollment` SET SECTION_ID = ".$sectionSql." WHERE ENROLLMENT_ID = '".$eid."'")) {
		message("Section assigned successfully!", "success");
	} else {
		message("The section could not be assigned.", "error");
	}
	redirect('index.php');
}

/* ---------------------------------------------------------
   SAVE GRADES - dapat sarili niyang course ang enrollment.
   Kaparehong upsert logic ng module/grade/controller.php.
   --------------------------------------------------------- */
function doSaveGrades() {
	global $mydb;

	$courseId = ph_get_course_id();
	$eid = isset($_POST['GRADE_EID']) ? intval($_POST['GRADE_EID']) : 0;

	if ($courseId <= 0 || $eid <= 0) {
		message("No enrollment record selected.", "error");
		redirect('index.php?view=grades');
		return;
	}

	$mydb->setQuery("SELECT S_ID, SY_ID, SEMESTER FROM `tblenrollment`
		WHERE ENROLLMENT_ID = '".$eid."' AND COURSE_ID = '".$courseId."' LIMIT 1");
	$rows = $mydb->loadResultList();
	if (count($rows) < 1) {
		message("That student is not enrolled under your course.", "error");
		redirect('index.php?view=grades');
		return;
	}
	$enr = $rows[0];

	$subjectIds    = isset($_POST['GRADE_SUBJECT_ID']) && is_array($_POST['GRADE_SUBJECT_ID']) ? $_POST['GRADE_SUBJECT_ID'] : array();
	$gradeValues   = isset($_POST['GRADE_VALUE']) && is_array($_POST['GRADE_VALUE']) ? $_POST['GRADE_VALUE'] : array();
	$remarksValues = isset($_POST['GRADE_REMARKS']) && is_array($_POST['GRADE_REMARKS']) ? $_POST['GRADE_REMARKS'] : array();

	$savedCount = 0;

	foreach ($subjectIds as $sidRaw) {
		$subjectId = intval($sidRaw);
		if ($subjectId <= 0) { continue; }

		// Tiyakin na ang subject na ito ay talagang kabilang sa
		// enrollment na ito (hindi basta-basta subject id).
		$mydb->setQuery("SELECT DETAIL_ID FROM `tblenrollmentdetails`
			WHERE ENROLLMENT_ID = '".$eid."' AND SUBJECT_ID = '".$subjectId."' LIMIT 1");
		if ($mydb->num_rows() < 1) { continue; }

		$gradeVal   = isset($gradeValues[$subjectId]) ? trim($gradeValues[$subjectId]) : '';
		$remarksVal = isset($remarksValues[$subjectId]) ? trim($remarksValues[$subjectId]) : '';

		if ($gradeVal === '' && $remarksVal === '') { continue; }

		$gradeSql   = ($gradeVal === '') ? 'NULL' : "'".floatval($gradeVal)."'";
		$remarksSql = ($remarksVal === '') ? 'NULL' : "'".$mydb->escape_value($remarksVal)."'";

		$mydb->setQuery("SELECT GRADE_ID FROM `tblgrades`
			WHERE ENROLLMENT_ID = '".$eid."' AND SUBJECT_ID = '".$subjectId."' LIMIT 1");
		$existing = $mydb->loadResultList();

		if (count($existing) >= 1) {
			$mydb->InsertThis("UPDATE `tblgrades`
				SET GRADE = ".$gradeSql.", REMARKS = ".$remarksSql.", DATE_ENCODED = CURDATE()
				WHERE GRADE_ID = '".intval($existing[0]->GRADE_ID)."'");
		} else {
			$mydb->InsertThis("INSERT INTO `tblgrades`
				(S_ID, SUBJECT_ID, ENROLLMENT_ID, SY_ID, SEMESTER, GRADE, REMARKS, DATE_ENCODED)
				VALUES
				('".intval($enr->S_ID)."', '".$subjectId."', '".$eid."', '".intval($enr->SY_ID)."',
				 '".$mydb->escape_value($enr->SEMESTER)."', ".$gradeSql.", ".$remarksSql.", CURDATE())");
		}

		$savedCount++;
	}

	if ($savedCount > 0) {
		message($savedCount." grade(s) saved successfully.", "success");
	} else {
		message("No grades were entered.", "info");
	}
	redirect('index.php?view=grades');
}

/* ---------------------------------------------------------
   EDIT PHOTO - tblusers.PICTURE, WHERE UID = SESSION['UID']
   lang. Kaparehong upload folder ng module/user (images/) para
   sumunod sa parehong convention ng header.php.
   --------------------------------------------------------- */
function doEditPhoto() {
	global $mydb;

	$uid = intval($_SESSION['UID']);

	if (!isset($_FILES['PICTURE']) || $_FILES['PICTURE']['error'] === UPLOAD_ERR_NO_FILE) {
		message("Please choose a photo to upload.", "error");
		redirect('index.php?view=account');
		return;
	}
	if ($_FILES['PICTURE']['error'] !== UPLOAD_ERR_OK) {
		message("The photo upload failed. Please try again.", "error");
		redirect('index.php?view=account');
		return;
	}

	$tmpName   = $_FILES['PICTURE']['tmp_name'];
	$imageInfo = @getimagesize($tmpName);
	if ($imageInfo === false) {
		message("Uploaded file is not a valid image!", "error");
		redirect('index.php?view=account');
		return;
	}

	$allowedExt = array('jpg' => true, 'jpeg' => true, 'png' => true, 'gif' => true, 'webp' => true);
	$ext = strtolower(pathinfo($_FILES['PICTURE']['name'], PATHINFO_EXTENSION));
	if (!isset($allowedExt[$ext])) {
		message("Unsupported image type. Please use JPG, PNG, GIF, or WEBP.", "error");
		redirect('index.php?view=account');
		return;
	}

	$uploadDir = SITE_ROOT.DS.'module'.DS.'user'.DS.'images'.DS;
	if (!is_dir($uploadDir)) {
		@mkdir($uploadDir, 0755, true);
	}

	$fileName = 'user_'.date('YmdHis').'_'.mt_rand(1000, 9999).'.'.$ext;

	if (!move_uploaded_file($tmpName, $uploadDir.$fileName)) {
		message("The photo could not be saved. Please try again.", "error");
		redirect('index.php?view=account');
		return;
	}

	$stored = 'images/'.$fileName;

	$mydb->InsertThis("UPDATE `tblusers` SET `PICTURE` = '".$mydb->escape_value($stored)."' WHERE `UID` = '".$uid."'");
	$_SESSION['PICTURE'] = $stored;

	message("Profile photo updated!", "success");
	redirect('index.php?view=account');
}

/* ---------------------------------------------------------
   CHANGE PASSWORD - tblusers.PASSWORD, WHERE UID = SESSION
   lang. Kailangan itugma muna ang current password.
   --------------------------------------------------------- */
function doChangePassword() {
	global $mydb;

	$uid = intval($_SESSION['UID']);

	$currentPassword = isset($_POST['CURRENT_PASSWORD']) ? $_POST['CURRENT_PASSWORD'] : '';
	$newPassword     = isset($_POST['NEW_PASSWORD']) ? $_POST['NEW_PASSWORD'] : '';
	$confirmPassword = isset($_POST['CONFIRM_PASSWORD']) ? $_POST['CONFIRM_PASSWORD'] : '';

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

	$mydb->setQuery("SELECT UID FROM `tblusers` WHERE UID = '".$uid."' AND PASSWORD = '".sha1($currentPassword)."' LIMIT 1");
	if ($mydb->num_rows() < 1) {
		message("Your current password is incorrect.", "error");
		redirect('index.php?view=account');
		return;
	}

	$mydb->InsertThis("UPDATE `tblusers` SET PASSWORD = '".$mydb->escape_value(sha1($newPassword))."',
		DATEMODIFIED = '".date('Y-m-d')."', MODIFIEDBY = '".$uid."' WHERE UID = '".$uid."'");

	message("Password updated successfully!", "success");
	redirect('index.php?view=account');
}
?>
