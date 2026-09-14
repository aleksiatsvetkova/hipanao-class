<?php

$GENERIC_TABLES = array(
	'alumni_details' => array(
		'title' => 'Alumni Details',
		'icon'  => 'fa-id-card',
	),
	'tblschoolyear' => array(
		'title' => 'School Year',
		'icon'  => 'fa-calendar-alt',
	),
	'tblsections' => array(
		'title' => 'Sections',
		'icon'  => 'fa-chalkboard',
	),
	/* tblenrollmentdetails at tblgrades ay may sarili nang dedikadong
	   module (module/enrollmentdetails at module/grade) - mas malinaw
	   doon ang ID/Name/Course/Year/Status ng estudyante bilang hiwalay
	   na columns, at ang Grades ay may sariling "pumili ng estudyante,
	   lagyan ng grade bawat subject niya" na flow - kesa sa generic
	   Add/Edit ng isang record sa isang pagkakataon. */
);

$GENERIC_FK_MAP = array(
	'COURSE_ID'  => array('table' => 'tblcourses',   'pk' => 'COURSE_ID',  'label' => "CONCAT(COURSE_CODE, ' - ', COURSE_NAME)"),
	'SECTION_ID' => array('table' => 'tblsections',  'pk' => 'SECTION_ID', 'label' => 'SECTION_NAME'),
	'SY_ID'      => array('table' => 'tblschoolyear','pk' => 'SY_ID',      'label' => 'SCHOOL_YEAR'),
	'S_ID'       => array('table' => 'tblstudent',   'pk' => 'S_ID',       'label' => "CONCAT(LNAME, ', ', FNAME)"),
	'UID'        => array('table' => 'tblusers',     'pk' => 'UID',        'label' => 'USERNAME'),
	'TYPEID'     => array('table' => 'tblusertype',  'pk' => 'TYPEID',     'label' => 'USERTYPE'),
	'AddedBy'    => array('table' => 'tblusers',     'pk' => 'UID',        'label' => 'DISPLAYNAME'),
);

$GENERIC_STATUS_CHOICES = array(
	'tblenrollment' => array('Register', 'Assign', 'Paid', 'Enroll', 'Dropped', 'Completed'),
);

$GENERIC_FIELD_CHOICES = array(
	'YEAR_LEVEL' => array('1st Year', '2nd Year', '3rd Year', '4th Year'),
	'SEMESTER'   => array('1st Semester', '2nd Semester', 'Summer'),
	'STATUS'     => array('Active', 'Inactive'),
);

$GENERIC_LABEL_OVERRIDES = array(
	'ADVIS' => 'Program Head',
);

function generic_table_config($tableKey) {
	global $GENERIC_TABLES;
	return isset($GENERIC_TABLES[$tableKey]) ? $GENERIC_TABLES[$tableKey] : null;
}
?>
