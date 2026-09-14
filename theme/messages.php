<?php

if (isset($_GET['msg'])) {
	
		echo '
<script type="text/javascript">

$(document).ready(function(){

  Swal.fire(
            "Good job!",
            "You clicked the button!",
            "success"
          )
});

</script>';
}
?>