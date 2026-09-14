<?php

/* HIPANAO SOLUTIONS - Program Head Module > Grades.
   Kaparehong disenyo ng Admin > Grades (module/grade/) - listahan ng
   estudyanteng may subject na kinuha, "Grade" button para lagyan ng
   grade bawat subject niya nang sabay-sabay - pero dito ay LIMITADO
   LANG sa mga estudyanteng naka-enroll sa SARILING course ng Program
   Head (COURSE_ID = $phCourseId, ipinapatupad sa ajax.php/controller.php). */
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <?php if (!$phCourse): ?>
      <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle mr-1"></i>
        You are not yet assigned as Program Head of any course. Please contact the Administrator.
      </div>
    <?php else: ?>

    <div class="row">
      <div class="col-12">
        <div class="card card-info card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-graduation-cap mr-1 text-muted"></i> Grades - <?php echo htmlspecialchars($phCourse->COURSE_CODE); ?></h3>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="tblphgradestudents" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>ID No.</th>
                  <th>Student Name</th>
                  <th>Year Level</th>
                  <th>AY / Semester</th>
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
     ENCODE GRADES - kaparehong disenyo ng module/grade/list.php.
     ================================================================= -->
<div class="modal fade" id="phGradeModal">
  <div class="modal-dialog modal-lg">
    <form action="controller.php?action=savegrades" method="POST">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><i class="fa fa-graduation-cap text-info"></i> &nbsp;Encode Grades</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="GRADE_EID" id="PH_GRADE_EID" value="">
          <div class="row">
            <div class="col-md-3">
              <div class="card card-info card-outline mb-0">
                <div class="card-body box-profile text-center">
                  <img id="PH_GRADE_PICTURE" class="profile-user-img img-fluid img-circle"
                       src="<?php echo WEB_ROOT; ?>module/student/image/1.png" alt="Student photo">
                  <h3 class="profile-username text-center mt-2 mb-0" id="PH_GRADE_IDNO_TEXT" style="font-size:1rem;">-</h3>
                  <p class="text-muted text-center mb-0" id="PH_GRADE_NAME_TEXT">-</p>
                </div>
                <ul class="list-group list-group-flush">
                  <li class="list-group-item p-2">
                    <small class="text-muted d-block">AY / Semester</small>
                    <span id="PH_GRADE_TERM_TEXT">-</span>
                  </li>
                </ul>
              </div>
            </div>
            <div class="col-md-9">
              <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                  <tr>
                    <th>Subject</th>
                    <th style="width:25%">Grade</th>
                    <th style="width:30%">Remarks</th>
                  </tr>
                </thead>
                <tbody id="PH_GRADE_SUBJECTS_TBODY">
                  <tr><td colspan="3" class="text-muted text-center">No subjects.</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Grades</button>
        </div>
      </div>
    </form>
  </div>
</div>

<?php /* HIPANAO SOLUTIONS - DataTable init + phDoGrade click handler
   nailipat na sa index.php (pagkatapos ng require_once theme/template.php)
   - tingnan ang paliwanag sa dashboard.php kung bakit dito ito dapat. */ ?>
