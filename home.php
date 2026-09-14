<?php

global $mydb;

/* HIPANAO SOLUTIONS - Student Login Accounts.
   Ang Student's own dashboard ay inilipat na sa theme/studentdashboard/
   (may sarili na itong layout, hiwalay sa Admin/Staff dashboard na ito).
   Ang index.php na ang nagde-decide kung saan dadalhin ang naka-login
   na user base sa $_SESSION['TYPE'], kaya hindi na kailangan pang
   i-check dito sa home.php.

   HIPANAO SOLUTIONS - Pinaganda ang Admin Dashboard: dagdag na mga
   card/chart, pero GAMIT LANG ANG TUNAY NA DATOS NG SISTEMA (walang
   fake na "Direct Chat", "Inventory", o e-commerce widget na wala
   namang kinalaman sa isang school enrollment system). Ang Chart.js
   rendering script mismo ay nasa index.php (PAGKATAPOS ng
   require_once theme/template.php), hindi dito - tingnan ang paliwanag
   sa module/programhead/index.php kung bakit dapat doon ito ilagay
   (script order: kailangang naka-load na ang Chart.js bago tumakbo ang
   init script na gumagamit nito). */

function home_safe_count($table) {
	global $mydb;
	if (!$mydb->tableExists($table)) { return 0; }
	$mydb->setQuery("SELECT * FROM `".$table."`");
	return $mydb->num_rows();
}

$totalAlumni  = home_safe_count('alumni_details');
$totalStudent = home_safe_count('tblstudent');
$totalCourse  = home_safe_count('tblcourses');
$totalSection = home_safe_count('tblsections');

/* ---------------- Enrollment Pipeline (status breakdown) ---------------- */
$homeStatuses = array('Register', 'Assign', 'Document', 'Paid', 'Enroll', 'Dropped', 'Completed');
$homeStatusCounts = array();
$homeTotalEnrollment = 0;
if ($mydb->tableExists('tblenrollment')) {
	foreach ($homeStatuses as $st) {
		$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE STATUS = '".$st."'");
		$homeStatusCounts[$st] = $mydb->num_rows();
		$homeTotalEnrollment += $homeStatusCounts[$st];
	}
}

/* ---------------- Monthly trend (last 6 months): New Registrations & Collections ---------------- */
$homeMonthLabels = array();
$homeMonthRegs   = array();
$homeMonthCollections = array();
for ($i = 5; $i >= 0; $i--) {
	$ts = strtotime("-".$i." months", strtotime(date('Y-m-01')));
	$ym = date('Y-m', $ts);
	$homeMonthLabels[] = date('M Y', $ts);

	$regCount = 0;
	if ($mydb->tableExists('tblenrollment')) {
		$mydb->setQuery("SELECT ENROLLMENT_ID FROM `tblenrollment` WHERE DATE_FORMAT(DATE_RESERVED, '%Y-%m') = '".$ym."'");
		$regCount = $mydb->num_rows();
	}
	$homeMonthRegs[] = $regCount;

	$collected = 0;
	if ($mydb->tableExists('tblpayments')) {
		$mydb->setQuery("SELECT SUM(AMOUNT) AS TOTAL FROM `tblpayments` WHERE DATE_FORMAT(DATE_PAID, '%Y-%m') = '".$ym."'");
		$crows = $mydb->loadResultList();
		$collected = (count($crows) >= 1 && $crows[0]->TOTAL !== null) ? floatval($crows[0]->TOTAL) : 0;
	}
	$homeMonthCollections[] = $collected;
}

/* ---------------- Quick totals (this month) ---------------- */
$homeThisMonthCollections = end($homeMonthCollections);
$homeEnrolledCount  = isset($homeStatusCounts['Enroll']) ? $homeStatusCounts['Enroll'] : 0;
$homePendingCount   = isset($homeStatusCounts['Register']) ? $homeStatusCounts['Register'] : 0;
$homeDroppedCount   = isset($homeStatusCounts['Dropped']) ? $homeStatusCounts['Dropped'] : 0;

