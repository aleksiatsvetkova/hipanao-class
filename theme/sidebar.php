   <nav class="mt-2">

 <!-- HIPANAO SOLUTIONS  -->

        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
         
           <?php
             /* HIPANAO SOLUTIONS - Dashboard link: dalhin ang bawat
                account type pabalik sa SARILI nilang module (Student /
                Program Head), hindi sa buong Admin dashboard. */
             $hipanaoDashHref = WEB_ROOT;
             if (isset($_SESSION['TYPE']) && $_SESSION['TYPE'] === 'Student') {
                 $hipanaoDashHref = WEB_ROOT.'module/studentmodule/index.php';
             } elseif (isset($_SESSION['TYPE']) && $_SESSION['TYPE'] === 'Program Head') {
                 $hipanaoDashHref = WEB_ROOT.'module/programhead/index.php';
             }
           ?>
           <li class="nav-header"> 
            <a href='<?php echo $hipanaoDashHref; ?>' class="nav-link <?php  echo ($title=='Home' || $title=='My Profile') ? "active" : 'na' ;?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                   </p>
            </a>
          </li>

<?php /* HIPANAO SOLUTIONS - Student Module.
         Ang Student account ay may sarili niyang menu dito (ibang
         module/studentmodule/ views - sariling section/grade/history/
         payment lang) - hiwalay sa buong Admin/Staff menu sa ibaba,
         na naka-block pa rin sa Student sa theme/template.php kung
         sakaling subukang puntahan pa rin ng direkta ang URL nito. */ ?>
<?php if (isset($_SESSION['TYPE']) && $_SESSION['TYPE'] === 'Student'): ?>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=section' class="nav-link <?php  echo ($title=='My Section') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-chalkboard"></i>
  <p style="font-weight: normal">
                My Section
  </p>
  </a>
  </li>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=grades' class="nav-link <?php  echo ($title=='My Grades') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-graduation-cap"></i>
  <p style="font-weight: normal">
                My Grades
  </p>
  </a>
  </li>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=history' class="nav-link <?php  echo ($title=='Enrollment History') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-history"></i>
  <p style="font-weight: normal">
                Enrollment History
  </p>
  </a>
  </li>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=payment' class="nav-link <?php  echo ($title=='My Payments') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-money-check-alt"></i>
  <p style="font-weight: normal">
                My Payments
  </p>
  </a>
  </li>

  <?php /* HIPANAO SOLUTIONS - My Account: dito LANG pwedeng baguhin ng
           Student ang sarili niyang profile photo at password (login
           account) - hiwalay sa "My Profile" na view-only lang. */ ?>
  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/studentmodule/index.php?view=account' class="nav-link <?php  echo ($title=='My Account') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-user-cog"></i>
  <p style="font-weight: normal">
                My Account
  </p>
  </a>
  </li>

<?php endif; ?>

<?php /* HIPANAO SOLUTIONS - Program Head Module.
         Kaparehong ideya ng Student Module sa itaas - sariling menu
         lang ng Program Head (kanyang course dashboard/students,
         sections, grades, account) - hiwalay sa buong Admin/Staff
         menu sa ibaba, na naka-block pa rin sa Program Head sa
         theme/template.php kung sakaling subukang puntahan pa rin
         ng direkta ang URL nito. */ ?>
<?php if (isset($_SESSION['TYPE']) && $_SESSION['TYPE'] === 'Program Head'): ?>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/programhead/index.php?view=dashboard' class="nav-link <?php  echo ($title=='Program Head Dashboard') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-user-graduate"></i>
  <p style="font-weight: normal">
                Students
  </p>
  </a>
  </li>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/programhead/index.php?view=sections' class="nav-link <?php  echo ($title=='Sections') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-chalkboard"></i>
  <p style="font-weight: normal">
                Sections
  </p>
  </a>
  </li>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/programhead/index.php?view=subjects' class="nav-link <?php  echo ($title=='Subjects') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-book-open"></i>
  <p style="font-weight: normal">
                Subjects
  </p>
  </a>
  </li>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/programhead/index.php?view=grades' class="nav-link <?php  echo ($title=='Grades') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-graduation-cap"></i>
  <p style="font-weight: normal">
                Grades
  </p>
  </a>
  </li>

  <li class="nav-item">
  <a href='<?php echo WEB_ROOT; ?>module/programhead/index.php?view=account' class="nav-link <?php  echo ($title=='My Account') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-user-cog"></i>
  <p style="font-weight: normal">
                My Account
  </p>
  </a>
  </li>

