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
	$course = new Course();
	$COURSE_CODE = trim($_POST['COURSE_CODE']);
	$COURSE_NAME = trim($_POST['COURSE_NAME']);
	$COURSE_DESC = isset($_POST['COURSE_DESC']) ? $_POST['COURSE_DESC'] : '';
	$STATUS      = isset($_POST['STATUS']) ? $_POST['STATUS'] : 'Active';
	// HIPANAO SOLUTIONS - Program Head ng course, kinuha mula sa
	// tblusers (yung mga account na Program Head ang usertype).
	// Pwedeng iwanang blangko kaya nagiging NULL kapag walang pinili.
	$PROGRAM_HEAD_ID = (isset($_POST['PROGRAM_HEAD_ID']) && $_POST['PROGRAM_HEAD_ID'] != '') ? intval($_POST['PROGRAM_HEAD_ID']) : null;

	$res = $course->find_all_course($COURSE_CODE);

	if ($res >= 1) {
		message("Course Code already exist!", "error");
		redirect('index.php');
	} else {
		$course->COURSE_CODE = $COURSE_CODE;
		$course->COURSE_NAME = $COURSE_NAME;
		$course->COURSE_DESC = $COURSE_DESC;
		$course->STATUS      = $STATUS;
		$course->PROGRAM_HEAD_ID = $PROGRAM_HEAD_ID;

		$istrue = $course->create();
		if ($istrue == true){
			message("New Course [". $COURSE_CODE ."] has been created successfully!", "success");
			redirect('index.php');
		} else {
			message("No course has been created successfully!", "error");
			redirect('index.php');
		}
	}
}

function doEdit(){
	if (isset($_POST['edit'])) {
		global $mydb;
		$course = new Course();
		$COURSE_ID   = $_POST['COURSE_ID'];
		$COURSE_CODE = trim($_POST['COURSE_CODE1']);
		$COURSE_NAME = trim($_POST['COURSE_NAME1']);
		$COURSE_DESC = isset($_POST['COURSE_DESC1']) ? $_POST['COURSE_DESC1'] : '';
		$STATUS      = isset($_POST['STATUS1']) ? $_POST['STATUS1'] : 'Active';
		// HIPANAO SOLUTIONS - Program Head ng course.
		$PROGRAM_HEAD_ID = (isset($_POST['PROGRAM_HEAD_ID1']) && $_POST['PROGRAM_HEAD_ID1'] != '') ? intval($_POST['PROGRAM_HEAD_ID1']) : null;

		$course->COURSE_CODE = $COURSE_CODE;
		$course->COURSE_NAME = $COURSE_NAME;
		$course->COURSE_DESC = $COURSE_DESC;
		$course->STATUS      = $STATUS;

		$istrue = $course->update($COURSE_ID);

		// I-sync ang PROGRAM_HEAD_ID sa hiwalay na query dahil ang
		// generic na update() sa itaas ay nilalaktawan ang mga NULL
		// na value - kaya kapag binalik sa blangko ang Program Head
		// sa Edit form, kailangang matanggal talaga ito (NULL) sa DB.
		$programHeadValue = ($PROGRAM_HEAD_ID === null) ? "NULL" : intval($PROGRAM_HEAD_ID);
		$mydb->InsertThis("UPDATE tblcourses SET PROGRAM_HEAD_ID = ".$programHeadValue." WHERE COURSE_ID = ".intval($COURSE_ID));

		if ($istrue == true){
			message("Course [". $COURSE_CODE ."] has been Updated successfully!", "success");
			redirect('index.php');
		} else {
			message("No course has been updated successfully!", "error");
			redirect('index.php');
		}
	}
}

function doDelete(){
	$id = $_GET['id'];
	$course = new Course();
	$course->delete($id);

	message("Course already Deleted!", "success");
	redirect('index.php');
}
?>
