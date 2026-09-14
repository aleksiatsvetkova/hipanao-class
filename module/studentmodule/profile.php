<?php

/* HIPANAO SOLUTIONS - Student Module > My Profile.
   Kaparehong disenyo ng "View Profile" modal na makikita sa Enrollment
   Details / History Enrollment (col-md-3 profile card sa kaliwa, mga
   info card sa kanan) - dinala lang dito sa sarili nang full page ng
   Student, dahil ito na ang tanging record na dapat niyang makita. */
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <?php if (!$studentRow): ?>
      <div class="alert alert-warning">
        <i class="fa fa-exclamation-triangle mr-1"></i>
        We could not find your student record. Please contact the Registrar's Office.
      </div>
    <?php else: ?>

    <div class="row">
      <!-- ================= LEFT: Profile summary card ================= -->
      <div class="col-md-3">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile text-center">
            <div class="hipanao-photo-upload d-inline-block" style="width:120px;">
              <img id="sdProfilePhoto" src="<?php echo $studentPhotoUrl; ?>"
                   class="profile-user-img img-fluid img-circle"
                   style="width:120px; height:120px; object-fit:cover;"
                   onerror="this.src='<?php echo WEB_ROOT; ?>module/student/image/1.png'"
                   alt="Student photo">
            </div>
            <br>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-toggle="modal" data-target="#sdEditPhoto">
              <i class="fa fa-camera mr-1"></i> Change Photo
            </button>
            <h3 class="profile-username text-center mt-2 mb-0"><?php echo htmlspecialchars(trim($studentRow->FNAME.' '.$studentRow->MNAME.' '.$studentRow->LNAME)); ?></h3>
            <p class="text-muted text-center mb-0">ID No.: <?php echo htmlspecialchars($studentRow->IDNO); ?></p>
            <p class="text-center mb-0"><?php echo hipanao_badge($studentRow->STATUS); ?></p>
          </div>
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <b>Course</b> <span class="float-right"><?php echo htmlspecialchars($studentRow->COURSE_CODE ? $studentRow->COURSE_CODE : '-'); ?></span>
            </li>
            <li class="list-group-item">
              <b>LRN No.</b> <span class="float-right"><?php echo htmlspecialchars($studentRow->LRNNO ? $studentRow->LRNNO : '-'); ?></span>
            </li>
            <?php if ($currentEnrollment): ?>
            <li class="list-group-item">
              <b>Year Level</b> <span class="float-right"><?php echo htmlspecialchars($currentEnrollment->YEAR_LEVEL); ?></span>
            </li>
            <li class="list-group-item">
              <b>Section</b> <span class="float-right"><?php echo htmlspecialchars($currentEnrollment->SECTION_NAME ? $currentEnrollment->SECTION_NAME : 'Not sectioned'); ?></span>
            </li>
            <?php endif; ?>
          </ul>
        </div>

        <div class="card card-outline card-secondary">
          <div class="card-header p-2"><h6 class="mb-0 pl-1"><i class="fa fa-clipboard-list mr-1 text-muted"></i>Quick Links</h6></div>
          <div class="card-body p-2">
            <a href="<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=section" class="btn btn-outline-primary btn-block btn-sm mb-2"><i class="fa fa-chalkboard mr-1"></i> My Section</a>
            <a href="<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=grades" class="btn btn-outline-info btn-block btn-sm mb-2"><i class="fa fa-graduation-cap mr-1"></i> My Grades</a>
            <a href="<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=history" class="btn btn-outline-secondary btn-block btn-sm mb-2"><i class="fa fa-history mr-1"></i> Enrollment History</a>
            <a href="<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=payment" class="btn btn-outline-success btn-block btn-sm mb-0"><i class="fa fa-money-check-alt mr-1"></i> My Payments</a>
          </div>
        </div>
      </div>

      <!-- ================= RIGHT: Info cards ================= -->
      <div class="col-md-9">

        <div class="card card-outline mb-3">
          <div class="card-header p-2"><h6 class="mb-0 pl-1"><i class="fa fa-id-card mr-1 text-muted"></i>Personal Information</h6></div>
          <div class="card-body p-2">
            <table class="table table-bordered table-sm mb-0">
              <tr>
                <th style="width:25%">Gender</th>
                <td style="width:25%"><?php echo hipanao_badge($studentRow->SEX); ?></td>
                <th style="width:25%">Birthday</th>
                <td><?php echo htmlspecialchars($studentRow->BDAY ? $studentRow->BDAY : '-'); ?></td>
              </tr>
              <tr>
                <th>Birth Place</th>
                <td><?php echo htmlspecialchars($studentRow->BPLACE ? $studentRow->BPLACE : '-'); ?></td>
                <th>Age</th>
                <td><?php echo htmlspecialchars($studentRow->AGE ? $studentRow->AGE : '-'); ?></td>
              </tr>
              <tr>
                <th>Nationality</th>
                <td><?php echo htmlspecialchars($studentRow->NATIONALITY ? $studentRow->NATIONALITY : '-'); ?></td>
                <th>Religion</th>
                <td><?php echo htmlspecialchars($studentRow->RELIGION ? $studentRow->RELIGION : '-'); ?></td>
              </tr>
              <tr>
                <th>Civil Status</th>
                <td colspan="3"><?php echo htmlspecialchars($studentRow->CIVIL_STATUS ? $studentRow->CIVIL_STATUS : '-'); ?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="card card-outline mb-3">
          <div class="card-header p-2"><h6 class="mb-0 pl-1"><i class="fa fa-address-book mr-1 text-muted"></i>Contact Information</h6></div>
          <div class="card-body p-2">
            <table class="table table-bordered table-sm mb-0">
              <tr>
                <th style="width:25%">Contact Number</th>
                <td><?php echo htmlspecialchars($studentRow->CONTACT_NO ? $studentRow->CONTACT_NO : '-'); ?></td>
              </tr>
              <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($studentRow->EMAIL ? $studentRow->EMAIL : '-'); ?></td>
              </tr>
              <tr>
                <th>Home Address</th>
                <td><?php echo htmlspecialchars($studentRow->HOME_ADD ? $studentRow->HOME_ADD : '-'); ?></td>
              </tr>
              <tr>
                <th>Contact Person</th>
                <td><?php echo htmlspecialchars($studentRow->CONTACTPERSON ? $studentRow->CONTACTPERSON : '-'); ?></td>
              </tr>
            </table>
          </div>
        </div>

        <div class="card card-outline mb-0">
          <div class="card-header p-2"><h6 class="mb-0 pl-1"><i class="fa fa-users mr-1 text-muted"></i>Parents' / Guardian's Information</h6></div>
          <div class="card-body p-2">
            <table class="table table-bordered table-sm mb-2">
              <thead class="thead-light">
                <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Contact No.</th>
                  <th>Email</th>
                  <th>Occupation</th>
                  <th>Deceased</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th>Father</th>
                  <td><?php echo htmlspecialchars($studentRow->FATHER_NAME ? $studentRow->FATHER_NAME : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->FATHER_CONTACT ? $studentRow->FATHER_CONTACT : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->FATHER_EMAIL ? $studentRow->FATHER_EMAIL : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->FATHER_OCCUPATION ? $studentRow->FATHER_OCCUPATION : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->FATHER_DECEASED ? $studentRow->FATHER_DECEASED : 'No'); ?></td>
                </tr>
                <tr>
                  <th>Mother</th>
                  <td><?php echo htmlspecialchars($studentRow->MOTHER_NAME ? $studentRow->MOTHER_NAME : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->MOTHER_CONTACT ? $studentRow->MOTHER_CONTACT : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->MOTHER_EMAIL ? $studentRow->MOTHER_EMAIL : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->MOTHER_OCCUPATION ? $studentRow->MOTHER_OCCUPATION : '-'); ?></td>
                  <td><?php echo htmlspecialchars($studentRow->MOTHER_DECEASED ? $studentRow->MOTHER_DECEASED : 'No'); ?></td>
                </tr>
              </tbody>
            </table>
            <table class="table table-bordered table-sm mb-0">
              <tr>
                <th style="width:20%">Guardian Name</th>
                <td><?php echo htmlspecialchars($studentRow->GUARDIAN_NAME ? $studentRow->GUARDIAN_NAME : '-'); ?></td>
                <th style="width:20%">Relationship</th>
                <td><?php echo htmlspecialchars($studentRow->GUARDIAN_RELATIONSHIP ? $studentRow->GUARDIAN_RELATIONSHIP : '-'); ?></td>
              </tr>
              <tr>
                <th>Contact No.</th>
                <td><?php echo htmlspecialchars($studentRow->GUARDIAN_CONTACT ? $studentRow->GUARDIAN_CONTACT : '-'); ?></td>
                <th>Email</th>
                <td><?php echo htmlspecialchars($studentRow->GUARDIAN_EMAIL ? $studentRow->GUARDIAN_EMAIL : '-'); ?></td>
              </tr>
              <tr>
                <th>Address</th>
                <td colspan="3"><?php echo htmlspecialchars($studentRow->GUARDIAN_ADDRESS ? $studentRow->GUARDIAN_ADDRESS : '-'); ?></td>
              </tr>
            </table>
          </div>
        </div>

      </div>
    </div>

    <!-- =================================================================
         CHANGE PROFILE PHOTO - HIPANAO SOLUTIONS. Sariling litrato lang
         ang kaya nitong palitan (S_ID galing sa SESSION, sa controller.php,
         hindi sa form) - saka na-sync sa naka-link na login account.
         ================================================================= -->
    <div class="modal fade" id="sdEditPhoto">
      <div class="modal-dialog">
        <form action="controller.php?action=editphoto" method="POST" enctype="multipart/form-data">
          <div class="modal-content">
            <div class="modal-header">
              <h4 class="modal-title"><i class="fa fa-camera"></i> &nbsp;Change Profile Photo</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body text-center">
              <div class="hipanao-photo-upload d-inline-block">
                <img id="sdImgUploadPreview" src="<?php echo $studentPhotoUrl; ?>"
                     style="width:120px; height:120px; object-fit:cover; border-radius:50%;"
                     onerror="this.src='<?php echo WEB_ROOT; ?>module/student/image/1.png'"
                     alt="Student photo preview">
                <div class="custom-file mt-2">
                  <input type="file" name="PICTURE" id="sdPicture" class="custom-file-input student-photo-input" data-preview="#sdImgUploadPreview" accept="image/*" required>
                  <label class="custom-file-label" for="sdPicture">Choose photo...</label>
                </div>
              </div>
              <p class="text-muted mt-2 mb-0" style="font-size:0.85rem;">JPG, PNG, GIF, or WEBP.</p>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Photo</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <?php endif; ?>
  </div>
</section>
