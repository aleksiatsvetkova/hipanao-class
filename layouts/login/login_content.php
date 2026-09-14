<div class="container-fluid p-0">
  <div class="row no-gutters">

    <!-- ================================================= -->
    <!-- LEFT SIDE - ABOUT THE SYSTEM -->
    <!-- ================================================= -->
    <div class="col-lg-7 tc-side">
      <div class="tc-side-content">

        <div class="mb-4">
          <img src="tanong.png" width="110" style="filter: drop-shadow(0 2px 6px rgba(0,0,0,0.5));">
        </div>

        <h1 class="font-weight-bold" style="font-size:42px; line-height:1.2; text-shadow:2px 2px 5px rgba(0,0,0,0.6);">
          Tañon College<br>Student Information &amp; Enrollment System
        </h1>

        <p class="mt-3" style="font-size:18px; line-height:1.6; max-width:800px; text-shadow:1px 1px 4px rgba(0,0,0,0.7);">
          A centralized platform built for Tañon College to manage student records,
          course offerings, enrollment, grades, and payments in one organized system
          helping the registrar and staff process transactions faster and with fewer errors.
        </p>

        <h4 class="font-weight-bold mt-5 mb-3" style="text-shadow:2px 2px 4px rgba(0,0,0,0.6);">
          System Features
        </h4>

        <div class="row">

          <div class="col-md-6 mb-3">
            <div class="tc-feature-card p-3">
              <h5 class="font-weight-bold text-dark"><i class="fas fa-user-graduate mr-2"></i>Student Management</h5>
              <p class="text-dark mb-0">Keep student profiles, records, and status organized and easy to access.</p>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <div class="tc-feature-card p-3">
              <h5 class="font-weight-bold text-dark"><i class="fas fa-clipboard-list mr-2"></i>Enrollment</h5>
              <p class="text-dark mb-0">Process student enrollment and generate enrollment forms with ease.</p>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <div class="tc-feature-card p-3">
              <h5 class="font-weight-bold text-dark"><i class="fas fa-book mr-2"></i>Courses &amp; Subjects</h5>
              <p class="text-dark mb-0">Manage course offerings, subjects, and units for every school term.</p>
            </div>
          </div>

          <div class="col-md-6 mb-3">
            <div class="tc-feature-card p-3">
              <h5 class="font-weight-bold text-dark"><i class="fas fa-graduation-cap mr-2"></i>Grades &amp; Payments</h5>
              <p class="text-dark mb-0">Record grades and track tuition payments in a single, unified view.</p>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- ================================================= -->
    <!-- RIGHT SIDE - ACCOUNT TYPE CHOOSER -->
    <!-- ================================================= -->
    <!-- HIPANAO SOLUTIONS - Step 1 of the login: walang login form dito
         pa, pinipili muna ng user kung Student ba siya o Admin/Staff.
         Depende sa kanyang pili, doon lang lalabas ang tamang form
         (layouts/studentlogin o layouts/adminlogin). -->
    <div class="col-lg-5 d-flex align-items-center justify-content-center tc-login-wrapper">
      <div class="w-100 py-5" style="max-width:420px;">

        <div class="card tc-login-card">
          <div class="card-body login-card-body" style="padding:35px 30px;">

            <div class="text-center mb-3">
              <img src="tanong.png" width="42%" style="filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2));">
            </div>

            <h2 class="text-center font-weight-bold" style="color:#A73A34;">Welcome Back</h2>
            <p class="text-center text-muted mb-1">Tañon College</p>
            <p class="text-center text-muted mb-4">Please select your account type to continue</p>

            <?php if (!empty($error)) { ?>
              <div class="alert alert-danger text-center py-2">
                <i class="fas fa-exclamation-circle mr-1"></i><?php echo htmlspecialchars($error); ?>
              </div>
            <?php } ?>

            <a href="login.php?type=student" class="tc-choice-card d-block mb-3 p-3 text-decoration-none">
              <div class="d-flex align-items-center">
                <div class="tc-choice-icon mr-3">
                  <i class="fas fa-user-graduate"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-0 font-weight-bold text-dark">Student</h5>
                  <small class="text-muted">Log in with your Student ID &amp; password</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </a>

            <a href="login.php?type=admin" class="tc-choice-card d-block mb-3 p-3 text-decoration-none">
              <div class="d-flex align-items-center">
                <div class="tc-choice-icon mr-3">
                  <i class="fas fa-user-shield"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-0 font-weight-bold text-dark">Admin / Staff</h5>
                  <small class="text-muted">Log in with your username &amp; password</small>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </a>

            <hr>

            <p class="text-center text-muted mb-2" style="font-size:14px;">
              New student? <a href="<?php echo WEB_ROOT; ?>enroll.php" style="color:#A73A34; font-weight:600;">Register here</a>
            </p>

            <a href="<?php echo WEB_ROOT; ?>index.php" class="btn btn-block" style="background:#6c757d; color:#fff; font-weight:600;">
              <i class="fas fa-arrow-left mr-2"></i>Back to Main Page
            </a>

          </div>
        </div>

      </div>
    </div>

  </div>
</div>
