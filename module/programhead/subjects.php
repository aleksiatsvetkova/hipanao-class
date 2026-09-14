<?php

/* HIPANAO SOLUTIONS - Program Head Module > Subjects.
   ==========================================================
   Bagong tab (kahilingan ni Program Head) - dito na rin siya
   makakapag-"Add New Subject" (Major) PARA SA SARILI niyang
   course, kaparehong-kapareho ng "Sections" tab sa itaas:
   listahan (DataTable, server-side via ajax.php act=subjects_list)
   + Add/Edit modal, COURSE_ID ay laging galing sa $phCourseId
   (hindi kailanman sa POST ng user - tingnan controller.php).

   Minor subjects (COURSE_ID = NULL, available sa lahat ng course,
   ginagawa lang ng Admin sa module/subject/) ay ipinapakita rin
   dito bilang reference lang (may badge na "All Courses"), pero
   hindi na-e-edit/delete ng Program Head - sariling Major subjects
   lang niya (COURSE_ID = $phCourseId) ang maaari niyang baguhin. */

global $mydb;
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
                <h3 class="card-title mb-0"><i class="fa fa-book-open mr-1 text-muted"></i> Subjects - <?php echo htmlspecialchars($phCourse->COURSE_CODE); ?></h3>
              </div>
              <div class="hipanao-toolbar-actions">
                <button type="button" class="btn btn-primary btn-add-new" data-toggle="modal" data-target="#phAddSubject"><i class="fa fa-plus mr-1"></i>Add New Subject</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <?php if (!$mydb->tableExists('tblsubjects')): ?>
            <div class="alert alert-warning mb-3">
              <i class="fa fa-exclamation-triangle mr-1"></i>
              Table <code>tblsubjects</code> was not found in the database.
              The SQL file may not have been fully imported yet. The rest of the system will still work normally.
            </div>
            <?php endif; ?>
            <table id="tblphsubjects" class="table table-bordered table-striped" style="width:100%">
              <thead>
                <tr>
                  <th width="5%">#</th>
                  <th>Subject Code</th>
                  <th>Subject Name</th>
                  <th>Units</th>
                  <th>Year Level</th>
                  <th>Semester</th>
                  <th>Type</th>
                  <th width="9%">Action</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- =============== Add New Subject =============== -->
    <div class="modal fade" id="phAddSubject">
      <div class="modal-dialog">
        <form action="controller.php?action=addsubject" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-plus text-primary"></i> &nbsp;Add New Subject</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="form-group">
                <label>Course</label>
                <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($phCourse->COURSE_CODE.' - '.$phCourse->COURSE_NAME); ?>" disabled>
                <small class="form-text text-muted">Subjects you add here are always Major subjects under your own course.</small>
              </div>
              <div class="form-group">
                <label for="PH_SUBJ_CODE">Subject Code</label>
                <input type="text" class="form-control form-control-sm" name="SUBJECT_CODE" id="PH_SUBJ_CODE" placeholder="e.g. IT101" required>
              </div>
              <div class="form-group">
                <label for="PH_SUBJ_NAME">Subject Name</label>
                <input type="text" class="form-control form-control-sm" name="SUBJECT_NAME" id="PH_SUBJ_NAME" placeholder="e.g. Introduction to Computing" required>
              </div>
              <div class="form-group">
                <label for="PH_SUBJ_UNITS">Units</label>
                <input type="number" step="1" min="1" class="form-control form-control-sm" name="UNITS" id="PH_SUBJ_UNITS" placeholder="3" value="3" required>
              </div>
              <div class="form-group">
                <label for="PH_SUBJ_YL">Year Level</label>
                <select class="form-control form-control-sm" name="YEAR_LEVEL" id="PH_SUBJ_YL" required>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                </select>
              </div>
              <div class="form-group">
                <label for="PH_SUBJ_SEM">Semester</label>
                <select class="form-control form-control-sm" name="SEMESTER" id="PH_SUBJ_SEM" required>
                  <option value="1st Semester">1st Semester</option>
                  <option value="2nd Semester">2nd Semester</option>
                  <option value="Summer">Summer</option>
                </select>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Subject</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- =============== Edit Subject =============== -->
    <div class="modal fade" id="phEditSubject">
      <div class="modal-dialog">
        <form action="controller.php?action=editsubject" method="POST">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-pen text-warning"></i> &nbsp;Modify Subject</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="SUBJECT_ID" id="PH_ESUBJ_ID">
              <div class="form-group">
                <label>Course</label>
                <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($phCourse->COURSE_CODE.' - '.$phCourse->COURSE_NAME); ?>" disabled>
              </div>
              <div class="form-group">
                <label for="PH_ESUBJ_CODE">Subject Code</label>
                <input type="text" class="form-control form-control-sm" name="SUBJECT_CODE" id="PH_ESUBJ_CODE" required>
              </div>
              <div class="form-group">
                <label for="PH_ESUBJ_NAME">Subject Name</label>
                <input type="text" class="form-control form-control-sm" name="SUBJECT_NAME" id="PH_ESUBJ_NAME" required>
              </div>
              <div class="form-group">
                <label for="PH_ESUBJ_UNITS">Units</label>
                <input type="number" step="1" min="1" class="form-control form-control-sm" name="UNITS" id="PH_ESUBJ_UNITS" required>
              </div>
              <div class="form-group">
                <label for="PH_ESUBJ_YL">Year Level</label>
                <select class="form-control form-control-sm" name="YEAR_LEVEL" id="PH_ESUBJ_YL" required>
                  <option value="1st Year">1st Year</option>
                  <option value="2nd Year">2nd Year</option>
                  <option value="3rd Year">3rd Year</option>
                  <option value="4th Year">4th Year</option>
                </select>
              </div>
              <div class="form-group">
                <label for="PH_ESUBJ_SEM">Semester</label>
                <select class="form-control form-control-sm" name="SEMESTER" id="PH_ESUBJ_SEM" required>
                  <option value="1st Semester">1st Semester</option>
                  <option value="2nd Semester">2nd Semester</option>
                  <option value="Summer">Summer</option>
                </select>
              </div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save changes</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php endif; ?>
  </div>
</section>

<?php /* HIPANAO SOLUTIONS - DataTable init nasa index.php
   (pagkatapos ng require_once theme/template.php) - tingnan ang
   paliwanag sa dashboard.php kung bakit dito ito dapat, hindi dito
   sa content file. */ ?>
