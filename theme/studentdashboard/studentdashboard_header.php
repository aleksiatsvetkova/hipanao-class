<?php
$sdStudentPhoto = (isset($_SESSION['PICTURE']) && $_SESSION['PICTURE'] != '')
    ? WEB_ROOT.'module/user/'.$_SESSION['PICTURE']
    : WEB_ROOT.'module/user/images/default.png';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Student Dashboard - HIPANAO SOLUTIONS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/adminlte.min.css">
  <!-- HIPANAO custom theme (AdminLTE-based) -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/hipanao-theme.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

  <style>
    html, body { height: 100%; }
    body { font-family: 'Source Sans Pro', sans-serif; background: #f4f6f9; }

    /* HIPANAO SOLUTIONS - Student Dashboard theme (theme/studentdashboard) */
    .sd-navbar {
      background: linear-gradient(135deg, #8B2E1F 0%, #A73A34 100%);
      padding: 14px 25px;
      color: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    }
    .sd-navbar .sd-brand {
      color: #fff;
      font-weight: 700;
      font-size: 18px;
      text-decoration: none;
    }
    .sd-navbar .sd-brand img { filter: drop-shadow(0 1px 3px rgba(0,0,0,0.4)); }
    .sd-navbar .sd-user-chip { color: #fff; text-decoration: none; }
    .sd-navbar .sd-user-chip:hover { color: #f1e5e4; }
    .sd-wrapper { min-height: calc(100vh - 64px); padding: 30px 15px; }
    .sd-footer {
      background: #fff;
      border-top: 1px solid #e9ecef;
      color: #6c757d;
      font-size: 13px;
      padding: 15px;
      text-align: center;
    }
  </style>
</head>
<body class="hold-transition">

<!-- ================================================= -->
<!-- TOP NAVBAR - Student Dashboard (no admin sidebar/menu) -->
<!-- ================================================= -->
<nav class="sd-navbar d-flex align-items-center justify-content-between">
  <a href="<?php echo WEB_ROOT; ?>index.php" class="sd-brand d-flex align-items-center">
    <img src="<?php echo WEB_ROOT; ?>tanong.png" width="30" class="mr-2">
    Tañon College Student Portal
  </a>
  <div class="dropdown">
    <a href="#" class="sd-user-chip dropdown-toggle" data-toggle="dropdown">
      <img src="<?php echo $sdStudentPhoto; ?>" class="img-circle" style="width:32px; height:32px; object-fit:cover; margin-right:8px;">
      <?php echo isset($_SESSION['DISPLAYNAME']) ? htmlspecialchars($_SESSION['DISPLAYNAME']) : 'Student'; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
      <a class="dropdown-item" href="<?php echo WEB_ROOT; ?>logout.php"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
    </div>
  </div>
</nav>

<div class="sd-wrapper">
