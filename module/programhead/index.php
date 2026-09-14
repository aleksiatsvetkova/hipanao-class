<?php

/* HIPANAO SOLUTIONS - Program Head Module.
   ==========================================================
   Sariling module ito ng isang "Program Head" account - kaparehong-
   kapareho ng disenyo/layout ng Admin (theme/template.php - full
   AdminLTE: navbar, sidebar, content-wrapper, footer), pero ang
   makikita/magagawa ng Program Head ay LIMITADO LANG sa SARILI
   niyang COURSE (tblcourses.PROGRAM_HEAD_ID = kanyang UID):

     - dashboard.php -> Course summary + listahan ng estudyanteng
                         naka-enroll sa kanyang course. Dito rin
                         niya ma-a-"Assign Section" ang isang estudyante.
     - sections.php  -> Listahan ng Sections ng kanyang course +
                         "Add New Section".
     - grades.php    -> Listahan ng estudyante (kanyang course lang)
                         na may subject na kinuha - "Grade" button
                         para lagyan ng grade bawat subject.
     - account.php   -> "My Account" - sariling profile photo at
                         password (tblusers - ito mismo ang account
                         niya, hindi katulad ng Student na may hiwalay
                         na tblstudent record).

   Bawat isa sa itaas ay may sariling file dito mismo (kagaya ng
   module/studentmodule/) - ang controller na ito (index.php) ang
   nagde-decide kung alin ang ipapakita (?view=...) tapos ipinapasa
   sa theme/template.php gamit ang parehong $title / $content pattern
   na ginagamit ng LAHAT ng ibang module.

   IMPORTANT: Ang COURSE_ID ng Program Head ay HINDI kailanman
   kinukuha mula sa POST/GET - laging galing ito sa isang query dito
   sa index.php gamit ang SESSION['UID'] (tblcourses.PROGRAM_HEAD_ID).
   Kaya't kahit anong gawin sa request, hindi makikita/maeedit ng
   isang Program Head ang datos ng course na hindi sa kanya.
   ========================================================== */

require_once("../../include/initialize.php");

if (!isset($_SESSION['UID'])) {
    redirect(WEB_ROOT."login.php");
    exit;
}

// Isang Admin/Staff/Student account na direktang pumunta dito - ibalik
// na lang sa sarili nilang dashboard.
if (!isset($_SESSION['TYPE']) || $_SESSION['TYPE'] !== 'Program Head') {
    redirect(WEB_ROOT."index.php");
    exit;
}

global $mydb;

$phUid = intval($_SESSION['UID']);

/* =========================================================
   STEP 1: Anong Course ba siya Program Head?
   ========================================================= */
$phCourse = null;
$mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME, COURSE_DESC, STATUS
    FROM `tblcourses` WHERE PROGRAM_HEAD_ID = '".$phUid."' LIMIT 1");
$rows = $mydb->loadResultList();
if (count($rows) >= 1) { $phCourse = $rows[0]; }

$phCourseId = $phCourse ? intval($phCourse->COURSE_ID) : 0;

$phPhotoUrl = (isset($_SESSION['PICTURE']) && $_SESSION['PICTURE'] != '')
    ? WEB_ROOT.'module/user/'.$_SESSION['PICTURE']
    : WEB_ROOT.'module/user/images/default.png';

/* =========================================================
   STEP 2: Alin bang view ang ipapakita?
   ========================================================= */
$view = (isset($_GET['view']) && $_GET['view'] != '') ? $_GET['view'] : 'dashboard';

$phTitles = array(
    'dashboard' => 'Program Head Dashboard',
    'sections'  => 'Sections',
    'subjects'  => 'Subjects',
    'grades'    => 'Grades',
    'account'   => 'My Account',
);

switch ($view) {
    case 'sections':
        $title   = $phTitles['sections'];
        $content = 'sections.php';
        break;
    case 'subjects':
        $title   = $phTitles['subjects'];
        $content = 'subjects.php';
        break;
    case 'grades':
        $title   = $phTitles['grades'];
        $content = 'grades.php';
        break;
    case 'account':
        $title   = $phTitles['account'];
        $content = 'account.php';
        break;
    default:
        $title   = $phTitles['dashboard'];
        $content = 'dashboard.php';
}

/* Sinasabihan si theme/template.php na okay lang papasukin ang
   Program Head dito - kaparehong flag na ginagamit ng Student
   module (HIPANAO_ALLOW_STUDENT). */
$GLOBALS['HIPANAO_ALLOW_PROGRAMHEAD'] = true;

