<?php

require_once ("../../include/initialize.php");
	  if (!isset($_SESSION['ACCOUNT_ID'])){
     
     }

$action = (isset($_GET['action']) && $_GET['action'] != '') ? $_GET['action'] : '';

switch ($action) {
	case 'add' :
	doInsert();
	break;
	
	case 'edit' :
	doEdit();
	break;
	
	case 'editpass' :
	dochangepass();
	break;

	case 'delete' :
	doDelete();
	break;
	 
	}

	function handleUserPhotoUpload(){

		if (!isset($_FILES['PICTURE']) || $_FILES['PICTURE']['error'] === UPLOAD_ERR_NO_FILE) {
			return '';
		}
		if ($_FILES['PICTURE']['error'] !== UPLOAD_ERR_OK) {
			return false;
		}

		$tmpName   = $_FILES['PICTURE']['tmp_name'];
		$imageInfo = @getimagesize($tmpName);
		if ($imageInfo === false) {
			return false;
		}

		$allowedExt = array('jpg' => true, 'jpeg' => true, 'png' => true, 'gif' => true, 'webp' => true);
		$ext = strtolower(pathinfo($_FILES['PICTURE']['name'], PATHINFO_EXTENSION));
		if (!isset($allowedExt[$ext])) {
			return false;
		}

		$uploadDir = __DIR__ . DS . 'images' . DS;
		if (!is_dir($uploadDir)) {
			@mkdir($uploadDir, 0755, true);
		}

		$fileName = 'user_' . date('YmdHis') . '_' . mt_rand(1000, 9999) . '.' . $ext;

		if (move_uploaded_file($tmpName, $uploadDir . $fileName)) {
			return 'images/' . $fileName;
		}
		return false;
	}

	function doInsert(){
  
		$user = new User();
		$DISPLAYNAME   = $_POST['DISPLAYNAME'];
		$USERNAME	= $_POST['USERNAME'];
		$PASSWORD 	= $_POST['PASSWORD'];
		$TYPE 		= $_POST['TYPE'];
		$DATEADDED  = date("Y-m-d H:i:s");
		$ADDEDBY	= $_SESSION['UID'];
		$DATEMODIFIED = date("Y-m-d H:i:s");
		$MODIFIEDBY	 = $_SESSION['UID'];
		$res = $user->find_all_user($USERNAME);
		
			if ($res >=1) {
				message("Username already exist!", "error");
				redirect('index.php');
			}else{

				$photo = handleUserPhotoUpload();
				if ($photo === false) {
					message("Uploaded photo is not a valid image!", "error");
					redirect('index.php');
					return;
				}

				$user->DISPLAYNAME = $DISPLAYNAME;
				$user->USERNAME = $USERNAME;
				$user->PASSWORD = sha1($PASSWORD);
				$user->TYPE 	= $TYPE;
				$user->DATEADDED = $DATEADDED;
				$user->ADDEDBY 	= $ADDEDBY;
				$user->DATEMODIFIED = $DATEMODIFIED;
				$user->MODIFIEDBY 	= $MODIFIEDBY;
				if ($photo !== '') {
					$user->PICTURE = $photo;
				}
				
				 $istrue = $user->create(); 
				 
				 		if ($istrue == true) {
					 		message("New User [". $USERNAME ."] has been created successfully!", "success");
					 		redirect('index.php');
					 	}else{
					 		message("No user has been created successfully!", "error");
					 		redirect('index.php');
					 	}
			}	 

	}
	function doEdit(){
		if (isset($_POST['edit'])) {
			$user = new User();
			$UID	= $_POST['UID'];
			$USERNAME	= $_POST['USERNAME'];
			
			$TYPE 		= $_POST['TYPE'];
			if (isset($_POST['status']) =='on') {
				$STATUSACTIVE = 1;
			}else{
				$STATUSACTIVE = 0;
			}

			$DATEMODIFIED = date("Y-m-d H:i:s");
			$MODIFIEDBY	 = $_SESSION['UID'];
			$res = $user->find_all_user_notthis($USERNAME, $UID);
		
				if ($res >=1) {
					message("Username already exist!", "error");
					redirect('index.php');
				}else{

				$photo = handleUserPhotoUpload();
				if ($photo === false) {
					message("Uploaded photo is not a valid image!", "error");
					redirect('index.php');
					return;
				}

				$user->USERNAME = $USERNAME;
				
				$user->TYPE 	= $TYPE;
				$user->STATUSACTIVE = $STATUSACTIVE;
				$user->DATEMODIFIED = $DATEMODIFIED;
				$user->MODIFIEDBY 	= $MODIFIEDBY;
				
				if ($photo !== '') {
					$user->PICTURE = $photo;
				}
				
				 $istrue = $user->update($UID); 
				 if ($istrue == true){

				 	if (isset($_SESSION['UID']) && intval($_SESSION['UID']) === intval($UID) && $photo !== '') {
				 		$_SESSION['PICTURE'] = $photo;
				 	}

				 	/* Kung may bagong na-upload na photo dito, i-sync din ito
				 	   pabalik sa naka-link na tblstudent record (kung ang
				 	   user na ito ay isang Student account), para sumalamin
				 	   din sa module/student at sa dashboard niya -
				 	   HIPANAO SOLUTIONS. */
				 	if ($photo !== '') {
				 		hipanao_sync_user_photo_to_student($UID);
				 	}

				 	message("User account has been Updated successfully!", "success");
				 	redirect('index.php');
				 	
				 }else{
				 	message("No user account has been updated successfully!", "error");
				 	redirect('index.php');
				 }
			}
					 
		}
		
	}
	function dochangepass(){
		if (isset($_POST['editpass'])) {
			$user = new User();
			$UID	= $_POST['UID'];
			
			$PASSWORD 	= $_POST['PASSWORD'];			
			$DATEMODIFIED = date("Y-m-d H:i:s");
			$MODIFIEDBY	 = $_SESSION['UID'];
				
				$user->PASSWORD = sha1($PASSWORD);
				$user->DATEMODIFIED = $DATEMODIFIED;
				$user->MODIFIEDBY 	= $MODIFIEDBY;
				
				 $istrue = $user->update($UID); 
				 if ($istrue == true){
				 	
				 	message("User password has been Updated successfully!", "success");
				 	redirect('index.php');
				 	
				 }else{
				 	message("No Password has been updated successfully!", "error");
				 	redirect('index.php');
				 }
			
		}
	}
	function doInsertOldcode(){
  
		$fileName = date("Y").date("m").date("d").$_FILES['PICTURE']['name'];
		$errofile = $_FILES['PICTURE']['error'];
		$type = $_FILES['PICTURE']['type'];
		$temp = $_FILES['PICTURE']['tmp_name'];
		$myfile =preg_replace('#[^a-z.0-9]#i', '', $fileName); 
		$location="images/".$myfile;

		if ( $errofile > 0) {
				message("No Image selected!", "info");
					redirect('index.php');
		}else{
	 
				@$file=$_FILES['PICTURE']['tmp_name'];
				@$image= addslashes(file_get_contents($_FILES['PICTURE']['tmp_name']));
				@$image_name= addslashes($_FILES['PICTURE']['name']); 
				@$image_size= getimagesize($_FILES['PICTURE']['tmp_name']);

			if ($image_size==FALSE ) {
					message("Uploaded File is not an Image!", "error");
					redirect('index.php');
			}else{
					
					move_uploaded_file($temp,"images/" . $myfile);
		 	
							$user = new User();
							$USERNAME	= $_POST['USERNAME'];
							$PASSWORD 	= $_POST['PASSWORD'];
							$FULLNAME   = $_POST['FULLNAME'];
							$TYPE 		= $_POST['TYPE'];
							$DOB 		= $_POST['DOB'];
							$AGE 		= $_POST['AGE'];
							$SEX 		= $_POST['SEX'];
							$ADDRESS 	= $_POST['ADDRESS'];
							$PICTURE 	=  $location;
							$DATEADDED  = date("Y-m-d H:i:s");
							$ADDEDBY	= $_SESSION['UID'];
							$DATEMODIFIED = date("Y-m-d H:i:s");
							$MODIFIEDBY	 = $_SESSION['UID'];
							$res = $user->find_all_user($USERNAME);
							
								if ($res >=1) {
									message("Username already exist!", "error");
									redirect('index.php');
								}else{
									
									$user->USERNAME = $USERNAME;
									$user->PASSWORD = sha1($PASSWORD);
									$user->FULLNAME = $FULLNAME;
									$user->TYPE 	= $TYPE;
									$user->DOB 		= date('Y-m-d', strtotime($DOB));
									$user->AGE 		= $AGE;
									$user->SEX 		= $SEX;
									$user->ADDRESS 	= $ADDRESS;
									$user->PICTURE 	= $PICTURE;
									$user->DATEADDED = $DATEADDED;
									$user->ADDEDBY 	= $ADDEDBY;
									$user->DATEMODIFIED = $DATEMODIFIED;
									$user->MODIFIEDBY 	= $MODIFIEDBY;
									
									 $istrue = $user->create(); 
									 if ($istrue == true){
									 	
									 	message("New User [". $USERNAME ."] has been created successfully!", "success");
									 	redirect('index.php');
									 	
									 }else{
									 	message("No user has been created successfully!", "error");
									 	redirect('index.php');
									 }
								}	 

			}
		}
			 
	}	

	function doDelete(){
		
				$id = 	$_GET['id'];

				$user = New User();
	 		 	$user->delete($id);
			 
			message("User already Deleted!","success");
			redirect('index.php');
		
	}

	function doupdateimage(){
 
			$errofile = $_FILES['photo']['error'];
			$type = $_FILES['photo']['type'];
			$temp = $_FILES['photo']['tmp_name'];
			$myfile =$_FILES['photo']['name'];
		 	$location="photos/".$myfile;

		if ( $errofile > 0) {
				message("No Image Selected!", "error");
				redirect("index.php?view=view&id=". $_GET['id']);
		}else{
	 
				@$file=$_FILES['photo']['tmp_name'];
				@$image= addslashes(file_get_contents($_FILES['photo']['tmp_name']));
				@$image_name= addslashes($_FILES['photo']['name']); 
				@$image_size= getimagesize($_FILES['photo']['tmp_name']);

			if ($image_size==FALSE ) {
				message("Uploaded file is not an image!", "error");
				redirect("index.php?view=view&id=". $_GET['id']);
			}else{
					
					move_uploaded_file($temp,"photos/" . $myfile);
		 	
						$user = New User();
						$user->USERIMAGE 			= $location;
						$user->update($_SESSION['USERID']);
						redirect("index.php");
						 
			}
		}
			 
	}
 
?>