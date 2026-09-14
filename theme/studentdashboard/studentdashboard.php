<?php

/* HIPANAO SOLUTIONS - Student Dashboard (DEPRECATED / kept for backward
   compatibility only).

   Dating dito ang sariling custom layout ng Student account (hiwalay
   sa AdminLTE - sarili niyang navbar, walang sidebar). Inilipat na ang
   Student dashboard sa module/studentmodule/ - kaparehong-kapareho na
   ngayon ng disenyo/layout ng Admin (theme/template.php, buong
   AdminLTE), pero sarili lang niyang impormasyon (My Profile, My
   Section, My Grades, Enrollment History, My Payments) ang makikita.

   Ang file na ito ay basta na lang nagre-redirect papunta doon, sa
   kaso may lumang link/bookmark pa na direkta dito tumuturo. Ang mga
   kasamang file dito (studentdashboard_header.php,
   studentdashboard_content.php, studentdashboard_footer.php) ay hindi
   na ginagamit, pero iniwan na lang bilang reference. */

if (!isset($_SESSION['UID'])) {
    redirect(WEB_ROOT."login.php");
    exit;
}

redirect(WEB_ROOT."module/studentmodule/index.php");
exit;
