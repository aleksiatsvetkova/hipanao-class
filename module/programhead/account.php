<?php

/* HIPANAO SOLUTIONS - Program Head Module > My Account.
   Sarili niyang login account (tblusers) mismo ito - walang hiwalay
   na "profile" table (di gaya ng Student na may tblstudent), kaya
   dito lang mismo pwedeng baguhin ang profile photo at password.
   UID ay laging galing sa SESSION (controller.php) - kaya hindi
   makakabago ang isang Program Head ng account ng iba. */
?>
<section class="content">
  <div class="container-fluid">
    <?php check_message(); ?>

    <div class="row">
      <!-- ================= LEFT: Photo ================= -->
      <div class="col-md-4">
        <div class="card card-primary card-outline">
          <div class="card-body box-profile text-center">
            <div class="hipanao-photo-upload d-inline-block" style="width:120px;">
              <img id="phProfilePhoto" src="<?php echo $phPhotoUrl; ?>"
                   class="profile-user-img img-fluid img-circle"
                   style="width:120px; height:120px; object-fit:cover;"
                   onerror="this.src='<?php echo WEB_ROOT; ?>module/user/images/default.png'"
                   alt="Profile photo">
            </div>
            <br>
            <button type="button" class="btn btn-outline-primary btn-sm mt-2" data-toggle="modal" data-target="#phEditPhoto">
              <i class="fa fa-camera mr-1"></i> Change Photo
            </button>
            <h3 class="profile-username text-center mt-2 mb-0"><?php echo htmlspecialchars(isset($_SESSION['DISPLAYNAME']) ? $_SESSION['DISPLAYNAME'] : '-'); ?></h3>
            <p class="text-muted text-center mb-0">Username: <?php echo htmlspecialchars(isset($_SESSION['USERNAME']) ? $_SESSION['USERNAME'] : '-'); ?></p>
            <p class="text-muted text-center mb-0">Program Head<?php echo $phCourse ? ' - '.htmlspecialchars($phCourse->COURSE_CODE) : ''; ?></p>
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
         CHANGE PROFILE PHOTO - tblusers.PICTURE (WHERE UID = SESSION UID)
         ================================================================= -->
    <div class="modal fade" id="phEditPhoto">
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
                <img id="phImgUploadPreview" src="<?php echo $phPhotoUrl; ?>"
                     style="width:120px; height:120px; object-fit:cover; border-radius:50%;"
                     onerror="this.src='<?php echo WEB_ROOT; ?>module/user/images/default.png'"
                     alt="Photo preview">
                <div class="custom-file mt-2">
                  <input type="file" name="PICTURE" id="phPicture" class="custom-file-input ph-photo-input" data-preview="#phImgUploadPreview" accept="image/*" required>
                  <label class="custom-file-label" for="phPicture">Choose photo...</label>
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

  </div>
</section>
