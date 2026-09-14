<?php

/* HIPANAO SOLUTIONS - Student Module > My Grades.
   Current Term Grades - ang subjects + grades para sa $currentEnrollment
   (kaparehong query pattern ng module/grade/ajax.php - itinutugma ang
   ENROLLMENT_ID + SUBJECT_ID, HINDI ang IDNO string, para talagang
   tama ang nakukuhang grade).
   Grade History - lahat ng IBANG enrollment term ng estudyanteng ito
   (mga nakaraang term na - "Completed" o "Dropped" na), bawat isa may
   sariling maliit na table ng subjects + grades. */

global $mydb;

function sm_load_grade_rows($enrollmentId) {
    global $mydb;
    $mydb->setQuery("SELECT sub.SUBJECT_CODE, sub.SUBJECT_NAME, sub.UNITS, sub.SUBJECT_TYPE,
            g.GRADE, g.REMARKS
        FROM `tblenrollmentdetails` d
        JOIN `tblsubjects` sub ON sub.SUBJECT_ID = d.SUBJECT_ID
        LEFT JOIN `tblgrades` g ON g.ENROLLMENT_ID = d.ENROLLMENT_ID AND g.SUBJECT_ID = d.SUBJECT_ID
        WHERE d.ENROLLMENT_ID = '".intval($enrollmentId)."'
        ORDER BY sub.SUBJECT_TYPE ASC, sub.SUBJECT_CODE ASC");
    return $mydb->loadResultList();
}

$smCurrentGrades = $currentEnrollment ? sm_load_grade_rows($currentEnrollment->ENROLLMENT_ID) : array();

$smHistoryTerms = array();
if ($sid > 0) {
    $excludeId = $currentEnrollment ? intval($currentEnrollment->ENROLLMENT_ID) : 0;
    $mydb->setQuery("SELECT e.ENROLLMENT_ID, e.YEAR_LEVEL, e.SEMESTER, e.STATUS,
            c.COURSE_CODE, c.COURSE_NAME, sy.SCHOOL_YEAR
        FROM `tblenrollment` e
        JOIN `tblcourses`    c  ON c.COURSE_ID = e.COURSE_ID
        JOIN `tblschoolyear` sy ON sy.SY_ID    = e.SY_ID
        WHERE e.S_ID = '".$sid."' AND e.ENROLLMENT_ID <> '".$excludeId."'
        ORDER BY e.ENROLLMENT_ID DESC");
    $smHistoryTerms = $mydb->loadResultList();
}
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <!-- ================= Current Term Grades ================= -->
    <div class="row">
      <div class="col-12">
        <div class="card card-info card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-graduation-cap mr-1 text-muted"></i> Current Term Grades</h3>
              </div>
              <?php if ($currentEnrollment): ?>
              <div class="hipanao-toolbar-actions">
                <span class="text-muted small"><?php echo htmlspecialchars($currentEnrollment->SCHOOL_YEAR.' / '.$currentEnrollment->SEMESTER); ?></span>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="card-body">
            <?php if (!$currentEnrollment): ?>
              <p class="text-muted text-center mb-0">You do not have an enrollment record yet.</p>
            <?php else: ?>
            <table class="table table-bordered table-striped mb-0">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Subject</th>
                  <th>Type</th>
                  <th>Units</th>
                  <th>Grade</th>
                  <th>Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($smCurrentGrades) < 1): ?>
                  <tr><td colspan="6" class="text-muted text-center">No subjects assigned yet.</td></tr>
                <?php else: foreach ($smCurrentGrades as $g): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($g->SUBJECT_CODE); ?></td>
                    <td><?php echo htmlspecialchars($g->SUBJECT_NAME); ?></td>
                    <td><?php echo hipanao_badge($g->SUBJECT_TYPE); ?></td>
                    <td><?php echo htmlspecialchars($g->UNITS); ?></td>
                    <td><?php echo ($g->GRADE === null) ? '<span class="text-muted">-</span>' : htmlspecialchars($g->GRADE); ?></td>
                    <td><?php echo $g->REMARKS ? hipanao_badge($g->REMARKS) : '<span class="text-muted">-</span>'; ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- ================= Grade History ================= -->
    <div class="row">
      <div class="col-12">
        <div class="card card-secondary card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-history mr-1 text-muted"></i> Grade History</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <?php if (count($smHistoryTerms) < 1): ?>
              <p class="text-muted text-center mb-0">No previous terms yet.</p>
            <?php else: foreach ($smHistoryTerms as $term):
                $termGrades = sm_load_grade_rows($term->ENROLLMENT_ID);
            ?>
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h6 class="mb-0">
                    <?php echo htmlspecialchars($term->COURSE_CODE); ?> -
                    <?php echo htmlspecialchars($term->YEAR_LEVEL.' / '.$term->SEMESTER); ?>
                    <span class="text-muted">(<?php echo htmlspecialchars($term->SCHOOL_YEAR); ?>)</span>
                  </h6>
                  <?php echo hipanao_badge($term->STATUS); ?>
                </div>
                <table class="table table-bordered table-sm mb-0">
                  <thead class="thead-light">
                    <tr>
                      <th>Code</th>
                      <th>Subject</th>
                      <th>Type</th>
                      <th>Units</th>
                      <th>Grade</th>
                      <th>Remarks</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (count($termGrades) < 1): ?>
                      <tr><td colspan="6" class="text-muted text-center">No subjects on record.</td></tr>
                    <?php else: foreach ($termGrades as $g): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($g->SUBJECT_CODE); ?></td>
                        <td><?php echo htmlspecialchars($g->SUBJECT_NAME); ?></td>
                        <td><?php echo hipanao_badge($g->SUBJECT_TYPE); ?></td>
                        <td><?php echo htmlspecialchars($g->UNITS); ?></td>
                        <td><?php echo ($g->GRADE === null) ? '<span class="text-muted">-</span>' : htmlspecialchars($g->GRADE); ?></td>
                        <td><?php echo $g->REMARKS ? hipanao_badge($g->REMARKS) : '<span class="text-muted">-</span>'; ?></td>
                      </tr>
                    <?php endforeach; endif; ?>
                  </tbody>
                </table>
              </div>
            <?php endforeach; endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>
