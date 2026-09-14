<?php

require_once("include/initialize.php");

if (isset($_SESSION['UID'])) {
    redirect_to("index.php");
}


// =====================================================
// STEP: WHICH LOGIN SCREEN TO SHOW
// =====================================================
// HIPANAO SOLUTIONS - Two-step login.
// Step 1: the visitor picks an account type (Student or Admin/Staff) -
//         no login form is shown yet - see layouts/login (chooser).
// Step 2: depending on the choice, a DIFFERENT form is shown:
//           - Student  -> layouts/studentlogin (Student ID + Password)
//           - Admin    -> layouts/adminlogin   (Username + Password)
//         Both forms still authenticate against the same `tblusers`
//         table (see include/user.php - AuthenticateUser), but each
//         screen only accepts the matching account TYPE, so a Student
//         account can't log in through the Admin form and vice versa.

$allowedTypes = array('student', 'admin');
$loginType = isset($_GET['type']) && in_array($_GET['type'], $allowedTypes) ? $_GET['type'] : '';


// =====================================================
// REMEMBERED USERNAME / STUDENT ID
// =====================================================
// Naka-hiwalay na cookie ang student at admin para hindi magkahalo ang
// "Remember Me" ng dalawang magkaibang uri ng login.

$studentIdRemembered = isset($_COOKIE['hipanao_student_id']) ? $_COOKIE['hipanao_student_id'] : "";
$adminUserRemembered = isset($_COOKIE['hipanao_admin_user']) ? $_COOKIE['hipanao_admin_user'] : "";

$username = ($loginType === 'admin') ? $adminUserRemembered : $studentIdRemembered;


// =====================================================
// LOGIN
// =====================================================

$error = "";

if (isset($_POST['btnLogin'])) {

    // logintype galing sa hidden field ng form mismo - kailangan tumugma
    // sa $loginType (galing sa URL) bago pa man mag-attempt ng login,
    // para hindi malito kung anong porma ang ipapakita ulit sa error.
    $postedType = isset($_POST['logintype']) && in_array($_POST['logintype'], $allowedTypes)
        ? $_POST['logintype']
        : '';

    if ($postedType !== '') {
        $loginType = $postedType;
    }

    $username = trim($_POST['username']);
    $upass    = trim($_POST['userpass']);

    if ($loginType === '' ) {

        $error = "Please choose an account type first.";

    } elseif ($username == '' || $upass == '') {

        $error = ($loginType === 'student')
            ? "Please enter both Student ID and password."
            : "Please enter both username and password.";

    } else {

        $h_upass = sha1($upass);
        $user    = new User();
        $res     = $user::AuthenticateUser($username, $h_upass);

        if ($res == true) {

            // =============================================
            // MAKE SURE THE ACCOUNT TYPE MATCHES THE FORM USED
            // =============================================
            // Hal. Student account na sumubok mag-login gamit ang Admin
            // form (o kabaligtaran) - i-reject at i-clear ang session na
            // na-set na ng AuthenticateUser().

            $accountIsStudent = isset($_SESSION['TYPE']) && $_SESSION['TYPE'] === 'Student';
            $typeMismatch = ($loginType === 'student' && !$accountIsStudent)
                         || ($loginType === 'admin'   &&  $accountIsStudent);

            if ($typeMismatch) {

                unset($_SESSION['UID'], $_SESSION['DISPLAYNAME'], $_SESSION['USERNAME'], $_SESSION['TYPE'], $_SESSION['PICTURE'], $_SESSION['STUDENT_SID']);

                $error = ($loginType === 'student')
                    ? "This is not a Student account. Please use the Admin/Staff login instead."
                    : "This is a Student account. Please use the Student login instead.";

            } else {

                // =============================================
                // REMEMBER ME
                // =============================================

                if ($loginType === 'student') {
                    if (isset($_POST['remember'])) {
                        setcookie("hipanao_student_id", $username, time() + (86400 * 30), "/");
                    } else {
                        setcookie("hipanao_student_id", "", time() - 3600, "/");
                    }
                } else {
                    if (isset($_POST['remember'])) {
                        setcookie("hipanao_admin_user", $username, time() + (86400 * 30), "/");
                    } else {
                        setcookie("hipanao_admin_user", "", time() - 3600, "/");
                    }
                }

                redirect_to("index.php");
            }

        } else {

            $error = ($loginType === 'student')
                ? "Invalid Student ID or password."
                : "Invalid username or password.";

        }
    }
}

?>



<?php if ($loginType === 'student') { ?>

    <?php include "layouts/studentlogin/studentlogin_header.php"; ?>
    <?php include "layouts/studentlogin/studentlogin_content.php"; ?>
    <?php include "layouts/studentlogin/studentlogin_footer.php"; ?>

<?php } elseif ($loginType === 'admin') { ?>

    <?php include "layouts/adminlogin/adminlogin_header.php"; ?>
    <?php include "layouts/adminlogin/adminlogin_content.php"; ?>
    <?php include "layouts/adminlogin/adminlogin_footer.php"; ?>

<?php } else { ?>

    <?php include "layouts/login/login_header.php"; ?>
    <?php include "layouts/login/login_content.php"; ?>
    <?php include "layouts/login/login_footer.php"; ?>

<?php } ?>
