<?php

/* HIPANAO SOLUTIONS - Program Head Dashboard (DEPRECATED / kept for
   backward compatibility only - kaparehong-kapareho ng ideya ng
   theme/studentdashboard/).

   Dating dito sana dadating ang sariling custom layout ng Program
   Head account (hiwalay sa AdminLTE - sarili niyang navbar, walang
   sidebar) - pero ang tunay na Program Head dashboard ay nasa
   module/programhead/ na, kaparehong-kapareho na ngayon ng
   disenyo/layout ng Admin (theme/template.php, buong AdminLTE), pero
   sarili lang niyang COURSE (dashboard/students, sections, subjects,
   grades, account) ang makikita.

   Ang file na ito ay basta na lang nagre-redirect papunta doon, sa
   kaso may lumang link/bookmark pa na direkta dito tumuturo. Ang mga
   kasamang file dito (programheaddashboard_header.php,
   programheaddashboard_content.php, programheaddashboard_footer.php)
   ay hindi na ginagamit, pero iniwan na lang bilang reference -
   parehong pattern ng theme/studentdashboard/. */

if (!isset($_SESSION['UID'])) {
    redirect(WEB_ROOT."login.php");
    exit;
}

redirect(WEB_ROOT."module/programhead/index.php");
exit;
