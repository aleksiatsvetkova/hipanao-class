<?php

/* HIPANAO SOLUTIONS - Program Head Dashboard content (REFERENCE ONLY,
   hindi na ginagamit - tingnan programheaddashboard.php). Kaparehong
   ideya ng theme/studentdashboard/studentdashboard_content.php - ang
   laman lang na sana makikita ng Program Head pagka-login niya (sarili
   niyang COURSE), bago pa inilipat/ginawa sa module/programhead/ ang
   tunay na dashboard (kaparehong-kapareho na ng disenyo ng Admin,
   theme/template.php). */

global $mydb;

$phCourse = null;
$phUid = isset($_SESSION['UID']) ? intval($_SESSION['UID']) : 0;

if ($phUid > 0) {
    $mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME, COURSE_DESC, STATUS
        FROM `tblcourses` WHERE PROGRAM_HEAD_ID = '".$phUid."' LIMIT 1");
    $rows = $mydb->loadResultList();
    if (count($rows) >= 1) { $phCourse = $rows[0]; }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card card-primary card-outline">
                <div class="card-body text-center">
                    <h3 class="mt-3 mb-0">Welcome, <?php echo isset($_SESSION['DISPLAYNAME']) ? htmlspecialchars($_SESSION['DISPLAYNAME']) : 'Program Head'; ?>!</h3>
                    <?php if ($phCourse): ?>
                        <p class="text-muted">Program Head - <?php echo htmlspecialchars($phCourse->COURSE_CODE); ?></p>
                        <hr>
                        <div class="row text-left">
                            <div class="col-md-6">
                                <p><strong>Course:</strong> <?php echo htmlspecialchars($phCourse->COURSE_NAME); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Status:</strong> <?php echo hipanao_badge($phCourse->STATUS); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">You are not yet assigned as Program Head of any course. Please contact the Administrator.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