<?php endif; ?>

<?php if ((!isset($_SESSION['TYPE']) || $_SESSION['TYPE'] !== 'Student') && (!isset($_SESSION['TYPE']) || $_SESSION['TYPE'] !== 'Program Head')): ?>

 <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/generic/index.php?t=alumni_details' class="nav-link <?php  echo ($title=='Alumni Details') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-id-card"></i>
  <p style="font-weight: normal">
                Alumni Details
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/student' class="nav-link <?php  echo ($title=='Student Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-user-graduate"></i>
  <p style="font-weight: normal">
                Student
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/course' class="nav-link <?php  echo ($title=='Course Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-book"></i>
  <p style="font-weight: normal">
                Course
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/subject' class="nav-link <?php  echo ($title=='Subject Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-book-open"></i>
  <p style="font-weight: normal">
                Subject
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblschoolyear' class="nav-link <?php  echo ($title=='School Year') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-calendar-alt"></i>
  <p style="font-weight: normal">
                School Year
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/generic/index.php?t=tblsections' class="nav-link <?php  echo ($title=='Sections') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-chalkboard"></i>
  <p style="font-weight: normal">
                Sections
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/enrollment/index.php' class="nav-link <?php  echo ($title=='Enrollment') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-clipboard-list"></i>
  <p style="font-weight: normal">
                Enrollment
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/payment/index.php' class="nav-link <?php  echo ($title=='Payment') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-money-check-alt"></i>
  <p style="font-weight: normal">
                Payment
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/enrollmentdetails/index.php' class="nav-link <?php  echo ($title=='Enrollment Details') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-list-alt"></i>
  <p style="font-weight: normal">
                Enrollment Details
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/hitoryenrollment/index.php' class="nav-link <?php  echo ($title=='History Enrollment') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-history"></i>
  <p style="font-weight: normal">
                History Enrollment
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/grade/index.php' class="nav-link <?php  echo ($title=='Grades') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-graduation-cap"></i>
  <p style="font-weight: normal">
                Grades
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/announcement/index.php' class="nav-link <?php  echo ($title=='Announcement Module') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-bullhorn"></i>
  <p style="font-weight: normal">
                Announcements
  </p>
  </a>
  </li>

  <li class="nav-item"> 
  <a href='<?php echo WEB_ROOT; ?>module/about/index.php' class="nav-link <?php  echo ($title=='About') ? "active" : 'na' ;?>">
  <i class="nav-icon fa fa-info-circle"></i>
  <p style="font-weight: normal">
                About
  </p>
  </a>
  </li>

          <li class="nav-item has-treeview">
           
            <a href="#" class="nav-link <?php  echo ($title=='User Module' || $title=='User Type' ) ? "active" : 'na' ;?>">
              <i class="fas fa-cog"></i>
              <p>
                Account Settings
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href='<?php echo WEB_ROOT; ?>module/user/' class="nav-link">
                  <i class="nav-icon fa fa-users"></i>
                  <p>Manage User Accounts</p>
                </a>
              </li>
              <li class="nav-item">
                <a href='<?php echo WEB_ROOT; ?>module/usertype/' class="nav-link">
                  <i class="nav-icon fa fa-key"></i>
                  <p>Manage User Type</p>
                </a>
              </li>    
            </ul>
          </li>
          
            </ul>
              
        </ul>

<?php endif; ?>

      </nav>