/* ---------------- Students per Course ---------------- */
$homeCoursesBreakdown = array();
if ($mydb->tableExists('tblcourses')) {
	$mydb->setQuery("SELECT c.COURSE_CODE, c.COURSE_NAME,
			(SELECT COUNT(*) FROM tblstudent s WHERE s.COURSE_ID = c.COURSE_ID) AS STUD_COUNT
		FROM `tblcourses` c ORDER BY c.COURSE_CODE ASC");
	$homeCoursesBreakdown = $mydb->loadResultList();
}
$homeMaxCourseCount = 1;
foreach ($homeCoursesBreakdown as $hc) { if ($hc->STUD_COUNT > $homeMaxCourseCount) { $homeMaxCourseCount = $hc->STUD_COUNT; } }

/* ---------------- Recent Enrollees ---------------- */
$homeRecentEnrollees = array();
if ($mydb->tableExists('tblenrollment')) {
	$mydb->setQuery("SELECT e.ENROLLMENT_ID, e.STATUS, e.DATE_RESERVED, s.LNAME, s.FNAME, s.MNAME, s.COMPANYIDNO, c.COURSE_CODE
		FROM `tblenrollment` e
		JOIN `tblstudent` s ON s.S_ID = e.S_ID
		JOIN `tblcourses` c ON c.COURSE_ID = e.COURSE_ID
		ORDER BY e.ENROLLMENT_ID DESC
		LIMIT 6");
	$homeRecentEnrollees = $mydb->loadResultList();
}

function home_status_badge($status) {
	$cls = 'secondary';
	if ($status == 'Enroll')    { $cls = 'success'; }
	if ($status == 'Register')  { $cls = 'warning'; }
	if ($status == 'Assign')    { $cls = 'primary'; }
	if ($status == 'Document')  { $cls = 'dark'; }
	if ($status == 'Paid')      { $cls = 'info'; }
	if ($status == 'Dropped')   { $cls = 'danger'; }
	return '<span class="badge badge-'.$cls.'">'.htmlspecialchars($status).'</span>';
}
?>

<section class="content">

      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $totalAlumni; ?></h3>

                <p>Alumni Count</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=alumni_details" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3><?php echo $totalStudent; ?></h3>

                <p>Total Students</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/student/" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3><?php echo $totalCourse; ?></h3>

                <p>Total Courses</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/course/" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3><?php echo $totalSection; ?></h3>

                <p>Total Sections</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblsections" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

        <!-- ============================================================
             HIPANAO SOLUTIONS - Monthly Trend + Enrollment Pipeline
             ============================================================ -->
        <div class="row">
          <div class="col-lg-8">
            <div class="card card-outline card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chart-line mr-1"></i> New Registrations &amp; Collections (Last 6 Months)</h3>
              </div>
              <div class="card-body">
                <canvas id="homeMonthlyChart" style="min-height: 260px; height: 260px; max-height: 260px;"></canvas>
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-4 text-center border-right">
                    <span class="text-muted">This Month's Collections</span>
                    <h5 class="mb-0">&#8369;<?php echo number_format($homeThisMonthCollections, 2); ?></h5>
                  </div>
                  <div class="col-4 text-center border-right">
                    <span class="text-muted">Total Enrollment Records</span>
                    <h5 class="mb-0"><?php echo $homeTotalEnrollment; ?></h5>
                  </div>
                  <div class="col-4 text-center">
                    <span class="text-muted">Currently Enrolled</span>
                    <h5 class="mb-0"><?php echo $homeEnrolledCount; ?></h5>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="card card-outline card-secondary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-1"></i> Enrollment Pipeline</h3>
              </div>
              <div class="card-body">
                <?php foreach ($homeStatuses as $st):
                    $cnt = isset($homeStatusCounts[$st]) ? $homeStatusCounts[$st] : 0;
                    $pct = ($homeTotalEnrollment > 0) ? round(($cnt / $homeTotalEnrollment) * 100) : 0;
                    $barClass = 'bg-secondary';
                    if ($st == 'Enroll')   { $barClass = 'bg-success'; }
                    if ($st == 'Register') { $barClass = 'bg-warning'; }
                    if ($st == 'Assign')   { $barClass = 'bg-primary'; }
                    if ($st == 'Document') { $barClass = 'bg-dark'; }
                    if ($st == 'Paid')     { $barClass = 'bg-info'; }
                    if ($st == 'Dropped')  { $barClass = 'bg-danger'; }
                ?>
                <div class="mb-2">
                  <div class="d-flex justify-content-between">
                    <span><?php echo htmlspecialchars($st); ?></span>
                    <span><?php echo $cnt; ?></span>
                  </div>
                  <div class="progress progress-sm">
                    <div class="progress-bar <?php echo $barClass; ?>" style="width: <?php echo $pct; ?>%"></div>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
              <div class="card-footer text-center">
                <a href="<?php echo WEB_ROOT; ?>module/enrollment/index.php">Go to Enrollment Module <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <!-- ============================================================
             HIPANAO SOLUTIONS - Quick totals row
             ============================================================ -->
        <div class="row">
          <div class="col-lg-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-success"><i class="fas fa-user-graduate"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Currently Enrolled</span>
                <span class="info-box-number"><?php echo $homeEnrolledCount; ?></span>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-warning"><i class="fas fa-hourglass-half"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Pending Applications</span>
                <span class="info-box-number"><?php echo $homePendingCount; ?></span>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="info-box">
              <span class="info-box-icon bg-danger"><i class="fas fa-user-times"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Dropped</span>
                <span class="info-box-number"><?php echo $homeDroppedCount; ?></span>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

        <!-- ============================================================
             HIPANAO SOLUTIONS - Students per Course + Recent Enrollees
             ============================================================ -->
        <div class="row">
          <div class="col-lg-6">
            <div class="card card-outline card-warning">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-graduation-cap mr-1"></i> Students per Course</h3>
              </div>
              <div class="card-body">
                <?php if (count($homeCoursesBreakdown) < 1): ?>
                  <p class="text-muted text-center mb-0">No course records yet.</p>
                <?php else: foreach ($homeCoursesBreakdown as $hc):
                    $pct = round(($hc->STUD_COUNT / $homeMaxCourseCount) * 100);
                ?>
                  <div class="mb-2">
                    <div class="d-flex justify-content-between">
                      <span><?php echo htmlspecialchars($hc->COURSE_CODE); ?> <small class="text-muted"><?php echo htmlspecialchars($hc->COURSE_NAME); ?></small></span>
                      <span><?php echo (int)$hc->STUD_COUNT; ?></span>
                    </div>
                    <div class="progress progress-sm">
                      <div class="progress-bar bg-warning" style="width: <?php echo $pct; ?>%"></div>
                    </div>
                  </div>
                <?php endforeach; endif; ?>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="card card-outline card-info">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-clock mr-1"></i> Recent Enrollees</h3>
              </div>
              <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                  <?php if (count($homeRecentEnrollees) < 1): ?>
                    <li class="item"><p class="text-muted text-center mb-0 py-3">No enrollment records yet.</p></li>
                  <?php else: foreach ($homeRecentEnrollees as $re):
                      $picUrl = (isset($re->COMPANYIDNO) && $re->COMPANYIDNO != '')
                          ? WEB_ROOT.'module/student/image/'.$re->COMPANYIDNO
                          : WEB_ROOT.'module/student/image/1.png';
                  ?>
                  <li class="item">
                    <div class="product-img">
                      <img src="<?php echo $picUrl; ?>" alt="Student photo" class="img-size-50 img-circle" onerror="this.src='<?php echo WEB_ROOT; ?>module/student/image/1.png';">
                    </div>
                    <div class="product-info">
                      <a href="<?php echo WEB_ROOT; ?>module/enrollmentdetails/index.php" class="product-title">
                        <?php echo htmlspecialchars(trim($re->LNAME.', '.$re->FNAME.' '.$re->MNAME)); ?>
                        <span class="float-right"><?php echo home_status_badge($re->STATUS); ?></span>
                      </a>
                      <span class="product-description">
                        <?php echo htmlspecialchars($re->COURSE_CODE); ?> &middot;
                        <?php echo $re->DATE_RESERVED ? date('M d, Y', strtotime($re->DATE_RESERVED)) : '-'; ?>
                      </span>
                    </div>
                  </li>
                  <?php endforeach; endif; ?>
                </ul>
              </div>
              <div class="card-footer text-center">
                <a href="<?php echo WEB_ROOT; ?>module/enrollmentdetails/index.php">View All Students <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</section>

<!-- HIPANAO SOLUTIONS - ang datos ng chart lang ang inihahanda dito bilang
     JS variables; ang ACTUAL na Chart.js init script ay nasa index.php
     (pagkatapos ng require_once theme/template.php sa dulo ng file),
     dahil ang home.php na ito ay naka-require SA GITNA ng template.php
     (bago pa ma-load ang Chart.js sa footer). Kung ilalagay dito ang
     "new Chart(...)" tuwiran, hindi pa umiiral ang Chart.js kaya JS
     error agad - eksaktong parehong dahilan ng dating "blangkong
     DataTable" na bug sa Program Head module. -->
<script>
window.HIPANAO_HOME_CHART_DATA = {
	labels: <?php echo json_encode($homeMonthLabels); ?>,
	registrations: <?php echo json_encode($homeMonthRegs); ?>,
	collections: <?php echo json_encode($homeMonthCollections); ?>
};
</script>