require_once("../../theme/template.php");
?>

<?php /* =========================================================
   HIPANAO SOLUTIONS - Bakit dito nakalagay ang mga DataTable init
   script (hindi sa loob ng dashboard.php/sections.php/grades.php):

   Ang mga content file (dashboard.php, sections.php, grades.php) ay
   naka-require_once SA LOOB ng theme/template.php (sa gitna ng
   pahina), BAGO pa ma-load ang jQuery/DataTables (naka-footer pa
   lang ang mga iyon sa template.php). Kaya kung ilalagay doon ang
   "$(document).ready(...).DataTable(...)", hindi pa umiiral ang "$"
   sa oras na iyon - JS error agad, kaya hindi na-i-initialize ang
   DataTables (walang search box, walang pagination, laging blangko
   ang table kahit tama at kumpleto ang datos mula sa ajax.php).

   Ito rin mismo ang pattern na ginagamit ng Admin modules (hal.
   module/course/index.php, module/student/index.php) - inilalagay
   ang DataTable init dito sa index.php, PAGKATAPOS ng require_once
   sa itaas, para siguradong naka-load na ang jQuery/DataTables bago
   pa tumakbo ang script na ito.
   ========================================================= */ ?>

<?php if ($view === 'sections'): ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#tblphsections').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/programhead/ajax.php",
            type: "POST",
            data: function (d) { d.act = 'sections_list'; }
        }
    });
});
</script>
<?php elseif ($view === 'subjects'): ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#tblphsubjects').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/programhead/ajax.php",
            type: "POST",
            data: function (d) { d.act = 'subjects_list'; }
        },
        "columnDefs": [
            { "orderable": false, "targets": [7] }
        ]
    });
});

$(document).on('click', '.phEditSubjectBtn', function(){
    var SUBJECT_ID = $(this).attr('data-id');
    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/programhead/ajax.php",
        method: "POST",
        data: { act: 'subject_get', SUBJECT_ID: SUBJECT_ID },
        dataType: "json",
        success: function(data) {
            try {
                if (!data.SUBJECT_ID) {
                    alert('DEBUG - That subject could not be loaded. Raw response: ' + JSON.stringify(data));
                    return;
                }
                $('#PH_ESUBJ_ID').val(data.SUBJECT_ID);
                $('#PH_ESUBJ_CODE').val(data.SUBJECT_CODE);
                $('#PH_ESUBJ_NAME').val(data.SUBJECT_NAME);
                $('#PH_ESUBJ_UNITS').val(data.UNITS);
                $('#PH_ESUBJ_YL').val(data.YEAR_LEVEL);
                $('#PH_ESUBJ_SEM').val(data.SEMESTER);
                $('#phEditSubject').modal('show');
            } catch (err) {
                alert('DEBUG - JS error sa Edit Subject: ' + err.message);
            }
        },
        error: function(xhr) {
            alert('DEBUG - Ajax failed (Edit Subject). Status: ' + xhr.status + ' | Response: ' + (xhr.responseText ? xhr.responseText.substring(0, 400) : '(walang response)'));
        }
    });
});

$(document).on('click', '.phDeleteSubjectBtn', function(){
    var SUBJECT_ID = $(this).attr('data-id');
    Swal.fire({
        title: 'Delete Subject?',
        text: "This action cannot be undone. Are you sure you want to delete this subject?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            window.location.href = "<?php echo WEB_ROOT; ?>module/programhead/controller.php?action=deletesubject&id=" + SUBJECT_ID;
        }
    });
});
</script>
<?php elseif ($view === 'grades'): ?>
<script type="text/javascript">
$(document).ready(function() {
    $('#tblphgradestudents').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/programhead/ajax.php",
            type: "POST",
            data: function (d) { d.act = 'grade_list'; }
        },
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ]
    });
});

