<?php
session_start(); 
	
	function logged_in() {
		return isset($_SESSION['UID']);
        
	}
	
	function confirm_logged_in() {
		if (!logged_in()) {?>

			<script type="text/javascript">
				window.location = "login.php";
			</script>

		<?php
		}
	}
function admin_confirm_logged_in() {
		if (@!$_SESSION['UID']) {?>
			<script type="text/javascript">
				window.location ="login.php";
			</script>

		<?php
		}
	}

	function studlogged_in() {
		return isset($_SESSION['UID']);
        
	}
	function studconfirm_logged_in() {
		if (!studlogged_in()) {?>
			<script type="text/javascript">
				window.location = "index.php";
			</script>

		<?php
		}
	}

	function message($msg="", $msgtype="") {
	  if(!empty($msg)) {
	    
	    $_SESSION['message'] = $msg;
	    $_SESSION['msgtype'] = $msgtype;
	  } else {
	    
			return $message;
	  }
	}
	function check_message(){
	
		if(isset($_SESSION['message'])){
			if(isset($_SESSION['msgtype'])){
				if ($_SESSION['msgtype']=="info"){
	 				echo '<script type="text/javascript">
				      swal("Information!","'.$_SESSION['message'].'","info");
					</script>';
	 				 
				}elseif($_SESSION['msgtype']=="error"){
					echo '<script type="text/javascript">
					      swal("Error!","'.$_SESSION['message'].'","error");
						</script>';
									
				}elseif($_SESSION['msgtype']=="success"){
						
				echo '<script type="text/javascript">
			      swal("Success!","'.$_SESSION['message'].'","success");
				</script>';

				}	
				unset($_SESSION['message']);
	 			unset($_SESSION['msgtype']);
	   		}
  
		}	

	}

?>
