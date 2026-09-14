
<?php 

require_once 'include/initialize.php';

@session_start();

unset( $_SESSION['UID'] );
unset( $_SESSION['DISPLAYNAME'] );
unset( $_SESSION['USERNAME'] );
unset( $_SESSION['TYPE'] );

?>
<script language="javascript">
	window.location.href = "login.php?logout=1"
</script>
              <?php

?>
