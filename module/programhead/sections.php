<?php

/* HIPANAO SOLUTIONS - Program Head Module > Sections.
   Listahan LANG ng Sections ng sarili niyang course
   (tblsections.COURSE_ID = $phCourseId) - "Add New Section" ang
   tanging aksyon dito (COURSE_ID ay hidden field, laging galing sa
   $phCourseId, hindi sa POST ng user, tingnan controller.php). */

global $mydb;

$phSchoolYears = array();
$mydb->setQuery("SELECT SY_ID, SCHOOL_YEAR FROM `tblschoolyear` ORDER BY SCHOOL_YEAR DESC");
$phSchoolYears = $mydb->loadResultList();
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
        <div class="card card-primary card-outline">
          <div class="card-header">
            <div class="hipanao-toolbar">
              <div>
                <h3 class="card-title mb-0"><i class="fa fa-chalkboard mr-1 text-muted"></i> Sections - <?php echo htmlspecialchars($phCourse->COURSE_CODE); ?></h3>
              </div>
              <div class="hipanao-toolbar-actions">
                <button type="button" class="btn btn-primary btn-add-new" data-toggle="modal" data-target="#phAddSection"><i class="fa fa-plus mr-1"></i>Add New Section</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="tblphsections" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Section Name</th>
                  <th>Year Level</th>
                  <th>School Year</th>
                  <th>No. of Students</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- =============== Add New Section =============== -->
    <div class="modal fade" id="phAddSection">
      <div class="modal-dialog">
        <form action="controller.php?action=addsection" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-plus text-primary"></i> &nbsp;Add New Section</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Course</label>
                <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($phCourse->COURSE_CODE.' - '.$phCourse->COURSE_NAME); ?>" disabled>
              </div>
              <div class="form-group">
                <label>Section Name</label>
                <input type="text" class="form-control form-control-sm" name="SEC_NAME" placeholder="e.g. A, B, Diamond, Emerald" required>
              </div>
              <div class="form-group">
                <label>Year Level</label>
                <select class="form-control form-control-sm" name="SEC_YEARLEVEL" required>
                  <option value="">-- Select Year Level --</option>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                </select>
              </div>
              <div class="form-group">
                <label>School Year</label>
                <select class="form-control form-control-sm" name="SEC_SY" required>
                  <option value="">-- Select School Year --</option>
                  <?php foreach ($phSchoolYears as $sy): ?>
                    <option value="<?php echo $sy->SY_ID; ?>"><?php echo htmlspecialchars($sy->SCHOOL_YEAR); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Section</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php endif; ?>
  </div>
</section>

<?php /* HIPANAO SOLUTIONS - DataTable init nailipat na sa index.php
   (pagkatapos ng require_once theme/template.php) - tingnan ang paliwanag
   sa dashboard.php kung bakit dito ito dapat, hindi dito sa content file. */ ?>
