<?php

/* HIPANAO SOLUTIONS - Program Head Module > Dashboard.
   Course summary cards + listahan ng LAHAT ng estudyanteng naka-
   enroll sa kanyang COURSE (tblenrollment.COURSE_ID = $phCourseId).
   Dito rin niya ma-a-"Assign Section" ang isang estudyante - ang
   pagpipilian ng section ay galing lang sa mga Section na kanya ring
   course (tblsections.COURSE_ID = $phCourseId), kaya't hindi niya
   kayang ilagay ang isang estudyante sa section ng ibang course. */

global $mydb;

$phStats = array('students' => 0, 'sections' => 0, 'ungraded' => 0);

if ($phCourseId > 0) {
    $mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblenrollment` WHERE COURSE_ID = '".$phCourseId."'");
    $r = $mydb->loadResultList();
    $phStats['students'] = (count($r) >= 1) ? intval($r[0]->CNT) : 0;

    $mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblsections` WHERE COURSE_ID = '".$phCourseId."'");
    $r = $mydb->loadResultList();
    $phStats['sections'] = (count($r) >= 1) ? intval($r[0]->CNT) : 0;

    $mydb->setQuery("SELECT COUNT(*) AS CNT FROM `tblenrollment` WHERE COURSE_ID = '".$phCourseId."' AND STATUS = 'Enroll' AND SECTION_ID IS NULL");
    $r = $mydb->loadResultList();
    $phStats['ungraded'] = (count($r) >= 1) ? intval($r[0]->CNT) : 0;
}
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <?php if (!$phCourse): ?>
      <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle mr-1"></i>
        You are not yet assigned as Program Head of any course. Please contact the Administrator
        so a course can be assigned to your account (Course module &gt; Program Head).
      </div>
    <?php else: ?>

    <!-- ================= Course summary ================= -->
    <div class="row">
      <div class="col-md-6">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <h4 class="mb-1"><i class="fa fa-book mr-1 text-muted"></i> <?php echo htmlspecialchars($phCourse->COURSE_CODE.' - '.$phCourse->COURSE_NAME); ?></h4>
            <p class="text-muted mb-0"><?php echo htmlspecialchars($phCourse->COURSE_DESC ? $phCourse->COURSE_DESC : 'No description.'); ?></p>
            <p class="mb-0 mt-2"><?php echo hipanao_badge($phCourse->STATUS); ?></p>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="small-box bg-info">
          <div class="inner"><h3><?php echo $phStats['students']; ?></h3><p>Enrolled Students</p></div>
          <div class="icon"><i class="fa fa-user-graduate"></i></div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="small-box bg-success">
          <div class="inner"><h3><?php echo $phStats['sections']; ?></h3><p>Sections</p></div>
          <div class="icon"><i class="fa fa-chalkboard"></i></div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="small-box bg-warning">
          <div class="inner"><h3><?php echo $phStats['ungraded']; ?></h3><p>Not yet Sectioned</p></div>
          <div class="icon"><i class="fa fa-exclamation-circle"></i></div>
        </div>
      </div>
    </div>

    <!-- ================= Students under this course ================= -->
    <div class="row">
      <div class="col-12">
        <div class="card card-info card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-user-graduate mr-1 text-muted"></i> Students Enrolled in <?php echo htmlspecialchars($phCourse->COURSE_CODE); ?></h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="tblphstudents" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Student Name</th>
                  <th>Year Level</th>
                  <th>AY / Semester</th>
                  <th>Section</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <?php endif; ?>
  </div>
</section>

<!-- =================================================================
     ASSIGN SECTION - HIPANAO SOLUTIONS. Ang mga pagpipilian sa Section
     dropdown ay ino-load via AJAX (ajax.php?act=sections), i-filter sa
     COURSE_ID (laging sarili niyang course) + SY_ID ng enrollment na
     ito - kaya hindi siya makakapili ng section ng ibang course/taon.
     ================================================================= -->
<div class="modal fade" id="phAssignSectionModal">
  <div class="modal-dialog">
    <form action="controller.php?action=assignsection" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-chalkboard text-info"></i> &nbsp;Assign Section</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="AS_EID" id="AS_EID" value="">
          <p class="mb-1"><b>Student:</b> <span id="AS_NAME_TEXT">-</span></p>
          <p class="mb-3"><b>Year Level / Term:</b> <span id="AS_TERM_TEXT">-</span></p>
          <div class="form-group">
            <label>Section</label>
            <select class="form-control form-control-sm" name="AS_SECTION" id="AS_SECTION">
              <option value="">-- No Section --</option>
            </select>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php /* HIPANAO SOLUTIONS - Ang DataTable init + click handler na dati'y
   nandito ay nailipat na sa module/programhead/index.php (PAGKATAPOS ng
   require_once theme/template.php) - kaparehong pattern ng Admin modules
   (hal. module/course/index.php) - dahil kapag nandito pa ito (na naka-
   require sa GITNA ng template.php, BAGO pa ma-load ang jQuery/DataTables
   sa footer), hindi pa umiiral ang "$" kaya nagkakaroon ng JS error at
   hindi na-i-initialize ang DataTables (walang search box/pagination,
   laging blangko ang table kahit tama ang datos). */ ?>
