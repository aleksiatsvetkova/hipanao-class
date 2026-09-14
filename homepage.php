<?php
/* =====================================================================
   HIPANAO SOLUTIONS - Tañon College Public Homepage
   =====================================================================
   Ito ang PUBLIC-FACING na homepage (bisita, bago pa mag-login).
   Hiwalay ito sa admin panel (home.php + theme/template.php) - sarili
   niyang HTML/CSS/JS, walang AdminLTE dependency, pero gumagamit pa
   rin ng parehong database/config (include/initialize.php) para sa
   live na Programs, Stats, at Announcements.

   PAANO ITO NAKikita ng bisita:
   index.php > kapag walang session (hindi naka-login) > ito ang
   ipinapakita sa halip na direktang i-redirect sa login.php.
   ===================================================================== */

if (!defined('WEB_ROOT')) {
	require_once(__DIR__ . "/include/initialize.php");
}
global $mydb;

/* ---------------------------------------------------------------------
   ‼ EDITABLE CONTENT - dito palitan ng Admin/Webmaster ang mga text na
   wala pang column sa database (history, mission, vision, email, social
   links, founded year). Hanapin lang ang mga TODO sa ibaba.
   --------------------------------------------------------------------- */
$SCHOOL_EMAIL        = defined('SCHOOL_EMAIL') ? SCHOOL_EMAIL : 'example@tanoncollege.edu.ph'; // TODO: palitan ng tunay na email
$SCHOOL_FOUNDED_YEAR = 1952; // Base sa "FOUNDED 1952" makikita sa school gate photo
$SCHOOL_TAGLINE      = 'Shaping minds. Building futures. Serving San Carlos City since '.$SCHOOL_FOUNDED_YEAR.'.'; // TODO
$SCHOOL_MISSION      = 'To provide accessible, quality, and values-driven higher education that equips every '.SCHOOL_NAME.' student with the knowledge, skills, and character needed to serve their community and succeed in a changing world.'; // TODO
$SCHOOL_VISION       = 'A leading institution in San Carlos City recognized for producing competent, ethical, and community-oriented graduates in Information Technology, Tourism Management, and Education.'; // TODO
$SCHOOL_HISTORY      = SCHOOL_NAME.' has been serving the youth of San Carlos City for generations, growing from a small local college into a trusted institution offering industry-relevant programs in IT, Tourism, and Education. '
	.'Over the years, the college has continuously improved its facilities, curriculum, and faculty expertise to keep pace with the changing demands of industry and society. '
	.'Guided by strong values and a commitment to accessible education, '.SCHOOL_NAME.' has produced graduates who now serve as professionals, entrepreneurs, and community leaders both locally and abroad. '
	.'Today, the college continues to welcome new generations of students, offering not just academic instruction but also a supportive community where every learner is encouraged to grow, discover their strengths, and prepare for a meaningful career.'; // TODO
$SCHOOL_ABOUT_SYSTEM = 'Welcome to the '.SCHOOL_NAME.' Student Information System, a comprehensive digital platform developed to provide a convenient, organized, and efficient way of managing and accessing important student and academic information. '
	.'The system is designed to support the daily needs of students, administrators, faculty members, and staff by bringing essential school services and information together in one secure and accessible platform.'
	."\n\n"
	.'Through this Student Information System, students can conveniently access their personal and academic records without the need for lengthy manual processes. '
	.'The platform provides access to important information such as student profiles, enrollment details, academic records, grades, class schedules, subjects, announcements, and other school-related information. '
	.'It allows students to stay updated with important activities, notices, and academic developments within the college.'
	."\n\n"
	.'For administrators and authorized school personnel, the system provides an organized way to manage student information and academic records. '
	.'It helps simplify different school processes, reduce paperwork, maintain accurate records, and make information easier to manage and retrieve. '
	.'By having centralized student data, authorized personnel can efficiently monitor and update records while maintaining proper access and security.'
	."\n\n"
	.'The system is designed with simplicity, accessibility, efficiency, and reliability in mind. '
	.'Its user-friendly interface allows users to navigate the platform easily and find the information they need without unnecessary complications. '
	.'The system also aims to minimize errors that may occur through manual record keeping while providing a more systematic approach to handling student information.'
	."\n\n"
	.'The '.SCHOOL_NAME.' Student Information System serves as an important part of the college\'s effort to embrace digital technology and improve the delivery of school services. '
	.'It provides a bridge between traditional school processes and modern digital solutions, allowing information to be accessed and managed more efficiently.'
	."\n\n"
	.'More than just a platform for storing student records, this system represents '.SCHOOL_NAME.'\'s commitment to innovation, efficiency, accessibility, and quality education. '
	.'By utilizing technology in school administration and student services, the college continues to create a more convenient and reliable environment where students and school personnel can manage academic information with greater ease.'
	."\n\n"
	.'Ultimately, the '.SCHOOL_NAME.' Student Information System is built to support the school\'s growing digital needs and to provide a dependable platform for managing student information. '
	.'Through this system, '.SCHOOL_NAME.' continues to move toward a more connected, organized, and technology-driven educational community.'; // TODO
