<?php

/* HIPANAO SOLUTIONS - Program Head Module > ajax.php
   ==========================================================
   IMPORTANT: Ang COURSE_ID na ginagamit sa BAWAT query dito ay
   LAGING kinukuha via lookup gamit ang $_SESSION['UID']
   (tblcourses.PROGRAM_HEAD_ID) - HINDI kailanman mula sa
   POST/GET. Kaya't kahit anong ilagay sa request, laging sarili
   lang niyang course ang makikita/maeedit ng isang Program Head.
   ========================================================== */

require_once("../../include/initialize.php");
global $mydb;

if (!isset($_SESSION['UID']) || !isset($_SESSION['TYPE']) || $_SESSION['TYPE'] !== 'Program Head') {
	echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
	exit;
}

$phUid = intval($_SESSION['UID']);

$mydb->setQuery("SELECT COURSE_ID FROM `tblcourses` WHERE PROGRAM_HEAD_ID = '".$phUid."' LIMIT 1");
$courseRows = $mydb->loadResultList();
$phCourseId = (count($courseRows) >= 1) ? intval($courseRows[0]->COURSE_ID) : 0;

$act = isset($_POST['act']) ? $_POST['act'] : '';

/* =========================================================
   act=students - Dashboard > listahan ng estudyanteng naka-
   enroll sa sariling course (lahat ng status, para makita rin
   kung sino ang wala pang section).
   ========================================================= */
