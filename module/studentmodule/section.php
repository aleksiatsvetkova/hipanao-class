<?php

/* HIPANAO SOLUTIONS - Student Module > My Section.
   Ipinapakita dito ang kasalukuyang enrollment ng Student - anong
   Course/Year Level/Semester/School Year siya kasama, saang Section
   siya naka-assign, at ang listahan ng subjects na kinuha niya para
   sa enrollment (tblenrollmentdetails) na ito. Kaparehong query/istilo
   ng ginagamit sa module/enrollmentdetails at module/payment (act=row). */

global $mydb;

$secSubjects = array();
$secTotalUnits = 0;

if ($currentEnrollment) {
    $mydb->setQuery("SELECT sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS, sub.SUBJECT_TYPE
        FROM `tblenrollmentdetails` d
        JOIN `tblsubjects` sub ON sub.SUBJECT_ID = d.SUBJECT_ID
        WHERE d.ENROLLMENT_ID = '".intval($currentEnrollment->ENROLLMENT_ID)."'
        ORDER BY sub.SUBJECT_TYPE ASC, sub.SUBJECT_CODE ASC");
    $secSubjects = $mydb->loadResultList();
    foreach ($secSubjects as $s) { $secTotalUnits += intval($s->UNITS); }
}
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <?php if (!$currentEnrollment): ?>
      <div class="alert alert-info">
        <i class="fa fa-info-circle mr-1"></i>
        You do not have an enrollment record yet. Please visit the Registrar's Office to enroll.
      </div>
    <?php else: ?>

    <div class="row">
      <!-- ================= LEFT: Enrollment term summary ================= -->
      <div class="col-md-3">
        <div class="card card-info card-outline mb-0">
          <div class="card-body box-profile text-center">
            <img src="<?php echo $studentPhotoUrl; ?>"
                 class="profile-user-img img-fluid img-circle"
                 style="width:100px; height:100px; object-fit:cover;"
                 onerror="this.src='<?php echo WEB_ROOT; ?>module/student/image/1.png'"
                 alt="Student photo">
            <h3 class="profile-username text-center mt-2 mb-0" style="font-size:1rem;"><?php echo htmlspecialchars($studentRow->IDNO); ?></h3>
            <p class="text-muted text-center mb-0"><?php echo htmlspecialchars(trim($studentRow->FNAME.' '.$studentRow->LNAME)); ?></p>
            <p class="text-center mb-0"><?php echo hipanao_badge($currentEnrollment->STATUS); ?></p>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item p-2">
              <small class="text-muted d-block">Course</small>
              <?php echo htmlspecialchars($currentEnrollment->COURSE_CODE.' - '.$currentEnrollment->COURSE_NAME); ?>
            </li>
            <li class="list-group-item p-2">
              <small class="text-muted d-block">Year Level / Semester</small>
              <?php echo htmlspecialchars($currentEnrollment->YEAR_LEVEL.' - '.$currentEnrollment->SEMESTER); ?>
            </li>
            <li class="list-group-item p-2">
              <small class="text-muted d-block">School Year</small>
              <?php echo htmlspecialchars($currentEnrollment->SCHOOL_YEAR); ?>
            </li>
            <li class="list-group-item p-2">
              <small class="text-muted d-block">Section</small>
              <?php echo htmlspecialchars($currentEnrollment->SECTION_NAME ? $currentEnrollment->SECTION_NAME : 'Not sectioned yet'); ?>
            </li>
            <li class="list-group-item p-2">
              <small class="text-muted d-block">Program Head / Adviser</small>
              <?php echo htmlspecialchars($currentEnrollment->PROGRAM_HEAD ? $currentEnrollment->PROGRAM_HEAD : '-'); ?>
            </li>
            <li class="list-group-item p-2">
              <small class="text-muted d-block">Category</small>
              <?php echo htmlspecialchars($currentEnrollment->CATEGORY ? $currentEnrollment->CATEGORY : '-'); ?>
            </li>
          </ul>
        </div>
      </div>

      <!-- ================= RIGHT: Subjects taken ================= -->
      <div class="col-md-9">
        <div class="card card-info card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-list-alt mr-1 text-muted"></i> Subjects for this Term</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Subject</th>
                  <th>Type</th>
                  <th>Units</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($secSubjects) < 1): ?>
                  <tr><td colspan="4" class="text-muted text-center">No subjects assigned yet.</td></tr>
                <?php else: foreach ($secSubjects as $s): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($s->SUBJECT_CODE); ?></td>
                    <td><?php echo htmlspecialchars($s->SUBJECT_NAME); ?></td>
                    <td><?php echo hipanao_badge($s->SUBJECT_TYPE); ?></td>
                    <td><?php echo htmlspecialchars($s->UNITS); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
              <?php if (count($secSubjects) >= 1): ?>
              <tfoot>
                <tr>
                  <th colspan="3" class="text-right">Total Units</th>
                  <th><?php echo $secTotalUnits; ?></th>
                </tr>
              </tfoot>
              <?php endif; ?>
            </table>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>
  </div>
</section>
