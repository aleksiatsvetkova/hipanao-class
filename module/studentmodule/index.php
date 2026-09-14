<?php

/* HIPANAO SOLUTIONS - Student Module.
   ==========================================================
   Ito na ang BAGONG "sariling module" ng isang Student account -
   dating nasa theme/studentdashboard/ (may sarili pang navbar/layout,
   hiwalay sa AdminLTE). Ngayon, dito na, kaparehong-kapareho ng disenyo
   at layout ng Admin (theme/template.php - full AdminLTE: navbar,
   sidebar, content-wrapper, footer), pero ang laman na makikita ng
   Student ay SARILI NIYA LANG na impormasyon:

     - profile.php  -> "My Profile"        (personal/contact/family info)
     - section.php  -> "My Section"        (kasalukuyang enrollment/section/subjects)
     - grades.php   -> "My Grades"         (kasalukuyang term + grade history)
     - history.php  -> "Enrollment History"(mga nakaraang enrollment term)
     - payment.php  -> "My Payments"       (payment history)

   Bawat isa sa itaas ay may sariling file dito mismo sa
   module/studentmodule/ (kagaya ng ibang module - student, grade,
   payment, atbp.) - ang controller na ito (index.php) ang nagde-decide
   kung alin ang ipapakita (?view=...) tapos ipinapasa sa
   theme/template.php gamit ang parehong $title / $content pattern na
   ginagamit ng LAHAT ng ibang module.
   ========================================================== */

require_once("../../include/initialize.php");

if (!isset($_SESSION['UID'])) {
    redirect(WEB_ROOT."login.php");
    exit;
}

// Isang Admin/Staff account na direktang pumunta dito - ibalik na lang
// sa sarili nilang dashboard, kagaya ng ginagawa ng ibang module kapag
// Student ang sumubok pumasok doon (theme/template.php).
if (!isset($_SESSION['TYPE']) || $_SESSION['TYPE'] !== 'Student') {
    redirect(WEB_ROOT."index.php");
    exit;
}

global $mydb;

$sid = isset($_SESSION['STUDENT_SID']) ? intval($_SESSION['STUDENT_SID']) : 0;

/* =========================================================
   STEP 1: Sariling record ng Student (tblstudent + Course)
   ========================================================= */
$studentRow = null;
if ($sid > 0) {
    $mydb->setQuery("SELECT s.*, c.COURSE_NAME, c.COURSE_CODE
        FROM `tblstudent` s
        LEFT JOIN `tblcourses` c ON c.COURSE_ID = s.COURSE_ID
        WHERE s.S_ID = '".$sid."' LIMIT 1");
    $rows = $mydb->loadResultList();
    if (count($rows) >= 1) { $studentRow = $rows[0]; }
}

$studentPhotoUrl = (isset($studentRow->COMPANYIDNO) && $studentRow->COMPANYIDNO != '')
    ? WEB_ROOT.'module/student/image/'.$studentRow->COMPANYIDNO
    : WEB_ROOT.'module/student/image/1.png';

/* =========================================================
   STEP 2: Pinaka-huling/kasalukuyang Enrollment record niya
   (kasama Course/School Year/Section) - ito ang gamit ng
   section.php at grades.php (current term).
   ========================================================= */
$currentEnrollment = null;
if ($sid > 0) {
    // HIPANAO SOLUTIONS - Ang Program Head na ipapakita sa "My Section"
    // ay yung naka-assign sa COURSE ng estudyante (tblcourses.PROGRAM_HEAD_ID,
    // module/course), hindi na yung dati niyang manual na PROGRAM_HEAD text
    // sa tblsections. Kaya LEFT JOIN dito papunta sa tblusers gamit ang
    // c.PROGRAM_HEAD_ID para makuha ang pangalan (DISPLAYNAME) niya.
    $mydb->setQuery("SELECT e.*, c.COURSE_NAME, c.COURSE_CODE,
            sy.SCHOOL_YEAR, sec.SECTION_NAME, ph.DISPLAYNAME AS PROGRAM_HEAD
        FROM `tblenrollment` e
        JOIN `tblcourses`    c   ON c.COURSE_ID = e.COURSE_ID
        JOIN `tblschoolyear` sy  ON sy.SY_ID    = e.SY_ID
        LEFT JOIN `tblsections` sec ON sec.SECTION_ID = e.SECTION_ID
        LEFT JOIN `tblusers` ph ON ph.UID = c.PROGRAM_HEAD_ID
        WHERE e.S_ID = '".$sid."'
        ORDER BY e.ENROLLMENT_ID DESC LIMIT 1");
    $rows = $mydb->loadResultList();
    if (count($rows) >= 1) { $currentEnrollment = $rows[0]; }
}

/* =========================================================
   STEP 3: Alin bang view ang ipapakita?
   ========================================================= */
$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : 'profile';

$smTitles = array(
    'profile' => 'My Profile',
    'section' => 'My Section',
    'grades'  => 'My Grades',
    'history' => 'Enrollment History',
    'payment' => 'My Payments',
    'account' => 'My Account',
);

switch ($view) {
    case 'section':
        $title   = $smTitles['section'];
        $content = 'section.php';
        break;
    case 'grades':
        $title   = $smTitles['grades'];
        $content = 'grades.php';
        break;
    case 'history':
        $title   = $smTitles['history'];
        $content = 'history.php';
        break;
    case 'payment':
        $title   = $smTitles['payment'];
        $content = 'payment.php';
        break;
    case 'account':
        $title   = $smTitles['account'];
        $content = 'account.php';
        break;
    default:
        $title   = $smTitles['profile'];
        $content = 'profile.php';
}

/* Sinasabihan si theme/template.php na okay lang papasukin ang
   Student account dito - kaparehong flag na ginagamit dati ng
   theme/studentdashboard/. */
$GLOBALS['HIPANAO_ALLOW_STUDENT'] = true;

require_once("../../theme/template.php");
?>
<script type="text/javascript">
  // Photo preview - ipinapakita agad ang napiling litrato bago pa i-submit
  // (My Profile > Change Photo) - HIPANAO SOLUTIONS.
  $(document).ready( function() {
    $(document).on('change', '.student-photo-input', function() {
        var input = this;
        var $input = $(this);
        var fileName = $input.val() ? $input.val().replace(/\\/g, '/').replace(/.*\//, '') : '';

        $input.next('.custom-file-label').text(fileName ? fileName : 'Choose photo...');

        var preview = $input.data('preview');
        if (preview && input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $(preview).attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    });
  });
</script>
<script type="text/javascript">
  // Reset Change Photo modal - laging blangko ang laman pag bubukas
  $('#sdEditPhoto').on('show.bs.modal', function () {
      $('#sdPicture').val('');
      $('#sdPicture').next('.custom-file-label').text('Choose photo...');
      $('#sdImgUploadPreview').attr('src', $('#sdProfilePhoto').attr('src'));
  });
</script>