$SOCIAL_FACEBOOK     = '#'; // TODO: link ng official Facebook Page
$SOCIAL_EMAIL_LINK   = 'mailto:'.$SCHOOL_EMAIL;

/* ---------------------------------------------------------------------
   Helper: gamitin ang na-upload na campus photo kung meron (file_exists
   check sa server), kung wala, huwag mag-print ng broken image - i-fallback
   na lang sa seal/wala nang laman ang section (walang nasisirang layout).
   --------------------------------------------------------------------- */
function hp_img_or_null($relPath) {
	$full = SITE_ROOT . DS . str_replace('/', DS, $relPath);
	return file_exists($full) ? WEB_ROOT . $relPath : null;
}

$heroImg  = hp_img_or_null('images/hero/hero-banner.jpg');
$aboutImg = hp_img_or_null('images/about-system/about-system-photo.jpg');
$historyImg = hp_img_or_null('images/about/about-photo.jpg');

$galleryImgs = array();
for ($g = 1; $g <= 6; $g++) {
	$img = hp_img_or_null('images/gallery/gallery-'.$g.'.jpg');
	if ($img) { $galleryImgs[] = $img; }
}

/* ---------------------------------------------------------------------
   Live data mula sa database - parehong tables na ginagamit na ng
   admin panel (home.php gamit ang home_safe_count, tblcourses,
   tblannouncements).
   --------------------------------------------------------------------- */
function hp_safe_count($table) {
	global $mydb;
	if (!$mydb->tableExists($table)) { return 0; }
	$mydb->setQuery("SELECT * FROM `".$table."`");
	return $mydb->num_rows();
}

$totalCourses = hp_safe_count('tblcourses');
$totalAlumni  = hp_safe_count('alumni_details');
$totalStudent = hp_safe_count('tblstudent');
$yearsServing = intval(date('Y')) - intval($SCHOOL_FOUNDED_YEAR);

$mydb->setQuery("SELECT * FROM `tblcourses` WHERE `STATUS` = 'Active' ORDER BY `COURSE_NAME` ASC");
$courses = $mydb->loadResultList();

$announcementObj = new Announcement();
$announcements = $announcementObj->allPublished();

function hp_excerpt($text, $len = 130) {
	$text = trim(strip_tags($text));
	if (strlen($text) <= $len) { return $text; }
	return substr($text, 0, $len) . '...';
}

$courseIcons = array('BSIT' => 'fa-laptop-code', 'BSTM' => 'fa-plane', 'BSED' => 'fa-chalkboard-teacher');

/* ---------------------------------------------------------------------
   LAYOUT - hinati ang HTML sa 3 parte (header/content/footer), parehong
   pattern gaya ng layouts/login, layouts/studentlogin, atbp. Lahat ng
   PHP variables/functions sa itaas ay accessible pa rin sa loob ng mga
   include file na ito (parehong scope).
   --------------------------------------------------------------------- */
?>
<?php include "layouts/homepage/homepage_header.php"; ?>
<?php include "layouts/homepage/homepage_content.php"; ?>
<?php include "layouts/homepage/homepage_footer.php"; ?>
