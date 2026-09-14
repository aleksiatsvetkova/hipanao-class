<?php
  
  $headerUserPhoto = (isset($_SESSION['PICTURE']) && $_SESSION['PICTURE'] != '')
      ? WEB_ROOT.'module/user/'.$_SESSION['PICTURE']
      : WEB_ROOT.'module/user/images/default.png';
?>
  <nav class="main-header navbar navbar-expand navbar-light">

 <!-- HIPANAO SOLUTIONS -->

    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" data-controlsidebar-side="false" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo WEB_ROOT; ?>index.php" class="nav-link"><i class="fas fa-home"></i>&nbsp; Home</a>
      </li>
    </ul>

    <!-- Quick search : filters whichever list/table is on the current page -->
    <div class="hipanao-quicksearch d-none d-md-block ml-3">
      <i class="fas fa-search"></i>
      <input type="text" id="hipanaoQuickSearch" class="form-control form-control-sm" placeholder="Search this page...">
    </div>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

      <!-- Dark mode toggle -->
      <li class="nav-item">
        <a class="nav-link" href="#" id="hipanaoDarkModeToggle" role="button" title="Toggle dark mode">
          <i class="fas fa-moon"></i>
        </a>
      </li>

      <!-- User account -->
      <li class="nav-item dropdown">
        <a class="nav-link navbar-user-chip" data-toggle="dropdown" href="#">
          <?php echo '<img class="img-circle" src="'. $headerUserPhoto . '" alt="User Avatar">'; ?>
          <span class="d-none d-sm-flex flex-column text-left">
            <span class="chip-name"><?php echo isset($_SESSION['DISPLAYNAME']) ? htmlspecialchars($_SESSION['DISPLAYNAME']) : 'User'; ?></span>
            <span class="chip-role"><?php echo isset($_SESSION['TYPE']) ? htmlspecialchars($_SESSION['TYPE']) : 'Guest'; ?></span>
          </span>
          <i class="fas fa-angle-down ml-1 d-none d-sm-inline text-muted"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">

          <div class="card card-widget widget-user mb-0 shadow-none">
             <div class="widget-user-header" style="background-color: var(--hipanao-primary);">
                <h4 class="widget-user-username"><?php echo isset($_SESSION['DISPLAYNAME']) ? htmlspecialchars($_SESSION['DISPLAYNAME']) : 'User'; ?></h4>
                <h5 class="widget-user-desc"><?php echo isset($_SESSION['TYPE']) ? htmlspecialchars($_SESSION['TYPE']) : 'Guest'; ?></h5>
              </div>
              <div class="widget-user-image">
                <?php echo '<img class="img-circle elevation-6" src="'. $headerUserPhoto . '" alt="User Avatar">'; ?>
              </div>
              <div class="card-footer text-center">
                   <a href="<?php echo  WEB_ROOT;?>logout.php" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i>&nbsp; Logout</a>
                </div>

          </div>
        </div>

      </li>

    </ul>
  </nav>
