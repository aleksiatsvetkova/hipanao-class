<?php

/* HIPANAO SOLUTIONS - Student Module > My Account.
   ==========================================================
   Hiwalay na page ito sa "My Profile" (profile.php) - dito na LANG
   dapat pumunta ang Student kung gusto niyang baguhin ang KANYANG
   SARILING LOGIN ACCOUNT: profile photo at password. Wala nang ibang
   maeedit dito (hindi katulad ng My Profile na "view-only" na listahan
   ng personal/contact/family info niya galing sa tblstudent).

   - Change Photo  -> parehong controller.php?action=editphoto na ginagamit
                       na rin ng "My Profile" (tblstudent.COMPANYIDNO, na
                       naka-sync papunta sa tblusers.PICTURE).
   - Change Password -> bagong controller.php?action=changepassword dito
                       (tblusers.PASSWORD, WHERE UID = SESSION['UID'] LANG -
                       hindi kayang baguhin ng estudyante ang password ng
                       ibang account).
   ========================================================== */
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
      <!-- ================= LEFT: Photo ================= -->
      <div class="col-md-4">
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
            <p class="text-muted text-center mb-0">Username: <?php echo htmlspecialchars(isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : '-'); ?></p>
          </div>
        </div>
      </div>

      <!-- ================= RIGHT: Change Password ================= -->
      <div class="col-md-8">
        <div class="card card-outline mb-0">
          <div class="card-header p-2"><h6 class="mb-0 pl-1"><i class="fa fa-key mr-1 text-muted"></i>Change Password</h6></div>
          <div class="card-body">
            <form action="controller.php?action=changepassword" method="POST">
              <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="CURRENT_PASSWORD" class="form-control" required autocomplete="current-password">
              </div>
              <div class="form-group">
                <label>New Password</label>
                <input type="password" name="NEW_PASSWORD" class="form-control" minlength="6" required autocomplete="new-password">
              </div>
              <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="CONFIRM_PASSWORD" class="form-control" minlength="6" required autocomplete="new-password">
              </div>
              <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Update Password</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- =================================================================
         CHANGE PROFILE PHOTO - parehong modal/controller action na
         ginagamit din ng "My Profile" (controller.php?action=editphoto).
         ================================================================= -->
    <div class="modal fade" id="sdEditPhoto">
      <div class="modal-dialog">
        <form action="controller.php?action=editphoto" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="REDIRECT_TO" value="account">
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