$(document).on('click', '.phDoGrade', function() {
    var eid = $(this).attr('data-eid');

    $('#PH_GRADE_SUBJECTS_TBODY').html('<tr><td colspan="3" class="text-muted text-center">Loading...</td></tr>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/programhead/ajax.php",
        method: "POST",
        data: { act: 'grade_subjects', ENROLLMENT_ID: eid },
        dataType: "json",
        success: function(d) {
            try {
                if (!d.ENROLLMENT_ID) {
                    alert('DEBUG - Could not load that enrollment record. Raw response: ' + JSON.stringify(d));
                    return;
                }
                $('#PH_GRADE_EID').val(d.ENROLLMENT_ID);
                $('#PH_GRADE_IDNO_TEXT').text(d.IDNO || '-');
                $('#PH_GRADE_NAME_TEXT').text(d.FULLNAME || '-');
                $('#PH_GRADE_TERM_TEXT').text(d.TERM_TEXT || '-');
                $('#PH_GRADE_PICTURE').attr('src', d.PICTURE_URL ? d.PICTURE_URL : '<?php echo WEB_ROOT; ?>module/student/image/1.png');

                var rows = '';
                if (!d.subjects || d.subjects.length === 0) {
                    rows = '<tr><td colspan="3" class="text-muted text-center">No subjects taken yet for this enrollment.</td></tr>';
                } else {
                    $.each(d.subjects, function(i, s) {
                        var remarksOptions = '<option value="">-- Remarks --</option>';
                        $.each(['Passed', 'Failed', 'Incomplete', 'Dropped'], function(j, r) {
                            remarksOptions += '<option value="' + r + '"' + (s.REMARKS === r ? ' selected' : '') + '>' + r + '</option>';
                        });
                        rows += '<tr>' +
                            '<td>' + s.SUBJECT_CODE + '<div class="text-muted small">' + s.SUBJECT_NAME + '</div></td>' +
                            '<td>' +
                                '<input type="hidden" name="GRADE_SUBJECT_ID[]" value="' + s.SUBJECT_ID + '">' +
                                '<input type="number" step="0.01" min="1" max="5" class="form-control form-control-sm" name="GRADE_VALUE[' + s.SUBJECT_ID + ']" value="' + (s.GRADE !== null ? s.GRADE : '') + '">' +
                            '</td>' +
                            '<td>' +
                                '<select class="form-control form-control-sm" name="GRADE_REMARKS[' + s.SUBJECT_ID + ']">' + remarksOptions + '</select>' +
                            '</td>' +
                            '</tr>';
                    });
                }
                $('#PH_GRADE_SUBJECTS_TBODY').html(rows);
                $('#phGradeModal').modal('show');
            } catch (err) {
                alert('DEBUG - JS error sa Grade: ' + err.message);
            }
        },
        error: function(xhr) {
            alert('DEBUG - Ajax failed (Grade). Status: ' + xhr.status + ' | Response: ' + (xhr.responseText ? xhr.responseText.substring(0, 400) : '(walang response)'));
        }
    });
});
</script>
<?php else: ?>
<script type="text/javascript">
var phStudentsTable;

$(document).ready(function() {
    phStudentsTable = $('#tblphstudents').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "<?php echo WEB_ROOT; ?>module/programhead/ajax.php",
            type: "POST",
            data: function (d) { d.act = 'students'; }
        },
        "columnDefs": [
            { "orderable": false, "targets": [7] }
        ]
    });
});

$(document).on('click', '.doAssignSection', function() {
    var eid = $(this).attr('data-eid');
    var syId = $(this).attr('data-sy');
    var yearLevel = $(this).attr('data-yearlevel');
    var currentSection = $(this).attr('data-section');

    $('#AS_EID').val(eid);
    $('#AS_NAME_TEXT').text($(this).attr('data-name'));
    $('#AS_TERM_TEXT').text($(this).attr('data-term'));
    $('#AS_SECTION').html('<option value="">-- No Section --</option>');

    $.ajax({
        url: "<?php echo WEB_ROOT; ?>module/programhead/ajax.php",
        method: "POST",
        data: { act: 'sections_for_select', SY_ID: syId, YEAR_LEVEL: yearLevel },
        dataType: "json",
        success: function(rows) {
            $.each(rows, function(i, r) {
                var sel = (String(r.SECTION_ID) === String(currentSection)) ? 'selected' : '';
                $('#AS_SECTION').append('<option value="'+r.SECTION_ID+'" '+sel+'>'+r.YEAR_LEVEL+' - '+r.SECTION_NAME+'</option>');
            });
            $('#phAssignSectionModal').modal('show');
        }
    });
});

</script>
<?php endif; ?>

<script type="text/javascript">
  // Photo preview - ipinapakita agad ang napiling litrato bago pa i-submit
  // (My Account > Change Photo) - HIPANAO SOLUTIONS.
  $(document).ready( function() {
    $(document).on('change', '.ph-photo-input', function() {
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
  $('#phEditPhoto').on('show.bs.modal', function () {
      $('#phPicture').val('');
      $('#phPicture').next('.custom-file-label').text('Choose photo...');
      $('#phImgUploadPreview').attr('src', $('#phProfilePhoto').attr('src'));
  });
</script>
