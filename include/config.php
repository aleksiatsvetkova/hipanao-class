<?php

defined('DB_SERVER') ? null : define("DB_SERVER", "localhost");
defined('DB_USER')   ? null : define("DB_USER", "root");
defined('DB_PASS')   ? null : define("DB_PASS", "");
defined('DB_NAME')   ? null : define("DB_NAME", "alumni_db");

defined('ENROLLMENT_FEE')   ? null : define("ENROLLMENT_FEE", 1000);
defined('MAJOR_UNIT_RATE')  ? null : define("MAJOR_UNIT_RATE", 850);
defined('MINOR_UNIT_RATE')  ? null : define("MINOR_UNIT_RATE", 450);

/* HIPANAO SOLUTIONS - Palitan na lang ang mga ito ng tunay na pangalan/
   address ng school para awtomatikong magamit sa Print Enrollment Form
   (module/enrollmentdetails/print.php) at sa Official Receipt. */
defined('SCHOOL_NAME')    ? null : define("SCHOOL_NAME", "Tañon College");
defined('SCHOOL_ADDRESS') ? null : define("SCHOOL_ADDRESS", "San Carlos City");
defined('SCHOOL_CONTACT') ? null : define("SCHOOL_CONTACT", "0900-000-0000");
defined('SCHOOL_LOGO')    ? null : define("SCHOOL_LOGO", "tanong.png");

defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

$this_file = str_replace('\\', '/', __FILE__);
$doc_root  = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

defined('SITE_ROOT') ? null : define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT']);
defined('LIB_PATH')  ? null : define('LIB_PATH', SITE_ROOT.DS.'include');

$webRoot = str_replace(array($doc_root, "include/config.php"), '', $this_file);
$srvRoot = str_replace('config/config.php', '', $this_file);

define('WEB_ROOT', $webRoot);
define('SRV_ROOT', $srvRoot);
?>
