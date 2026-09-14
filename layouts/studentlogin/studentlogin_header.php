<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>HIPANAO SOLUTIONS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  <style>
    html, body { height: 100%; }
    body {
      font-family: 'Source Sans Pro', sans-serif;
    }
    .tc-side {
      background: linear-gradient(135deg, #8B2E1F 0%, #A73A34 100%);
      color: #fff;
      position: relative;
      min-height: 100vh;
      overflow: hidden;
    }
    .tc-side::before {
      content: "";
      position: absolute;
      top: -80px;
      right: -80px;
      width: 420px;
      height: 420px;
      background: url('tanong.png') center/contain no-repeat;
      opacity: 0.08;
      pointer-events: none;
    }
    .tc-side-content {
      position: relative;
      padding: 60px 55px;
      z-index: 1;
    }
    .tc-feature-card {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.15);
      height: 100%;
    }
    .tc-feature-card i { color: #A73A34; }
    .tc-login-wrapper {
      min-height: 100vh;
      background: #f4f6f9;
    }
    .tc-login-card {
      border: none;
      border-top: 4px solid #A73A34;
      border-radius: 12px;
      box-shadow: 0 15px 50px rgba(0,0,0,0.15);
    }
    .tc-btn-login {
      background: linear-gradient(135deg, #8B2E1F 0%, #A73A34 100%);
      color: #fff;
      font-weight: 700;
      padding: 11px 15px;
      border: none;
    }
    .tc-btn-login:hover { color: #fff; opacity: 0.92; }
    .tc-input { border: 1.5px solid #ddd; padding: 11px 15px; }
    .tc-input-icon { background-color: #f8f9fa; border: 1.5px solid #ddd; }
    /* HIPANAO SOLUTIONS - account type chooser (Step 1 of login) */
    .tc-choice-card {
      border: 1.5px solid #eee;
      border-radius: 10px;
      transition: all .15s ease-in-out;
      background: #fff;
    }
    .tc-choice-card:hover {
      border-color: #A73A34;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      transform: translateY(-2px);
    }
    .tc-choice-icon {
      width: 46px;
      height: 46px;
      min-width: 46px;
      border-radius: 50%;
      background: linear-gradient(135deg, #8B2E1F 0%, #A73A34 100%);
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }
    .tc-copyright {
      background: #fff;
      border-top: 1px solid #e9ecef;
      color: #6c757d;
      font-size: 13px;
    }
    .tc-copyright a {
      color: #A73A34;
      font-weight: 600;
      text-decoration: none;
    }
    .tc-copyright a:hover { text-decoration: underline; }
    @media (max-width: 991.98px) {
      .tc-side { min-height: auto; }
      .tc-side-content { padding: 40px 30px; }
    }
  </style>
</head>
<body class="hold-transition">
