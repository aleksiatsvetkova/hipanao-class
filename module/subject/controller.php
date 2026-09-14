<?php

require_once ("../../include/initialize.php");
if (!isset($_SESSION['ACCOUNT_ID'])){
    
}

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

function doInsert(){
	global $mydb;
	$subject = new Subject();
	$SUBJECT_CODE = trim($_POST['SUBJECT_CODE']);
	$SUBJECT_NAME = trim($_POST['SUBJECT_NAME']);
	$UNITS        = intval($_POST['UNITS']);
	$YEAR_LEVEL   = $_POST['YEAR_LEVEL'];
	$SEMESTER     = $_POST['SEMESTER'];
	$SUBJECT_TYPE = (isset($_POST['SUBJECT_TYPE']) && $_POST['SUBJECT_TYPE'] == 'Minor') ? 'Minor' : 'Major';

	/* HIPANAO SOLUTIONS - Major subjects belong to one specific course.
	   Minor subjects (GE, PE, NSTP, atbp.) ay bukas sa LAHAT ng course,
	   kaya COURSE_ID = NULL para dito, kahit ano pa ang napili sa
	   dropdown (na naka-hide na rin sa form pag Minor). */
	$COURSE_ID = ($SUBJECT_TYPE === 'Minor') ? 0 : intval($_POST['COURSE_ID']);

	if ($SUBJECT_TYPE !== 'Minor' && $COURSE_ID <= 0) {
		message("Please choose a course for a Major subject.", "error");
		redirect('index.php');
		return;
	}

	$res = $subject->find_all_subject($SUBJECT_CODE);

	if ($res >= 1) {
		message("Subject Code already exist!", "error");
		redirect('index.php');
	} else {
		/* Direktang raw SQL (hindi ang generic Subject::create()) para
		   sigurado talagang NULL ang COURSE_ID kapag Minor - ang
		   active-record sa include/subject.php ay nag-sski-skip ng
		   attribute pag PHP null ang value, kaya hindi ito magiging
		   maaasahan para sa "explicitly set to NULL" na kaso. */
		$courseSql = ($COURSE_ID > 0) ? "'".$COURSE_ID."'" : "NULL";
		$sql = "INSERT INTO `tblsubjects` (SUBJECT_CODE, SUBJECT_NAME, UNITS, COURSE_ID, YEAR_LEVEL, SEMESTER, SUBJECT_TYPE) VALUES (
				'".$mydb->escape_value($SUBJECT_CODE)."',
				'".$mydb->escape_value($SUBJECT_NAME)."',
				'".$UNITS."',
				".$courseSql.",
				'".$mydb->escape_value($YEAR_LEVEL)."',
				'".$mydb->escape_value($SEMESTER)."',
				'".$SUBJECT_TYPE."'
			)";

		if ($mydb->InsertThis($sql)) {
			message("New Subject [". $SUBJECT_CODE ."] has been created successfully!", "success");
			redirect('index.php');
		} else {
			message("No subject has been created successfully!", "error");
			redirect('index.php');
		}
	}
}

function doEdit(){
	global $mydb;
	if (isset($_POST['edit'])) {
		$SUBJECT_ID   = intval($_POST['SUBJECT_ID']);
		$SUBJECT_CODE = trim($_POST['SUBJECT_CODE1']);
		$SUBJECT_NAME = trim($_POST['SUBJECT_NAME1']);
		$UNITS        = intval($_POST['UNITS1']);
		$YEAR_LEVEL   = $_POST['YEAR_LEVEL1'];
		$SEMESTER     = $_POST['SEMESTER1'];
		$SUBJECT_TYPE = (isset($_POST['SUBJECT_TYPE1']) && $_POST['SUBJECT_TYPE1'] == 'Minor') ? 'Minor' : 'Major';

		/* Kagaya ng doInsert() - Minor = NULL palagi ang COURSE_ID,
		   Major = kailangan ng specific course. */
		$COURSE_ID = ($SUBJECT_TYPE === 'Minor') ? 0 : intval($_POST['COURSE_ID1']);

		if ($SUBJECT_TYPE !== 'Minor' && $COURSE_ID <= 0) {
			message("Please choose a course for a Major subject.", "error");
			redirect('index.php');
			return;
		}

		/* Raw SQL dito rin - tingnan ang paliwanag sa doInsert() kung
		   bakit hindi ginamit ang Subject::update() para dito: hindi
		   nito maaasahang ma-clear pabalik sa NULL ang COURSE_ID pag
		   nagpalit ng Major -> Minor ang isang subject. */
		$courseSql = ($COURSE_ID > 0) ? "'".$COURSE_ID."'" : "NULL";
		$sql = "UPDATE `tblsubjects` SET
				SUBJECT_CODE = '".$mydb->escape_value($SUBJECT_CODE)."',
				SUBJECT_NAME = '".$mydb->escape_value($SUBJECT_NAME)."',
				UNITS        = '".$UNITS."',
				COURSE_ID    = ".$courseSql.",
				YEAR_LEVEL   = '".$mydb->escape_value($YEAR_LEVEL)."',
				SEMESTER     = '".$mydb->escape_value($SEMESTER)."',
				SUBJECT_TYPE = '".$SUBJECT_TYPE."'
			WHERE SUBJECT_ID = '".$SUBJECT_ID."'";

		if ($mydb->InsertThis($sql)) {
			message("Subject [". $SUBJECT_CODE ."] has been Updated successfully!", "success");
			redirect('index.php');
		} else {
			message("No subject has been updated successfully!", "error");
			redirect('index.php');
		}
	}
}

function doDelete(){
	$id = $_GET['id'];
	$subject = new Subject();
	$subject->delete($id);

	message("Subject already Deleted!", "success");
	redirect('index.php');
}
?>
