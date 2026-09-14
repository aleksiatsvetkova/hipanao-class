<?php
require_once('include/initialize.php');

$success_message = '';
$error_message = '';
$registration_success = false;

// Kept across a failed submit so the form can be redisplayed with whatever
// the applicant already typed - HIPANAO SOLUTIONS. Wala nang kailangang
// i-type ulit ang lahat kapag may isa lang na field na mali/kulang.
$old = array();
// Per-field validation errors: $errors['FIELD'] = 'message'. Ginagamit ito
// para i-highlight lang yung mga field na may problema, hindi buong form.
$errors = array();

$citizenshipChoices = array('Filipino', 'Chinese', 'American', 'Korean', 'Japanese', 'Indian', 'Other');
$religions = array('Roman Catholic', 'Protestant', 'Islam', 'Buddhist', 'Hindu', 'Judaism', 'Agnostic', 'Atheist', 'Other');
$civilStatuses = array('Single', 'Married', 'Widowed', 'Separated');

function reg_val($old, $field, $default = '') {
    return isset($old[$field]) ? htmlspecialchars($old[$field]) : htmlspecialchars($default);
}
function reg_err($errors, $field) {
    return isset($errors[$field]) ? ' is-invalid' : '';
}
function reg_feedback($errors, $field) {
    if (isset($errors[$field])) {
        echo '<div class="invalid-feedback">' . htmlspecialchars($errors[$field]) . '</div>';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    global $mydb;

    // Remember everything the applicant typed, in case may kulang o mali.
    $old = $_POST;

    // ---------------------------------------------------------------
    // VALIDATION - per-field lang, hindi lahat nade-delete kapag may
    // error. ID No. at LRN No. ay HINDI na required (assigned/verified
    // na lang ng Registrar's Office pagdating dito).
    // ---------------------------------------------------------------

    // ID No. - optional. Kung binigyan, siguraduhing hindi duplicate.
    $idno = trim((string)($_POST['IDNO'] ?? ''));
    if ($idno !== '') {
        $mydb->setQuery("SELECT S_ID FROM `tblstudent` WHERE IDNO = '" . $mydb->escape_value($idno) . "'");
        if ($mydb->num_rows() > 0) {
            $errors['IDNO'] = "This Student ID already exists. Please use a different ID, or leave it blank.";
        }
    }

    // LRN No. - optional, but if provided dapat 12 digits.
    $lrnno = trim((string)($_POST['LRNNO'] ?? ''));
    if ($lrnno !== '' && (!ctype_digit($lrnno) || strlen($lrnno) != 12)) {
        $errors['LRNNO'] = "LRN must be exactly 12 digits if provided.";
    }

    if (trim((string)($_POST['LNAME'] ?? '')) === '') $errors['LNAME'] = "Last Name is required.";
    if (trim((string)($_POST['FNAME'] ?? '')) === '') $errors['FNAME'] = "First Name is required.";
    if (trim((string)($_POST['MNAME'] ?? '')) === '') $errors['MNAME'] = "Middle Name is required.";

    if (empty($_POST['SEX'])) {
        $errors['SEX'] = "Please select a gender.";
    }

    if (empty($_POST['BDAY'])) {
        $errors['BDAY'] = "Birthday is required.";
    } else {
        $birthdate = strtotime($_POST['BDAY']);
        if ($birthdate === false || $birthdate >= strtotime('today')) {
            $errors['BDAY'] = "Birthday must be a valid date in the past.";
        }
    }

    if (trim((string)($_POST['BPLACE'] ?? '')) === '') $errors['BPLACE'] = "Birth Place is required.";

    // Citizenship/Nationality - dropdown na ngayon (dati free-text).
    $nationality = isset($_POST['NATIONALITY']) ? trim($_POST['NATIONALITY']) : '';
    if ($nationality === '') {
        $errors['NATIONALITY'] = "Please select your citizenship.";
    } elseif ($nationality === 'Other') {
        $nationalityOther = trim((string)($_POST['NATIONALITY_OTHER'] ?? ''));
        if ($nationalityOther === '') {
            $errors['NATIONALITY_OTHER'] = "Please specify your citizenship.";
        } else {
            $nationality = $nationalityOther;
        }
    }

    if (empty($_POST['RELIGION'])) $errors['RELIGION'] = "Please select a religion.";
    if (empty($_POST['COURSE_ID'])) $errors['COURSE_ID'] = "Please select a course.";

    $contactNo = trim((string)($_POST['CONTACT_NO'] ?? ''));
    if ($contactNo === '') {
        $errors['CONTACT_NO'] = "Contact No. is required.";
    } elseif (!preg_match('/^[0-9\-\+\s()]{7,}$/', $contactNo)) {
        $errors['CONTACT_NO'] = "Please enter a valid contact number.";
    }

    $email = trim((string)($_POST['EMAIL'] ?? ''));
    if ($email === '') {
        $errors['EMAIL'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['EMAIL'] = "Please enter a valid email address.";
    }

    if (trim((string)($_POST['CONTACTPERSON'] ?? '')) === '') $errors['CONTACTPERSON'] = "Contact Person is required.";
    if (trim((string)($_POST['HOME_ADD'] ?? '')) === '') $errors['HOME_ADD'] = "Home Address is required.";

    // Portal Password - HIPANAO SOLUTIONS. Dating optional ito, pero
    // kailangan na talaga itong i-set dito mismo dahil ito na ang
    // gagamitin niya pang-login sa Student Portal (tingnan ang
    // "Remember your password" note sa ibaba ng form).
    $accPasswordRaw = (string)($_POST['ACC_PASSWORD'] ?? '');
    $accPasswordConfirmRaw = (string)($_POST['ACC_PASSWORD_CONFIRM'] ?? '');
    if ($accPasswordRaw === '') {
        $errors['ACC_PASSWORD'] = "Please create a password for your Student Portal account.";
    } elseif (strlen($accPasswordRaw) < 6) {
        $errors['ACC_PASSWORD'] = "Password must be at least 6 characters.";
    } elseif ($accPasswordRaw !== $accPasswordConfirmRaw) {
        $errors['ACC_PASSWORD_CONFIRM'] = "Passwords do not match.";
    }

    // Guardian's Information fields - optional, walang mahigpit na
    // validation maliban sa email format kung meron.
    foreach (array('FATHER_EMAIL', 'MOTHER_EMAIL', 'GUARDIAN_EMAIL') as $optEmailField) {
        $val = trim((string)($_POST[$optEmailField] ?? ''));
        if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) {
            $errors[$optEmailField] = "Please enter a valid email address.";
        }
    }

    if (count($errors) > 0) {
        // Tagalog/English note - hindi na kailangang i-type ulit ang lahat,
        // ang mga naka-highlight lang sa ibaba ang kailangang ayusin.
        $error_message = "Please review the highlighted field(s) below. "
                        . "(May kulang o maling detalye sa mga naka-highlight na field sa ibaba - "
                        . "hindi na kailangang punan ulit ang buong form.)";
    } else {
        try {
            $photo_file = '';
            if (isset($_FILES['PICTURE']) && $_FILES['PICTURE']['error'] == 0) {
                $allowed = array('jpg', 'jpeg', 'png', 'gif');
                $filename = $_FILES['PICTURE']['name'];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if (in_array($ext, $allowed)) {
                    $upload_dir = 'module/student/image/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

                    $file_name = 'student_' . date('YmdHis') . '_' . rand(1000, 9999) . '.' . $ext;

                    if (move_uploaded_file($_FILES['PICTURE']['tmp_name'], $upload_dir . $file_name)) {
                        $photo_file = $file_name;
                    }
                }
            }

            // Calculate age
            $birthdate = strtotime($_POST['BDAY']);
            $age = date('Y') - date('Y', $birthdate);
            if (date('md', $birthdate) > date('md')) $age--;

            // Portal Password - required na, validated na sa itaas na
            // hindi ito blangko - i-hash na lang (parehong paraan ng
            // pag-hash ng tblusers.PASSWORD sa login).
            $acc_password = sha1($_POST['ACC_PASSWORD']);

            // ID No. is NOT NULL in `tblstudent`, pero optional na sa form -
            // kung walang binigay, mag-a-auto-generate ng pansamantalang ID
            // na maaaring palitan ng Registrar's Office sa module/student.
            if ($idno === '') {
                // Tiyakin talagang unique ang auto-generated ID - subukan
                // ulit hanggang sa makahanap ng hindi pa ginagamit
                // (napakababa ng tsansang magkolisyon, pero siguraduhin
                // pa rin - HIPANAO SOLUTIONS).
                do {
                    $idno = 'APP-' . date('Ymd') . '-' . rand(1000, 9999);
                    $mydb->setQuery("SELECT S_ID FROM `tblstudent` WHERE IDNO = '" . $mydb->escape_value($idno) . "'");
                    $idnoTaken = ($mydb->num_rows() > 0);
                } while ($idnoTaken);
            }

            $sql = "INSERT INTO `tblstudent`
                    (IDNO, LRNNO, LNAME, FNAME, MNAME, SEX, BDAY, BPLACE, STATUS, AGE, NATIONALITY, RELIGION,
                     CONTACT_NO, HOME_ADD, EMAIL, CONTACTPERSON, COURSE_ID, COMPANYIDNO, ACC_PASSWORD,
                     CIVIL_STATUS, FATHER_NAME, FATHER_CONTACT, FATHER_EMAIL, FATHER_OCCUPATION, FATHER_DECEASED,
                     MOTHER_NAME, MOTHER_CONTACT, MOTHER_EMAIL, MOTHER_OCCUPATION, MOTHER_DECEASED,
                     GUARDIAN_NAME, GUARDIAN_RELATIONSHIP, GUARDIAN_CONTACT, GUARDIAN_EMAIL, GUARDIAN_ADDRESS,
                     OTHER_PERSON_SUPPORTING, IS_BOARDING, WITH_FAMILY, BOARDING_ADDRESS,
                     ELEM_SCHOOL, ELEM_ADDRESS, ELEM_YEAR, SEC_SCHOOL, SEC_ADDRESS, SEC_YEAR,
                     COLLEGE_SCHOOL, COLLEGE_ADDRESS, COLLEGE_YEAR, VOC_SCHOOL, VOC_ADDRESS, VOC_YEAR, OTHERS_SCHOOL)
                    VALUES
                    ('" . $mydb->escape_value($idno) . "',
                     '" . $mydb->escape_value($lrnno) . "',
                     '" . $mydb->escape_value(trim($_POST['LNAME'])) . "',
                     '" . $mydb->escape_value(trim($_POST['FNAME'])) . "',
                     '" . $mydb->escape_value(trim($_POST['MNAME'])) . "',
                     '" . $mydb->escape_value($_POST['SEX']) . "',
                     '" . $mydb->escape_value($_POST['BDAY']) . "',
                     '" . $mydb->escape_value(trim($_POST['BPLACE'])) . "',
                     'Active',
                     " . intval($age) . ",
                     '" . $mydb->escape_value($nationality) . "',
                     '" . $mydb->escape_value($_POST['RELIGION']) . "',
                     '" . $mydb->escape_value($contactNo) . "',
                     '" . $mydb->escape_value(trim($_POST['HOME_ADD'])) . "',
                     '" . $mydb->escape_value($email) . "',
                     '" . $mydb->escape_value(trim($_POST['CONTACTPERSON'])) . "',
                     '" . intval($_POST['COURSE_ID']) . "',
                     '" . $mydb->escape_value($photo_file) . "',
                     '" . $mydb->escape_value($acc_password) . "',
                     '" . $mydb->escape_value(!empty($_POST['CIVIL_STATUS']) ? $_POST['CIVIL_STATUS'] : 'Single') . "',
                     '" . $mydb->escape_value(trim((string)($_POST['FATHER_NAME'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['FATHER_CONTACT'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['FATHER_EMAIL'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['FATHER_OCCUPATION'] ?? ''))) . "',
                     '" . $mydb->escape_value(!empty($_POST['FATHER_DECEASED']) ? $_POST['FATHER_DECEASED'] : 'No') . "',
                     '" . $mydb->escape_value(trim((string)($_POST['MOTHER_NAME'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['MOTHER_CONTACT'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['MOTHER_EMAIL'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['MOTHER_OCCUPATION'] ?? ''))) . "',
                     '" . $mydb->escape_value(!empty($_POST['MOTHER_DECEASED']) ? $_POST['MOTHER_DECEASED'] : 'No') . "',
                     '" . $mydb->escape_value(trim((string)($_POST['GUARDIAN_NAME'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['GUARDIAN_RELATIONSHIP'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['GUARDIAN_CONTACT'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['GUARDIAN_EMAIL'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['GUARDIAN_ADDRESS'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['OTHER_PERSON_SUPPORTING'] ?? ''))) . "',
                     '" . $mydb->escape_value(!empty($_POST['IS_BOARDING']) ? $_POST['IS_BOARDING'] : 'No') . "',
                     '" . $mydb->escape_value(!empty($_POST['WITH_FAMILY']) ? $_POST['WITH_FAMILY'] : 'Yes') . "',
                     '" . $mydb->escape_value(trim((string)($_POST['BOARDING_ADDRESS'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['ELEM_SCHOOL'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['ELEM_ADDRESS'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['ELEM_YEAR'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['SEC_SCHOOL'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['SEC_ADDRESS'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['SEC_YEAR'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['COLLEGE_SCHOOL'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['COLLEGE_ADDRESS'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['COLLEGE_YEAR'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['VOC_SCHOOL'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['VOC_ADDRESS'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['VOC_YEAR'] ?? ''))) . "',
                     '" . $mydb->escape_value(trim((string)($_POST['OTHERS_SCHOOL'] ?? ''))) . "')";

            $mydb->setQuery($sql);
            $student_id = $mydb->insert_id();

            /* HIPANAO SOLUTIONS - BUG FIX: dating gumagawa rin ito ng
               tblenrollment record (STATUS='Register') kaagad-agad pagka-
               register. Dahil dito, kapag ginamit ng Registrar's Office
               ang "Register Enrollment Slot" (module/student) para sa
               parehong estudyante, nagkakabanggaan sila - lumalabas ang
               error na "This student already has a record for that
               academic year and semester" dahil mayroon nang existing
               na row mula sa registration mismo.

               Ang self-registration form na ito ay dapat GUMAWA LANG NG
               STUDENT RECORD. Ang aktwal na Enrollment record (kasama
               ang School Year, Semester, at Subjects) ay ginagawa na
               lang ng Registrar's Office gamit ang "Register Enrollment
               Slot" sa module/student, tapos Subjects (Assign) sa
               module/enrollment, bago ang Payment - hindi na dito sa
               register.php. */

            $success_message = "Registration Successful! Your Student ID is: <strong>" . htmlspecialchars($idno) . "</strong>";
            $registration_success = true;
        } catch (Exception $e) {
            $error_message = "An error occurred: " . $e->getMessage();
        }
    }
}

// Get active courses
$courses = array();
$mydb->setQuery("SELECT COURSE_ID, COURSE_CODE, COURSE_NAME FROM `tblcourses` WHERE STATUS = 'Active' ORDER BY COURSE_CODE ASC");
foreach ($mydb->loadResultList() as $c) {
    $courses[] = $c;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment - Tañon College</title>
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/adminlte.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #8B2E1F 0%, #A73A34 100%);
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #8B2E1F 0%, #A73A34 100%);
            color: white;
            padding: 30px 25px;
            text-align: center;
        }

        .register-header img {
            max-width: 85px;
            margin-bottom: 12px;
            filter: drop-shadow(0 2px 5px rgba(0,0,0,0.2));
        }

        .register-header h2 {
            margin: 0;
            font-weight: 700;
            font-size: 24px;
        }

        .register-header p {
            margin: 8px 0 0 0;
            font-size: 13px;
            opacity: 0.95;
        }

        .register-body {
            padding: 30px;
        }

        .form-section-title {
            color: #8B2E1F;
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 3px solid #8B2E1F;
            font-size: 15px;
        }

        .form-section-title:first-child {
            margin-top: 0;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
            font-size: 13px;
        }

        .form-control {
            border: 1.5px solid #ddd;
            padding: 9px 12px;
            border-radius: 5px;
            font-size: 13px;
        }

        .form-control:focus {
            border-color: #8B2E1F;
            box-shadow: 0 0 0 0.2rem rgba(139, 46, 31, 0.25);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .invalid-feedback {
            display: block;
            font-size: 12px;
        }

        .required-marker {
            color: #e74c3c;
            font-weight: bold;
        }

        .optional-marker {
            color: #999;
            font-weight: 400;
            font-size: 11px;
        }

        .photo-section {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .photo-upload img {
            max-width: 130px;
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #8B2E1F;
            margin-bottom: 12px;
            box-shadow: 0 5px 15px rgba(139, 46, 31, 0.3);
        }

        .btn-register {
            background: linear-gradient(135deg, #8B2E1F 0%, #A73A34 100%);
            border: none;
            padding: 12px 30px;
            font-weight: 700;
            width: 100%;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 15px;
            color: white;
        }

        .btn-register:hover {
            opacity: 0.9;
            color: white;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 25px;
            border: none;
            font-size: 14px;
            line-height: 1.6;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px 20px;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-left: 4px solid #dc3545;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #8B2E1F;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 12px;
        }

        .row {
            margin-left: -5px;
            margin-right: -5px;
        }

        .col-md-4, .col-md-6, .col-md-12 {
            padding-left: 5px;
            padding-right: 5px;
        }

        .custom-file-label {
            color: #666;
            font-size: 13px;
        }

        .custom-file-input {
            font-size: 13px;
        }

        /* Reg-form tabs, para hindi sobrang haba ang pahina - HIPANAO SOLUTIONS */
        .reg-nav-tabs {
            border-bottom: 2px solid #eee;
            margin-bottom: 18px;
        }
        .reg-nav-tabs .nav-link {
            font-weight: 600;
            font-size: 13px;
            color: #8B2E1F;
        }
        .reg-nav-tabs .nav-link.active {
            color: #8B2E1F;
            border-color: #eee #eee #fff;
            border-bottom: 3px solid #8B2E1F;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <img src="tanong.png" alt="Tañon College Logo">
            <h2><i class="fas fa-graduation-cap mr-2"></i>Student Registration</h2>
            <p>Enroll Now - Complete Your Registration</p>
        </div>

        <div class="register-body">
            <?php if (!empty($success_message) && $registration_success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success_message; ?>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <hr>
                    <small><strong>Next Steps:</strong></small>
                    <ul style="margin-bottom: 0;">
                        <li>Proceed to the Registrar's Office for verification</li>
                        <li>Complete payment of tuition fees</li>
                        <li>Wait for subject assignment</li>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message) && !$registration_success): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle mr-2"></i><strong>Registration Failed!</strong>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <div style="margin-top: 10px;">
                        <?php echo $error_message; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$registration_success): ?>
            <form method="POST" enctype="multipart/form-data" id="registrationForm" novalidate>
                <input type="hidden" name="action" value="register">

                <!-- Photo Upload -->
                <div class="photo-section">
                    <div class="photo-upload">
                        <img id="img-preview" src="<?php echo WEB_ROOT; ?>module/student/image/1.png" alt="Profile Photo">
                        <div class="custom-file">
                            <input type="file" name="PICTURE" id="PICTURE" class="custom-file-input" accept="image/*">
                            <label class="custom-file-label" for="PICTURE">Choose photo...</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="IDNO">ID No. <span class="optional-marker">(optional - assigned by Registrar if left blank)</span></label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'IDNO'); ?>" name="IDNO" id="IDNO" value="<?php echo reg_val($old, 'IDNO'); ?>">
                            <?php reg_feedback($errors, 'IDNO'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="LRNNO">LRN No. <span class="optional-marker">(optional)</span></label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'LRNNO'); ?>" name="LRNNO" id="LRNNO" value="<?php echo reg_val($old, 'LRNNO'); ?>">
                            <?php reg_feedback($errors, 'LRNNO'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="LNAME">Last Name <span class="required-marker">*</span></label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'LNAME'); ?>" name="LNAME" id="LNAME" value="<?php echo reg_val($old, 'LNAME'); ?>">
                            <?php reg_feedback($errors, 'LNAME'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="FNAME">First Name <span class="required-marker">*</span></label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'FNAME'); ?>" name="FNAME" id="FNAME" value="<?php echo reg_val($old, 'FNAME'); ?>">
                            <?php reg_feedback($errors, 'FNAME'); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="MNAME">Middle Name <span class="required-marker">*</span></label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'MNAME'); ?>" name="MNAME" id="MNAME" value="<?php echo reg_val($old, 'MNAME'); ?>">
                            <?php reg_feedback($errors, 'MNAME'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="SEX">Gender <span class="required-marker">*</span></label>
                            <select class="form-control<?php echo reg_err($errors, 'SEX'); ?>" name="SEX" id="SEX">
                                <option value="">-- Select --</option>
                                <option value="Male" <?php echo (($old['SEX'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo (($old['SEX'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                            </select>
                            <?php reg_feedback($errors, 'SEX'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="BDAY">Birthday <span class="required-marker">*</span></label>
                            <input type="date" class="form-control<?php echo reg_err($errors, 'BDAY'); ?>" name="BDAY" id="BDAY" value="<?php echo reg_val($old, 'BDAY'); ?>">
                            <?php reg_feedback($errors, 'BDAY'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="BPLACE">Birth Place <span class="required-marker">*</span></label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'BPLACE'); ?>" name="BPLACE" id="BPLACE" value="<?php echo reg_val($old, 'BPLACE'); ?>">
                            <?php reg_feedback($errors, 'BPLACE'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="NATIONALITY">Citizenship <span class="required-marker">*</span></label>
                            <select class="form-control<?php echo reg_err($errors, 'NATIONALITY'); ?>" name="NATIONALITY" id="NATIONALITY" onchange="document.getElementById('nationalityOtherWrap').style.display = (this.value === 'Other') ? 'block' : 'none';">
                                <option value="">-- Select Citizenship --</option>
                                <?php foreach ($citizenshipChoices as $c): ?>
                                    <option value="<?php echo htmlspecialchars($c); ?>" <?php echo (($old['NATIONALITY'] ?? '') === $c) ? 'selected' : ''; ?>><?php echo htmlspecialchars($c); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php reg_feedback($errors, 'NATIONALITY'); ?>
                        </div>
                        <div class="form-group" id="nationalityOtherWrap" style="display:<?php echo (($old['NATIONALITY'] ?? '') === 'Other') ? 'block' : 'none'; ?>;">
                            <label for="NATIONALITY_OTHER">Please specify</label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'NATIONALITY_OTHER'); ?>" name="NATIONALITY_OTHER" id="NATIONALITY_OTHER" value="<?php echo reg_val($old, 'NATIONALITY_OTHER'); ?>">
                            <?php reg_feedback($errors, 'NATIONALITY_OTHER'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="RELIGION">Religion <span class="required-marker">*</span></label>
                            <select class="form-control<?php echo reg_err($errors, 'RELIGION'); ?>" name="RELIGION" id="RELIGION">
                                <option value="">-- Select Religion --</option>
                                <?php foreach ($religions as $rel): ?>
                                    <option value="<?php echo htmlspecialchars($rel); ?>" <?php echo (($old['RELIGION'] ?? '') === $rel) ? 'selected' : ''; ?>><?php echo htmlspecialchars($rel); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php reg_feedback($errors, 'RELIGION'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="CIVIL_STATUS">Civil Status</label>
                            <select class="form-control" name="CIVIL_STATUS" id="CIVIL_STATUS">
                                <?php foreach ($civilStatuses as $cs): ?>
                                    <option value="<?php echo htmlspecialchars($cs); ?>" <?php echo (($old['CIVIL_STATUS'] ?? 'Single') === $cs) ? 'selected' : ''; ?>><?php echo htmlspecialchars($cs); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="COURSE_ID">Course <span class="required-marker">*</span></label>
                            <select class="form-control<?php echo reg_err($errors, 'COURSE_ID'); ?>" name="COURSE_ID" id="COURSE_ID">
                                <option value="">-- Select Course --</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course->COURSE_ID; ?>" <?php echo ((string)($old['COURSE_ID'] ?? '') === (string)$course->COURSE_ID) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($course->COURSE_CODE . ' - ' . $course->COURSE_NAME); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php reg_feedback($errors, 'COURSE_ID'); ?>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="form-section-title">
                    <i class="fas fa-phone mr-2"></i>Contact Information
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="CONTACT_NO">Contact No. <span class="required-marker">*</span></label>
                            <input type="tel" class="form-control<?php echo reg_err($errors, 'CONTACT_NO'); ?>" name="CONTACT_NO" id="CONTACT_NO" autocomplete="tel" value="<?php echo reg_val($old, 'CONTACT_NO'); ?>">
                            <?php reg_feedback($errors, 'CONTACT_NO'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="EMAIL">Email <span class="required-marker">*</span></label>
                            <input type="email" class="form-control<?php echo reg_err($errors, 'EMAIL'); ?>" name="EMAIL" id="EMAIL" autocomplete="email" value="<?php echo reg_val($old, 'EMAIL'); ?>">
                            <?php reg_feedback($errors, 'EMAIL'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="CONTACTPERSON">Contact Person <span class="required-marker">*</span></label>
                            <input type="text" class="form-control<?php echo reg_err($errors, 'CONTACTPERSON'); ?>" name="CONTACTPERSON" id="CONTACTPERSON" autocomplete="off" value="<?php echo reg_val($old, 'CONTACTPERSON'); ?>">
                            <?php reg_feedback($errors, 'CONTACTPERSON'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="HOME_ADD">Home Address <span class="required-marker">*</span></label>
                            <textarea class="form-control<?php echo reg_err($errors, 'HOME_ADD'); ?>" name="HOME_ADD" id="HOME_ADD" rows="2"><?php echo reg_val($old, 'HOME_ADD'); ?></textarea>
                            <?php reg_feedback($errors, 'HOME_ADD'); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ACC_PASSWORD">Portal Password <span class="required-marker">*</span></label>
                            <input type="password" class="form-control<?php echo reg_err($errors, 'ACC_PASSWORD'); ?>" name="ACC_PASSWORD" id="ACC_PASSWORD" placeholder="At least 6 characters" required minlength="6">
                            <?php reg_feedback($errors, 'ACC_PASSWORD'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ACC_PASSWORD_CONFIRM">Confirm Password <span class="required-marker">*</span></label>
                            <input type="password" class="form-control<?php echo reg_err($errors, 'ACC_PASSWORD_CONFIRM'); ?>" name="ACC_PASSWORD_CONFIRM" id="ACC_PASSWORD_CONFIRM" placeholder="Re-type your password" required minlength="6">
                            <?php reg_feedback($errors, 'ACC_PASSWORD_CONFIRM'); ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="alert alert-warning py-2 mb-0" style="font-size: 14px;">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Please remember this password</strong> - you will use it, together with your
                            Student ID, to log in to the Student Portal once your account is activated.
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     PARENTS' / GUARDIAN'S INFORMATION - HIPANAO SOLUTIONS.
                     Kaparehong laman ng module/student/add form, para kumpleto
                     rin ang datos kapag mula sa public self-registration.
                     ============================================================ -->
                <div class="form-section-title">
                    <i class="fas fa-users mr-2"></i>Parents' / Guardian's Information
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="FATHER_NAME">Father's Name</label>
                            <input type="text" class="form-control" name="FATHER_NAME" id="FATHER_NAME" value="<?php echo reg_val($old, 'FATHER_NAME'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="FATHER_CONTACT">Father's Contact No.</label>
                            <input type="text" class="form-control" name="FATHER_CONTACT" id="FATHER_CONTACT" autocomplete="off" value="<?php echo reg_val($old, 'FATHER_CONTACT'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="FATHER_EMAIL">Father's Email</label>
                            <input type="email" class="form-control<?php echo reg_err($errors, 'FATHER_EMAIL'); ?>" name="FATHER_EMAIL" id="FATHER_EMAIL" autocomplete="off" value="<?php echo reg_val($old, 'FATHER_EMAIL'); ?>">
                            <?php reg_feedback($errors, 'FATHER_EMAIL'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="FATHER_OCCUPATION">Father's Occupation</label>
                            <input type="text" class="form-control" name="FATHER_OCCUPATION" id="FATHER_OCCUPATION" value="<?php echo reg_val($old, 'FATHER_OCCUPATION'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="FATHER_DECEASED">Father Deceased?</label>
                            <select class="form-control" name="FATHER_DECEASED" id="FATHER_DECEASED">
                                <option value="No" <?php echo (($old['FATHER_DECEASED'] ?? 'No') === 'No') ? 'selected' : ''; ?>>No</option>
                                <option value="Yes" <?php echo (($old['FATHER_DECEASED'] ?? '') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="MOTHER_NAME">Mother's Maiden Name</label>
                            <input type="text" class="form-control" name="MOTHER_NAME" id="MOTHER_NAME" value="<?php echo reg_val($old, 'MOTHER_NAME'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="MOTHER_CONTACT">Mother's Contact No.</label>
                            <input type="text" class="form-control" name="MOTHER_CONTACT" id="MOTHER_CONTACT" autocomplete="off" value="<?php echo reg_val($old, 'MOTHER_CONTACT'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="MOTHER_EMAIL">Mother's Email</label>
                            <input type="email" class="form-control<?php echo reg_err($errors, 'MOTHER_EMAIL'); ?>" name="MOTHER_EMAIL" id="MOTHER_EMAIL" autocomplete="off" value="<?php echo reg_val($old, 'MOTHER_EMAIL'); ?>">
                            <?php reg_feedback($errors, 'MOTHER_EMAIL'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="MOTHER_OCCUPATION">Mother's Occupation</label>
                            <input type="text" class="form-control" name="MOTHER_OCCUPATION" id="MOTHER_OCCUPATION" value="<?php echo reg_val($old, 'MOTHER_OCCUPATION'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="MOTHER_DECEASED">Mother Deceased?</label>
                            <select class="form-control" name="MOTHER_DECEASED" id="MOTHER_DECEASED">
                                <option value="No" <?php echo (($old['MOTHER_DECEASED'] ?? 'No') === 'No') ? 'selected' : ''; ?>>No</option>
                                <option value="Yes" <?php echo (($old['MOTHER_DECEASED'] ?? '') === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="GUARDIAN_NAME">Guardian's Name</label>
                            <input type="text" class="form-control" name="GUARDIAN_NAME" id="GUARDIAN_NAME" value="<?php echo reg_val($old, 'GUARDIAN_NAME'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="GUARDIAN_RELATIONSHIP">Relationship</label>
                            <input type="text" class="form-control" name="GUARDIAN_RELATIONSHIP" id="GUARDIAN_RELATIONSHIP" placeholder="e.g. Brother, Aunt" value="<?php echo reg_val($old, 'GUARDIAN_RELATIONSHIP'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="GUARDIAN_CONTACT">Guardian's Contact No.</label>
                            <input type="text" class="form-control" name="GUARDIAN_CONTACT" id="GUARDIAN_CONTACT" autocomplete="off" value="<?php echo reg_val($old, 'GUARDIAN_CONTACT'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GUARDIAN_EMAIL">Guardian's Email</label>
                            <input type="email" class="form-control<?php echo reg_err($errors, 'GUARDIAN_EMAIL'); ?>" name="GUARDIAN_EMAIL" id="GUARDIAN_EMAIL" autocomplete="off" value="<?php echo reg_val($old, 'GUARDIAN_EMAIL'); ?>">
                            <?php reg_feedback($errors, 'GUARDIAN_EMAIL'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="GUARDIAN_ADDRESS">Guardian's Address</label>
                            <input type="text" class="form-control" name="GUARDIAN_ADDRESS" id="GUARDIAN_ADDRESS" value="<?php echo reg_val($old, 'GUARDIAN_ADDRESS'); ?>">
                        </div>
                    </div>

                    <!-- Other Person Supporting / Boarding - HIPANAO SOLUTIONS -->
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="OTHER_PERSON_SUPPORTING">Other Person Supporting</label>
                            <input type="text" class="form-control" name="OTHER_PERSON_SUPPORTING" id="OTHER_PERSON_SUPPORTING" value="<?php echo reg_val($old, 'OTHER_PERSON_SUPPORTING'); ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="d-block">Are you Boarding?</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="IS_BOARDING" id="IS_BOARDING_YES" value="Yes" <?php echo (($old['IS_BOARDING'] ?? '') === 'Yes') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="IS_BOARDING_YES">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="IS_BOARDING" id="IS_BOARDING_NO" value="No" <?php echo (($old['IS_BOARDING'] ?? 'No') !== 'Yes') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="IS_BOARDING_NO">No</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="d-block">With Family?</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="WITH_FAMILY" id="WITH_FAMILY_YES" value="Yes" <?php echo (($old['WITH_FAMILY'] ?? 'Yes') !== 'No') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="WITH_FAMILY_YES">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="WITH_FAMILY" id="WITH_FAMILY_NO" value="No" <?php echo (($old['WITH_FAMILY'] ?? '') === 'No') ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="WITH_FAMILY_NO">No</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="BOARDING_ADDRESS">Boarding Address <span class="optional-marker">(kung "Yes" sa Boarding)</span></label>
                            <input type="text" class="form-control" name="BOARDING_ADDRESS" id="BOARDING_ADDRESS" value="<?php echo reg_val($old, 'BOARDING_ADDRESS'); ?>">
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     EDUCATIONAL BACKGROUND - HIPANAO SOLUTIONS.
                     ============================================================ -->
                <div class="form-section-title">
                    <i class="fas fa-graduation-cap mr-2"></i>Educational Background
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ELEM_SCHOOL">Elementary</label>
                            <input type="text" class="form-control" name="ELEM_SCHOOL" id="ELEM_SCHOOL" value="<?php echo reg_val($old, 'ELEM_SCHOOL'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="ELEM_ADDRESS">Address</label>
                            <input type="text" class="form-control" name="ELEM_ADDRESS" id="ELEM_ADDRESS" value="<?php echo reg_val($old, 'ELEM_ADDRESS'); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="ELEM_YEAR">Academic Year</label>
                            <input type="text" class="form-control" name="ELEM_YEAR" id="ELEM_YEAR" placeholder="e.g. 2015-2016" value="<?php echo reg_val($old, 'ELEM_YEAR'); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="SEC_SCHOOL">Secondary</label>
                            <input type="text" class="form-control" name="SEC_SCHOOL" id="SEC_SCHOOL" value="<?php echo reg_val($old, 'SEC_SCHOOL'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="SEC_ADDRESS">Address</label>
                            <input type="text" class="form-control" name="SEC_ADDRESS" id="SEC_ADDRESS" value="<?php echo reg_val($old, 'SEC_ADDRESS'); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="SEC_YEAR">Academic Year</label>
                            <input type="text" class="form-control" name="SEC_YEAR" id="SEC_YEAR" placeholder="e.g. 2021-2022" value="<?php echo reg_val($old, 'SEC_YEAR'); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="COLLEGE_SCHOOL">College <span class="optional-marker">(kung meron)</span></label>
                            <input type="text" class="form-control" name="COLLEGE_SCHOOL" id="COLLEGE_SCHOOL" value="<?php echo reg_val($old, 'COLLEGE_SCHOOL'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="COLLEGE_ADDRESS">Address</label>
                            <input type="text" class="form-control" name="COLLEGE_ADDRESS" id="COLLEGE_ADDRESS" value="<?php echo reg_val($old, 'COLLEGE_ADDRESS'); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="COLLEGE_YEAR">Academic Year</label>
                            <input type="text" class="form-control" name="COLLEGE_YEAR" id="COLLEGE_YEAR" value="<?php echo reg_val($old, 'COLLEGE_YEAR'); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="VOC_SCHOOL">Vocational <span class="optional-marker">(kung meron)</span></label>
                            <input type="text" class="form-control" name="VOC_SCHOOL" id="VOC_SCHOOL" value="<?php echo reg_val($old, 'VOC_SCHOOL'); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="VOC_ADDRESS">Address</label>
                            <input type="text" class="form-control" name="VOC_ADDRESS" id="VOC_ADDRESS" value="<?php echo reg_val($old, 'VOC_ADDRESS'); ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="VOC_YEAR">Academic Year</label>
                            <input type="text" class="form-control" name="VOC_YEAR" id="VOC_YEAR" value="<?php echo reg_val($old, 'VOC_YEAR'); ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="OTHERS_SCHOOL">Others</label>
                            <input type="text" class="form-control" name="OTHERS_SCHOOL" id="OTHERS_SCHOOL" value="<?php echo reg_val($old, 'OTHERS_SCHOOL'); ?>">
                        </div>
                    </div>
                </div>

                <!-- ============================================================
                     HIPANAO SOLUTIONS - Ang Requirements Checklist ay
                     inalis na dito bilang form na pinipilan ng applicant.
                     Static/reminder na lang ito ngayon (tingnan sa ibaba,
                     sa loob ng "Registration Complete!" success page) -
                     ang mismong pag-check kung saling-natanggap na ang
                     mga dokumento ay ginagawa na ng REGISTRAR STAFF sa
                     module/enrollment (bagong "Document" stage), hindi
                     ng applicant mismo dito sa public registration form.
                     ============================================================ -->

                <!-- Submit Button -->
                <button type="submit" class="btn btn-register">
                    <i class="fas fa-check-circle mr-2"></i>REGISTER NOW
                </button>

                <!-- Back Link -->
                <div class="back-link">
                    <p><a href="<?php echo WEB_ROOT; ?>index.php"><i class="fas fa-arrow-left mr-2"></i>Back to Main</a></p>
                </div>
            </form>
            <?php else: ?>
                <!-- Success Page -->
                <div class="text-center">
                    <div style="font-size: 80px; color: #28a745; margin: 40px 0;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3 style="color: #28a745; margin-bottom: 20px;">Registration Complete!</h3>
                    <p style="font-size: 16px; color: #666; margin-bottom: 10px;">
                        Your registration has been successfully submitted.<br>
                        <strong>Please proceed to the Registrar's Office for the next steps.</strong>
                    </p>
                    <div class="alert alert-warning text-left d-inline-block" style="font-size: 15px; max-width: 480px;">
                        <i class="fas fa-key mr-1"></i>
                        <strong>Remember your Student ID and the password you just created</strong> -
                        you will use both of them to log in to the Student Portal
                        (Student Login) once your account has been activated by the
                        Registrar's Office.
                    </div>
                    <br><br>
                    <!-- =========================================================
                         HIPANAO SOLUTIONS - Requirements Checklist bilang
                         PAALALA lang (read-only) - hindi na ito form na
                         pinipilan ng applicant. Ang aktwal na pag-check kung
                         saling-natanggap na ang bawat dokumento ay ginagawa
                         na ng REGISTRAR STAFF sa module/enrollment (bagong
                         "Document" stage sa pagitan ng Assign at Paid).
                         ========================================================= -->
                    <div class="alert alert-secondary text-left d-inline-block" style="font-size: 15px; max-width: 480px;">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        <strong>Dalhin ang mga sumusunod na dokumento sa Registrar's Office:</strong>
                        <ul class="mb-0 mt-2" style="padding-left: 20px;">
                            <li>Form 138/SF9 (High School Report Card)</li>
                            <li>Certificate of Good Moral Character</li>
                            <li>Certificate of Live Birth - NSO/PSA (photocopy)</li>
                            <li>Baptismal/Dedication Certificate (photocopy)</li>
                            <li>Result of Assessment Test</li>
                            <li>For transferees: Certificate of Transfer Credential and O.T.R.</li>
                            <li>For married students (Female): Marriage Contract - NSO (photocopy)</li>
                            <li>1 pc. 2x2 Picture</li>
                        </ul>
                    </div>
                    <br><br>
                    <a href="<?php echo WEB_ROOT; ?>index.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Main
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="<?php echo WEB_ROOT; ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo WEB_ROOT; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('PICTURE').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    this.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('img-preview').src = event.target.result;
                };
                reader.readAsDataURL(file);

                document.querySelector('.custom-file-label').textContent = file.name;
            }
        });

        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            // ID No. and LRN No. are intentionally NOT in this list - optional na sila.
            // ACC_PASSWORD / ACC_PASSWORD_CONFIRM ay required na - ito ang
            // gagamitin niya pang-login sa Student Portal.
            const requiredFields = ['LNAME', 'FNAME', 'MNAME', 'SEX', 'BDAY', 'BPLACE', 'NATIONALITY', 'RELIGION', 'COURSE_ID', 'CONTACT_NO', 'EMAIL', 'CONTACTPERSON', 'HOME_ADD', 'ACC_PASSWORD', 'ACC_PASSWORD_CONFIRM'];

            for (let field of requiredFields) {
                const element = document.getElementById(field);
                if (!element.value || element.value.trim() === '') {
                    alert('Please fill in all required fields (marked with *)');
                    e.preventDefault();
                    element.focus();
                    return false;
                }
            }

            const pass = document.getElementById('ACC_PASSWORD');
            const passConfirm = document.getElementById('ACC_PASSWORD_CONFIRM');

            if (pass.value.length < 6) {
                alert('Password must be at least 6 characters.');
                e.preventDefault();
                pass.focus();
                return false;
            }

            if (pass.value !== passConfirm.value) {
                alert('Passwords do not match. Please make sure both password fields are the same - you will need this to log in.');
                e.preventDefault();
                passConfirm.focus();
                return false;
            }
        });
    </script>
</body>
</html>
