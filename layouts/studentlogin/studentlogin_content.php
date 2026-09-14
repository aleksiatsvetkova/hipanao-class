<div class="container-fluid p-0">
  <div class="row no-gutters">

    <!-- ================================================= -->
    <!-- LEFT SIDE - ABOUT THE SYSTEM (same design as the chooser) -->
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
    <!-- RIGHT SIDE - STUDENT LOGIN -->
    <!-- ================================================= -->
    <div class="col-lg-5 d-flex align-items-center justify-content-center tc-login-wrapper">
      <div class="w-100 py-5" style="max-width:420px;">

        <div class="card tc-login-card">
          <div class="card-body login-card-body" style="padding:35px 30px;">

            <div class="text-center mb-3">
              <img src="tanong.png" width="42%" style="filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2));">
            </div>

            <p class="text-center mb-2">
              <a href="login.php" style="color:#A73A34; font-weight:600; font-size:14px;">
                <i class="fas fa-arrow-left mr-1"></i>Choose a different account type
              </a>
            </p>

            <h2 class="text-center font-weight-bold" style="color:#A73A34;">
              <i class="fas fa-user-graduate mr-1"></i>Student Login
            </h2>
            <p class="text-center text-muted mb-1">Tañon College</p>
            <p class="text-center text-muted mb-4">Sign in with your Student ID to continue</p>

            <form action="login.php?type=student" method="post">

              <input type="hidden" name="logintype" value="student">

              <div class="input-group mb-3">
                <input type="text" class="form-control tc-input" name="username" placeholder="Student ID"
                       value="<?php echo htmlspecialchars($username); ?>" required>
                <div class="input-group-append">
                  <div class="input-group-text tc-input-icon"><span class="fas fa-id-card"></span></div>
                </div>
              </div>

              <div class="input-group mb-3">
                <input type="password" id="password" class="form-control tc-input" name="userpass" placeholder="Password" required>
                <div class="input-group-append">
                  <div class="input-group-text tc-input-icon">
                    <span class="fas fa-eye" id="eye" style="cursor:pointer;"></span>
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <div class="col-8">
                  <div class="icheck-primary d-inline">
                    <input type="checkbox" id="remember" name="remember" <?php echo isset($_COOKIE['hipanao_student_id']) ? "checked" : ""; ?>>
                    <label for="remember">&nbsp;Remember Me</label>
                  </div>
                </div>
                <div class="col-4 text-right">
                  <button type="submit" name="btnLogin" class="btn btn-block tc-btn-login">
                    <i class="fas fa-sign-in-alt mr-1"></i>Log in
                  </button>
                </div>
              </div>

            </form>

            <?php if (!empty($error)) { ?>
              <div class="alert alert-danger text-center py-2">
                <i class="fas fa-exclamation-circle mr-1"></i><?php echo htmlspecialchars($error); ?>
              </div>
            <?php } ?>

            <hr>

            <p class="text-center text-muted mb-2" style="font-size:14px;">
              New student? <a href="<?php echo WEB_ROOT; ?>enroll.php" style="color:#A73A34; font-weight:600;">Register here</a>
            </p>

            <a href="<?php echo WEB_ROOT; ?>login.php" class="btn btn-block" style="background:#6c757d; color:#fff; font-weight:600;">
              <i class="fas fa-arrow-left mr-2"></i>Back to Account Type Selection
            </a>

          </div>
        </div>

      </div>
    </div>

  </div>
</div>