if ($act === 'students') {

	if ($phCourseId <= 0) {
		echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
		exit;
	}

	$base = "FROM `tblenrollment` e
		LEFT JOIN `tblstudent`    s  ON s.S_ID  = e.S_ID
		LEFT JOIN `tblschoolyear` sy ON sy.SY_ID = e.SY_ID
		LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID
		WHERE e.COURSE_ID = '".$phCourseId."' ";

	$where = "";
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$s = $mydb->escape_value($_POST["search"]["value"]);
		$where .= " AND (s.IDNO LIKE '%".$s."%'
					 OR s.LNAME LIKE '%".$s."%'
					 OR s.FNAME LIKE '%".$s."%'
					 OR e.YEAR_LEVEL LIKE '%".$s."%'
					 OR sy.SCHOOL_YEAR LIKE '%".$s."%'
					 OR e.SEMESTER LIKE '%".$s."%'
					 OR e.STATUS LIKE '%".$s."%') ";
	}

	$orderCols = array(1 => 's.IDNO', 2 => 's.LNAME', 3 => 'e.YEAR_LEVEL', 4 => 'sy.SCHOOL_YEAR', 6 => 'e.STATUS');
	$orderBy = " ORDER BY e.ENROLLMENT_ID DESC ";
	if (isset($_POST['order'][0]['column'])) {
		$ci = intval($_POST['order'][0]['column']);
		$dir = (isset($_POST['order'][0]['dir']) && strtolower($_POST['order'][0]['dir']) == 'asc') ? 'ASC' : 'DESC';
		if (isset($orderCols[$ci])) { $orderBy = " ORDER BY ".$orderCols[$ci]." ".$dir." "; }
	}

	$limit = "";
	if (isset($_POST['length']) && $_POST['length'] != -1) {
		$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
	}

	$select = "SELECT e.ENROLLMENT_ID, e.SY_ID, e.YEAR_LEVEL, e.SEMESTER, e.STATUS, e.SECTION_ID,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME,
			sy.SCHOOL_YEAR, sec.SECTION_NAME ";

	$mydb->setQuery($select.$base.$where.$orderBy.$limit);
	$rows = $mydb->loadResultList();
	$debugErr = $mydb->hasError() ? $mydb->getErrorMessage() : '';

	$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base.$where);
	$filtered = $mydb->num_rows();

	$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base);
	$total = $mydb->num_rows();

	$data = array();
	$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

	foreach ($rows as $r) {
		$fullname = trim(($r->LNAME ? $r->LNAME : '').', '.($r->FNAME ? $r->FNAME : '').' '.($r->MNAME ? $r->MNAME : ''));
		$term = $r->YEAR_LEVEL.' / '.$r->SEMESTER.' ('.($r->SCHOOL_YEAR ? $r->SCHOOL_YEAR : '-').')';

		$actions = '<button type="button" class="btn btn-info btn-xs doAssignSection"
			data-eid="'.$r->ENROLLMENT_ID.'"
			data-sy="'.$r->SY_ID.'"
			data-yearlevel="'.htmlspecialchars($r->YEAR_LEVEL, ENT_QUOTES).'"
			data-section="'.($r->SECTION_ID ? $r->SECTION_ID : '').'"
			data-name="'.htmlspecialchars($fullname, ENT_QUOTES).'"
			data-term="'.htmlspecialchars($term, ENT_QUOTES).'">
			<span class="fa fa-chalkboard fw-fa"></span> Assign Section
		</button>';

		$data[] = array(
			$i,
			htmlspecialchars($r->IDNO),
			htmlspecialchars($fullname),
			htmlspecialchars($r->YEAR_LEVEL),
			htmlspecialchars($r->SCHOOL_YEAR.' / '.$r->SEMESTER),
			$r->SECTION_NAME ? htmlspecialchars($r->SECTION_NAME) : '<span class="text-muted">Not sectioned</span>',
			hipanao_badge($r->STATUS),
			$actions
		);
		$i++;
	}

	echo json_encode(array('data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'debug' => $debugErr, 'phCourseId' => $phCourseId));
	exit;
}

/* =========================================================
   act=subjects_data - "Add Subject" modal sa Students list:
   parehong shape ng module/enrollmentdetails/ajax.php
   act=subjects_data, pero dinagdagan ng "AND e.COURSE_ID =
   $phCourseId" sa query ng enrollment - kaya hindi niya
   makukuha (at hindi rin niya kayang i-save, tingnan
   controller.php::doManageSubjects) ang datos ng isang
   estudyanteng wala sa sarili niyang course.
   ========================================================= */
if ($act === 'subjects_data') {

	$id = isset($_POST['ENROLLMENT_ID']) ? intval($_POST['ENROLLMENT_ID']) : 0;
	$output = array('subjects' => array(), 'checked' => array());

	if ($phCourseId > 0 && $id > 0) {
		$mydb->setQuery("SELECT e.*, s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO
			FROM `tblenrollment` e
			JOIN `tblstudent` s ON s.S_ID = e.S_ID
			WHERE e.ENROLLMENT_ID = '".$id."' AND e.COURSE_ID = '".$phCourseId."' LIMIT 1");
		$rows = $mydb->loadResultList();

		if (count($rows) >= 1) {
			$r = $rows[0];
			$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
			$output['IDNO']          = $r->IDNO;
			$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
			$output['TERM_TEXT']     = $r->YEAR_LEVEL.' / '.$r->SEMESTER;
			$output['STATUS']        = $r->STATUS;

			// Major subjects ng sarili niyang course + lahat ng Minor
			// subjects (COURSE_ID IS NULL), na-filter din sa YEAR_LEVEL
			// at SEMESTER ng enrollment na ito - kaparehong logic ng
			// module/enrollmentdetails/ajax.php act=subjects_data.
			$semesterFilter = "SEMESTER IS NULL OR SEMESTER = ''";
			if (strtolower(trim($r->SEMESTER)) == 'summer') {
				$semesterFilter .= " OR SEMESTER = '1st Semester' OR SEMESTER = '2nd Semester' OR SEMESTER = 'Summer'";
			} else {
				$semesterFilter .= " OR SEMESTER = '".$mydb->escape_value($r->SEMESTER)."'";
			}

			$mydb->setQuery("SELECT SUBJECT_ID, SUBJECT_CODE, SUBJECT_NAME, UNITS, YEAR_LEVEL, SEMESTER, SUBJECT_TYPE
				FROM `tblsubjects`
				WHERE (COURSE_ID = '".$phCourseId."' OR COURSE_ID IS NULL)
				AND (YEAR_LEVEL = '".$mydb->escape_value($r->YEAR_LEVEL)."' OR YEAR_LEVEL IS NULL OR YEAR_LEVEL = '')
				AND (".$semesterFilter.")
				ORDER BY SUBJECT_TYPE ASC, YEAR_LEVEL ASC, SEMESTER ASC, SUBJECT_CODE ASC");
			foreach ($mydb->loadResultList() as $sub) {
				$output['subjects'][] = array(
					'SUBJECT_ID'   => intval($sub->SUBJECT_ID),
					'SUBJECT_CODE' => $sub->SUBJECT_CODE,
					'SUBJECT_NAME' => $sub->SUBJECT_NAME,
					'UNITS'        => $sub->UNITS,
					'YEAR_LEVEL'   => $sub->YEAR_LEVEL,
					'SEMESTER'     => $sub->SEMESTER,
					'SUBJECT_TYPE' => isset($sub->SUBJECT_TYPE) ? $sub->SUBJECT_TYPE : 'Major'
				);
			}

			$mydb->setQuery("SELECT SUBJECT_ID FROM `tblenrollmentdetails` WHERE ENROLLMENT_ID = '".$id."'");
			foreach ($mydb->loadResultList() as $d) {
				$output['checked'][] = intval($d->SUBJECT_ID);
			}
		}
	}

	echo json_encode($output);
	exit;
}

/* =========================================================
   act=sections_for_select - Assign Section modal dropdown -
   sections ng SARILI niyang course LANG, na-filter din sa SY_ID
   (at kung meron, YEAR_LEVEL) ng partikular na enrollment na
   ie-edit, kagaya ng ginagamit sa Enrollment/Enrollment Details.
   ========================================================= */
if ($act === 'sections_for_select') {

	$sy = intval($_POST['SY_ID']);
	$yearLevel = isset($_POST['YEAR_LEVEL']) ? trim($_POST['YEAR_LEVEL']) : '';
	$rows = array();

	if ($phCourseId > 0 && $sy > 0) {
		$sql = "SELECT SECTION_ID, SECTION_NAME, YEAR_LEVEL
			FROM `tblsections`
			WHERE COURSE_ID = '".$phCourseId."' AND SY_ID = '".$sy."' ";
		if ($yearLevel != '') {
			$sql .= "AND YEAR_LEVEL = '".$mydb->escape_value($yearLevel)."' ";
		}
		$sql .= "ORDER BY YEAR_LEVEL ASC, SECTION_NAME ASC";

		$mydb->setQuery($sql);
		foreach ($mydb->loadResultList() as $row) {
			$rows[] = array('SECTION_ID' => $row->SECTION_ID, 'SECTION_NAME' => $row->SECTION_NAME, 'YEAR_LEVEL' => $row->YEAR_LEVEL);
		}
	}

	echo json_encode($rows);
	exit;
}

/* =========================================================
   act=sections_list - Sections page listahan (sarili niyang
   course lang), kasama ang bilang ng estudyanteng naka-assign
   doon.
   ========================================================= */
if ($act === 'sections_list') {

	if ($phCourseId <= 0) {
		echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
		exit;
	}

	$base = "FROM `tblsections` sec
		LEFT JOIN `tblschoolyear` sy ON sy.SY_ID = sec.SY_ID
		WHERE sec.COURSE_ID = '".$phCourseId."' ";

	$where = "";
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$s = $mydb->escape_value($_POST["search"]["value"]);
		$where .= " AND (sec.SECTION_NAME LIKE '%".$s."%' OR sec.YEAR_LEVEL LIKE '%".$s."%' OR sy.SCHOOL_YEAR LIKE '%".$s."%') ";
	}

	$orderBy = " ORDER BY sec.YEAR_LEVEL ASC, sec.SECTION_NAME ASC ";

	$limit = "";
	if (isset($_POST['length']) && $_POST['length'] != -1) {
		$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
	}

	$select = "SELECT sec.SECTION_ID, sec.SECTION_NAME, sec.YEAR_LEVEL, sy.SCHOOL_YEAR ";

	$mydb->setQuery($select.$base.$where.$orderBy.$limit);
	$rows = $mydb->loadResultList();

	$mydb->setQuery("SELECT sec.SECTION_ID ".$base.$where);
	$filtered = $mydb->num_rows();

	$mydb->setQuery("SELECT sec.SECTION_ID ".$base);
	$total = $mydb->num_rows();

	$data = array();
	$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

	foreach ($rows as $r) {
		$mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblenrollment` WHERE SECTION_ID = '".intval($r->SECTION_ID)."'");
		$cntRows = $mydb->loadResultList();
		$cnt = (count($cntRows) >= 1) ? intval($cntRows[0]->CNT) : 0;

		$data[] = array(
			$i,
			htmlspecialchars($r->SECTION_NAME),
			htmlspecialchars($r->YEAR_LEVEL),
			htmlspecialchars($r->SCHOOL_YEAR),
			$cnt
		);
		$i++;
	}

	echo json_encode(array('data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered));
	exit;
}

/* =========================================================
   act=subjects_list - Subjects tab listahan: LAHAT ng Major
   subject ng SARILI niyang course (COURSE_ID = $phCourseId) +
   ang mga Minor subject (COURSE_ID IS NULL, "All Courses" -
   ginawa ng Admin, reference lang, walang Edit/Delete button).
   ========================================================= */
if ($act === 'subjects_list') {

	if ($phCourseId <= 0) {
		echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
		exit;
	}

	$base = "FROM `tblsubjects` s
		WHERE (s.COURSE_ID = '".$phCourseId."' OR s.COURSE_ID IS NULL) ";

	$where = "";
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$sv = $mydb->escape_value($_POST["search"]["value"]);
		$where .= " AND (s.SUBJECT_CODE LIKE '%".$sv."%' OR s.SUBJECT_NAME LIKE '%".$sv."%'
			OR s.YEAR_LEVEL LIKE '%".$sv."%' OR s.SEMESTER LIKE '%".$sv."%') ";
	}

	$orderBy = " ORDER BY s.YEAR_LEVEL ASC, s.SEMESTER ASC, s.SUBJECT_CODE ASC ";

	$limit = "";
	if (isset($_POST['length']) && $_POST['length'] != -1) {
		$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
	}

	$select = "SELECT s.SUBJECT_ID, s.SUBJECT_CODE, s.SUBJECT_NAME, s.UNITS, s.YEAR_LEVEL, s.SEMESTER, s.SUBJECT_TYPE, s.COURSE_ID ";

	$mydb->setQuery($select.$base.$where.$orderBy.$limit);
	$rows = $mydb->loadResultList();

	$mydb->setQuery("SELECT s.SUBJECT_ID ".$base.$where);
	$filtered = $mydb->num_rows();

	$mydb->setQuery("SELECT s.SUBJECT_ID ".$base);
	$total = $mydb->num_rows();

	$data = array();
	$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

	foreach ($rows as $r) {
		// Sariling Major subject lang niya (COURSE_ID = $phCourseId) ang
		// puwedeng i-edit/i-delete - ang Minor (COURSE_ID NULL, gawa ng
		// Admin) ay reference lang, walang action button.
		$isOwn = ($r->COURSE_ID !== null && intval($r->COURSE_ID) === $phCourseId);
		$type  = isset($r->SUBJECT_TYPE) ? $r->SUBJECT_TYPE : 'Major';

		$actions = '<div class="text-nowrap">';
		if ($isOwn) {
			$actions .= '<button type="button" data-id="'.$r->SUBJECT_ID.'" class="btn btn-outline-warning btn-sm action-btn phEditSubjectBtn" title="Edit"><i class="fa fa-pen"></i></button>';
			$actions .= '<button type="button" data-id="'.$r->SUBJECT_ID.'" class="btn btn-outline-danger btn-sm action-btn phDeleteSubjectBtn" title="Delete"><i class="fa fa-trash"></i></button>';
		} else {
			$actions .= '<span class="text-muted small">-</span>';
		}
		$actions .= '</div>';

		$data[] = array(
			$i,
			htmlspecialchars($r->SUBJECT_CODE),
			htmlspecialchars($r->SUBJECT_NAME),
			intval($r->UNITS),
			htmlspecialchars($r->YEAR_LEVEL),
			htmlspecialchars($r->SEMESTER),
			$isOwn
				? '<span class="badge badge-subject-major">'.$type.'</span>'
				: '<span class="badge badge-subject-minor">Minor</span> <span class="badge badge-secondary">All Courses</span>',
			$actions
		);
		$i++;
	}

	echo json_encode(array('data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered));
	exit;
}

/* =========================================================
   act=subject_get - Edit modal ng Subjects tab: iisang subject
   record lang, at DAPAT sariling course niya (COURSE_ID =
   $phCourseId) - hindi niya makukuha ang datos ng Minor subject
   o subject ng ibang course kahit anong SUBJECT_ID ang ipasa.
   ========================================================= */
if ($act === 'subject_get') {

	$subjectId = isset($_POST['SUBJECT_ID']) ? intval($_POST['SUBJECT_ID']) : 0;
	$output = array();

	if ($phCourseId > 0 && $subjectId > 0) {
		$mydb->setQuery("SELECT * FROM `tblsubjects`
			WHERE SUBJECT_ID = '".$subjectId."' AND COURSE_ID = '".$phCourseId."' LIMIT 1");
		$rows = $mydb->loadResultList();
		if (count($rows) >= 1) {
			$r = $rows[0];
			$output['SUBJECT_ID']   = $r->SUBJECT_ID;
			$output['SUBJECT_CODE'] = $r->SUBJECT_CODE;
			$output['SUBJECT_NAME'] = $r->SUBJECT_NAME;
			$output['UNITS']        = $r->UNITS;
			$output['YEAR_LEVEL']   = $r->YEAR_LEVEL;
			$output['SEMESTER']     = $r->SEMESTER;
		}
	}

	echo json_encode($output);
	exit;
}

/* =========================================================
   act=grade_list - Grades page listahan: mga kasalukuyang
   naka-"Enroll" na estudyante ng SARILI niyang course na may
   subject nang kinuha.
   ========================================================= */
if ($act === 'grade_list') {

	if ($phCourseId <= 0) {
		echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
		exit;
	}

	$base = "FROM `tblenrollment` e
		LEFT JOIN `tblstudent`    s  ON s.S_ID  = e.S_ID
		LEFT JOIN `tblschoolyear` sy ON sy.SY_ID = e.SY_ID
		WHERE e.COURSE_ID = '".$phCourseId."'
		  AND e.STATUS = 'Enroll'
		  AND EXISTS (SELECT 1 FROM `tblenrollmentdetails` ed WHERE ed.ENROLLMENT_ID = e.ENROLLMENT_ID) ";

	$where = "";
	if (isset($_POST["search"]["value"]) && $_POST["search"]["value"] != '') {
		$s = $mydb->escape_value($_POST["search"]["value"]);
		$where .= " AND (s.IDNO LIKE '%".$s."%' OR s.LNAME LIKE '%".$s."%' OR s.FNAME LIKE '%".$s."%'
					 OR e.YEAR_LEVEL LIKE '%".$s."%' OR sy.SCHOOL_YEAR LIKE '%".$s."%') ";
	}

	$orderBy = " ORDER BY e.ENROLLMENT_ID DESC ";

	$limit = "";
	if (isset($_POST['length']) && $_POST['length'] != -1) {
		$limit = " LIMIT ".intval($_POST['start']).", ".intval($_POST['length'])." ";
	}

	$select = "SELECT e.ENROLLMENT_ID, s.IDNO, s.LNAME, s.FNAME, s.MNAME, e.YEAR_LEVEL, sy.SCHOOL_YEAR, e.SEMESTER ";

	$mydb->setQuery($select.$base.$where.$orderBy.$limit);
	$rows = $mydb->loadResultList();

	$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base.$where);
	$filtered = $mydb->num_rows();

	$mydb->setQuery("SELECT e.ENROLLMENT_ID ".$base);
	$total = $mydb->num_rows();

	$data = array();
	$i = isset($_POST['start']) ? intval($_POST['start']) + 1 : 1;

	foreach ($rows as $r) {
		$actions = '<button type="button" class="btn btn-info btn-xs phDoGrade" data-eid="'.$r->ENROLLMENT_ID.'">
			<span class="fa fa-graduation-cap fw-fa"></span> Grade
		</button>';

		$data[] = array(
			$i,
			htmlspecialchars($r->IDNO),
			htmlspecialchars(trim(($r->LNAME ? $r->LNAME : '').', '.($r->FNAME ? $r->FNAME : '').' '.($r->MNAME ? $r->MNAME : ''))),
			htmlspecialchars($r->YEAR_LEVEL),
			htmlspecialchars(($r->SCHOOL_YEAR ? $r->SCHOOL_YEAR : '-').' / '.$r->SEMESTER),
			$actions
		);
		$i++;
	}

	echo json_encode(array('data' => $data, 'recordsTotal' => $total, 'recordsFiltered' => $filtered));
	exit;
}

/* =========================================================
   act=grade_subjects - Encode Grades modal data. Ownership
   check: kailangang tumugma ang COURSE_ID ng enrollment sa
   SARILI niyang $phCourseId, kung hindi ay walang ibabalik.
   ========================================================= */
if ($act === 'grade_subjects') {

	$id = intval($_POST['ENROLLMENT_ID']);
	$output = array('subjects' => array());

	if ($phCourseId <= 0) { echo json_encode($output); exit; }

	$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.SY_ID, e.SEMESTER,
			s.IDNO, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO,
			sy.SCHOOL_YEAR
		FROM `tblenrollment` e
		JOIN `tblstudent` s ON s.S_ID = e.S_ID
		JOIN `tblschoolyear` sy ON sy.SY_ID = e.SY_ID
		WHERE e.ENROLLMENT_ID = '".$id."' AND e.COURSE_ID = '".$phCourseId."' LIMIT 1");
	$rows = $mydb->loadResultList();

	if (count($rows) >= 1) {
		$r = $rows[0];
		$output['ENROLLMENT_ID'] = $r->ENROLLMENT_ID;
		$output['IDNO']          = $r->IDNO;
		$output['FULLNAME']      = trim($r->LNAME.', '.$r->FNAME.' '.$r->MNAME);
		$output['TERM_TEXT']     = $r->SCHOOL_YEAR.' / '.$r->SEMESTER;
		$output['PICTURE_URL']   = (isset($r->COMPANYIDNO) && $r->COMPANYIDNO != '')
			? WEB_ROOT.'module/student/image/'.$r->COMPANYIDNO
			: '';

		$mydb->setQuery("SELECT sub.SUBJECT_ID, sub.SUBJECT_CODE, sub.SUBJECT_NAME, g.GRADE, g.REMARKS
			FROM `tblenrollmentdetails` d
			JOIN `tblsubjects` sub ON sub.SUBJECT_ID = d.SUBJECT_ID
			LEFT JOIN `tblgrades` g ON g.ENROLLMENT_ID = d.ENROLLMENT_ID AND g.SUBJECT_ID = d.SUBJECT_ID
			WHERE d.ENROLLMENT_ID = '".$id."'
			ORDER BY sub.SUBJECT_CODE ASC");
		foreach ($mydb->loadResultList() as $sub) {
			$output['subjects'][] = array(
				'SUBJECT_ID'   => $sub->SUBJECT_ID,
				'SUBJECT_CODE' => $sub->SUBJECT_CODE,
				'SUBJECT_NAME' => $sub->SUBJECT_NAME,
				'GRADE'        => ($sub->GRADE === null) ? null : floatval($sub->GRADE),
				'REMARKS'      => $sub->REMARKS
			);
		}
	}

	echo json_encode($output);
	exit;
}

echo json_encode(array('data' => array(), 'recordsTotal' => 0, 'recordsFiltered' => 0));
?>
