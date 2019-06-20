<?php
ob_start();
	
	require "../includes/db_functions.php";
	require "../includes/common_functions.inc.php";
	require "../includes/interface_functions.inc.php";
	
	$role=logincheck();
	if ($role == "") header("Location: index.php?msg=Please login to continue.");
	
	// $initialContent needs a value in order for the 'Back' button to be shown in the navbar
	$initialContent = isset($_GET['page']) ? $_GET['page'] : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Informer 4 SMB</title>

	<?php 
		include("inc/head.inc.php"); 
	?>
		
	<script>
		// ----- ADD ANY NECESSARY JAVASCRIPT FUNCTIONS ----- 
		
	</script>
</head>
			
<body class="">
	<?php 
		// set the title to be shown in the nav bar
		$defaultTitle = 'Change Password';
		include("inc/navbar.inc.php"); 
	?>

	<section id="main">
		<div id="content" class="">
			<div class="container">
				
				<div class="tab-content">
					<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 content-panel">
						
						<div class="panel-body">
						<?php 
							// CHECK NECESSARY PERMISSIONS
							if ($permissions['Summary']==0) { 
						?>
							<!-- message to display when user doesn't have sufficient permission to view content -->
							<p>You do not have permission to view this example.</p>
						<?php } else { 

	        if($_POST) {

                $mode=$_POST['mode'];
                $oldpass=(isset($_POST['oldpass'])) ? $_POST['oldpass'] : "";
                $newpass=(isset($_POST['newpass'])) ? $_POST['newpass'] : "";	
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/user.php");
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => $mode,'userid'=>$_SESSION["userid"],'oldpass'=>$oldpass,'newpass'=>$newpass));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $result = curl_exec($ch);
				
				//echo $result;
				
                curl_close($ch);
		
        		$xml = simplexml_load_string(trim($result)) ;
        
                        $userresult=$xml->result;
                        $authresult=$xml->message;
        		echo "<p><b>$authresult</b></p>";
            }
?>	

<form action="changepass.php" method="post" name="changepass">
<p>Old Password: <input type="password" name="oldpass" value="" /></p>
<p>New Password: <input type="password" name="newpass" value="" /></p>
<input type="hidden" name="mode" value="changepass" />
<p><input type="submit" name="submit" value="Change Password" /></p>
</form>
<?php } ?>
						</div>
						
					</div>
				</div>
				
			</div>
		</div>
	</section>

	<!-- footer include -->
	<?php 
		include("inc/footer.inc.php"); 
	?>
	<!-- end footer -->

</body>
</html>
<?php
ob_end_flush;
?>
