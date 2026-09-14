<?php

/* HIPANAO SOLUTIONS - Student Module > Enrollment History.
   Mga naka-archive nang enrollment term ng estudyanteng ito
   (tblhistoryenrollment) - "Completed" (naipalit dahil nag-Register
   Again + nag-Enroll ulit sa bagong term) o "Dropped". Kaparehong
   join pattern ng module/hitoryenrollment/ajax.php - dahil hindi
   tinatanggal ang orihinal na tblenrollment row kapag na-archive,
   nandiyan pa rin ang SECTION_ID doon para makuha ang SECTION_NAME. */

global $mydb;

$smHistory = array();
if ($sid > 0) {
    $mydb->setQuery("SELECT h.*, c.COURSE_NAME, c.COURSE_CODE, sy.SCHOOL_YEAR, sec.SECTION_NAME
        FROM `tblhistoryenrollment` h
        JOIN `tblcourses`    c  ON c.COURSE_ID = h.COURSE_ID
        JOIN `tblschoolyear` sy ON sy.SY_ID    = h.SY_ID
        LEFT JOIN `tblenrollment` e ON e.ENROLLMENT_ID = h.ENROLLMENT_ID
        LEFT JOIN `tblsections`   sec ON sec.SECTION_ID = e.SECTION_ID
        WHERE h.S_ID = '".$sid."'
        ORDER BY h.DATE_ARCHIVED DESC");
    $smHistory = $mydb->loadResultList();
}
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <div class="row">
      <div class="col-12">
        <div class="card card-secondary card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-history mr-1 text-muted"></i> Enrollment History</h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table class="table table-bordered table-striped mb-0">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Course</th>
                  <th>Year Level</th>
                  <th>Semester</th>
                  <th>School Year</th>
                  <th>Section</th>
                  <th>Status</th>
                  <th>Date Reserved</th>
                  <th>Date Enrolled</th>
                  <th>Archived On</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($smHistory) < 1): ?>
                  <tr><td colspan="10" class="text-muted text-center">No enrollment history yet.</td></tr>
                <?php else: $i = 1; foreach ($smHistory as $h): ?>
                  <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars($h->COURSE_CODE.' - '.$h->COURSE_NAME); ?></td>
                    <td><?php echo htmlspecialchars($h->YEAR_LEVEL); ?></td>
                    <td><?php echo htmlspecialchars($h->SEMESTER); ?></td>
                    <td><?php echo htmlspecialchars($h->SCHOOL_YEAR); ?></td>
                    <td><?php echo htmlspecialchars($h->SECTION_NAME ? $h->SECTION_NAME : '-'); ?></td>
                    <td><?php echo hipanao_badge($h->STATUS); ?></td>
                    <td><?php echo htmlspecialchars($h->DATE_RESERVED ? $h->DATE_RESERVED : '-'); ?></td>
                    <td><?php echo htmlspecialchars($h->DATE_ENROLLED ? $h->DATE_ENROLLED : '-'); ?></td>
                    <td><?php echo htmlspecialchars($h->DATE_ARCHIVED); ?></td>
                  </tr>
                <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <p class="text-muted small mt-2">
      <i class="fa fa-info-circle mr-1"></i>
      Want to see the subjects and grades for a past term? Check
      <a href="<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=grades">My Grades</a>.
    </p>

  </div>
</section>
