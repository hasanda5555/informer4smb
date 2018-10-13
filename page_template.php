<?php
ob_start();
	
	require "../includes/db_functions.php";
	require "../includes/common_functions.inc.php";
	require "../includes/interface_functions.inc.php";
	
	$role=logincheck();
	if ($role == "") header("Location: index.php?msg=Please login to continue.");
	
	// $initialContent needs a value in order for the 'Back' button to be shown in the navbar
	$initialContent = isset($_GET['page']) ? $_GET['page'] : 'Example';
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
		$defaultTitle = 'Example template';
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
							if ($permissions['Reports']==0) { 
						?>
							<!-- message to display when user doesn't have sufficient permission to view content -->
							<p>You do not have permission to view this example.</p>
						<?php } else { ?>
							<!-- ADD MAIN PAGE CONTENT HERE -->
							<h2>Example content</h2>
							<p>You would add your own content here.</p>
							
							
							<!-- END MAIN CONTENT -->
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
