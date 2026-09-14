<?php

/* HIPANAO SOLUTIONS - Student Dashboard content.
   Ito ang laman ng dashboard na makikita ng Student pagka-login niya -
   ang sarili niyang record lang, walang ibang module gaya ng hiningi:
   "makikita dashboard lang muna nang student". Dating nasa home.php
   ito (sa loob ng branch na `TYPE === 'Student'`) - inilipat dito sa
   theme/studentdashboard/ para maka-sariling layout na ang Student
   dashboard. */

global $mydb;

$studentRow = null;
$sid = isset($_SESSION['STUDENT_SID']) ? intval($_SESSION['STUDENT_SID']) : 0;

if ($sid > 0) {
    $mydb->setQuery("SELECT s.*, c.COURSE_NAME, c.COURSE_CODE
        FROM `tblstudent` s
        LEFT JOIN `tblcourses` c ON c.COURSE_ID = s.COURSE_ID
        WHERE s.S_ID = '".$sid."' LIMIT 1");
    $rows = $mydb->loadResultList();
    if (count($rows) >= 1) { $studentRow = $rows[0]; }
}

$photoUrl = (isset($studentRow->COMPANYIDNO) && $studentRow->COMPANYIDNO != '')
    ? WEB_ROOT.'module/student/image/'.$studentRow->COMPANYIDNO
    : WEB_ROOT.'module/student/image/1.png';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary card-outline">
                <div class="card-body text-center">
                    <?php if ($studentRow): ?>
                        <img src="<?php echo $photoUrl; ?>" class="img-circle elevation-2" style="width: 100px; height: 100px; object-fit: cover;" onerror="this.src='<?php echo WEB_ROOT; ?>module/student/image/1.png'">
                        <h3 class="mt-3 mb-0">Welcome, <?php echo htmlspecialchars(trim($studentRow->FNAME.' '.$studentRow->LNAME)); ?>!</h3>
                        <p class="text-muted">Student ID No.: <?php echo htmlspecialchars($studentRow->IDNO); ?></p>
                        <hr>
                        <div class="row text-left">
                            <div class="col-md-6">
                                <p><strong>Course:</strong> <?php echo htmlspecialchars($studentRow->COURSE_NAME ? $studentRow->COURSE_NAME : '-'); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> <?php echo hipanao_badge($studentRow->STATUS); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <h3 class="mt-3">Welcome!</h3>
                        <p class="text-muted">We could not find your student record. Please contact the Registrar's Office.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
