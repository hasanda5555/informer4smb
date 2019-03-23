<?php
ob_start();
	
	require "../includes/db_functions.php";
	require "../includes/common_functions.inc.php";
	require "../includes/interface_functions.inc.php";
	
	$role=logincheck();
	if ($role == "") header("Location: index.php?msg=Please login to continue.");
	
	$initialContent = isset($_GET['page']) ? $_GET['page'] : '';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Informer 4 SMB</title>
<style>
    #divrpt .report {
        display: inline !important;
    }
    .text-right {
        text-align: right !important;
    }
    tr.table-head th {
        background-color: #22437e;
        color: #fff;
    }
    td.sec-head {
        background: #e4e4e4;
        color: #ea7e19;
    }
</style>
	<?php 
		include("inc/head.inc.php"); 
	?>
	
	<script>
		var userid = <?php echo $_SESSION['userid']; ?>
	</script>
		
	<script>
		// ----- CUSTOM PAGE SCRIPTS (from Ishti's page, path corrected) ----- 
		function showrpt(comp,yr,mnth,bs) {
			if(bs=="") {
				$.get('../reports.php', {showrpt: true, company: comp,year:yr,month:mnth}, function(response) {
			
					document.getElementById('divrpt').innerHTML=response;
				});
			}
			else{
				$.get('../bsreports.php', {showrpt: true, company: comp,year:yr,month:mnth}, function(response) {
			
					document.getElementById('divrpt').innerHTML=response;
				});
			}
		}

		function showrpttabs(yr,comp,type,bs) {

			if(bs=="") {
				$.get('../reports.php', {year: yr, company: comp, rpttype: type}, function(response) {
					// Log the response to the console
				   // alert("Response: "+response);
					document.getElementById('divrpttabs').innerHTML=response;
				});
			}
			else{
				$.get('../bsreports.php', {year: yr, company: comp, rpttype: type}, function(response) {
					// Log the response to the console
				   // alert("Response: "+response);
					document.getElementById('divrpttabs').innerHTML=response;
				});
			}
		}
	</script>
</head>
			
<body class="">
	<?php 
		// CHECK PERMISSIONS
		if ($permissions['Reports']==0) {
			echo $GLOBALS['accesserrormsg']; 
			die;
		}
	?>
	
	<?php 
		// set the title to be shown in the nav bar
		$defaultTitle = 'Reports';
		include("inc/navbar.inc.php"); 
	?>

	<section id="main">
		<div id="content" class="">
			<div class="container">

				<div class="tab-content">
					<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 content-panel">
						<div class="panel-body">
						<?php if ($permissions['Reports']==0) { ?>
							<p>You do not have permission to view reports.</p>
						<?php } else { ?>
							
							<!-- report content -->
		
							<?php if(isset($_GET['bs']) && $_GET['bs']=='1') { ?>
								<h2 class="">Balance Sheet Report</h2>
							<?php } else { ?>
								<h2 class="">Profit/Loss Report</h2>
							<?php } ?>
							
							<?php
							require_once("../lib/PHPExcel/IOFactory.php");

							$data=array();
							$msg=false;
							$year=array();
							$company="";
							$msgtext="";
/*
							if($_POST) {
								$reports="";
									
								echo "<table><tr><td style=\"padding-top:10px; padding-left:10px; padding-bottom:10px; font-size:16px;\"><strong>Company:</strong>&nbsp;".$_POST['company']."</td></tr>\n<tr><td style=\"padding-top:10px; padding-left:10px; padding-bottom:10px; font-size:16px;\"><strong>Year:</strong>&nbsp;".$_POST['year']."</td></tr>";
								
								echo "</table>\n";*/
							?>
							
							<?php 
							if($_POST) {
								$reports="";
							?>
							<p class="left-content m-b-10">Company:</p>
							<p class="right-content m-b-10"><b><?php echo $_POST['company']; ?></b></p>
							
							<p class="spacer"></p>

							<p class="left-content">Year:</p>
							<p class="right-content"><b><?php echo $_POST['year']; ?></b></p>
							
							<p class="text-center">
								<button class="btn btn-primary waves-effect m-r-10" onclick='document.getElementById("divrpt").innerHTML=""; showrpttabs(<?php echo  $_POST['year'] ?>,"<?php echo $_POST['company']?>","1","<?php echo  $_POST['bs'] ?>")'>Show Monthly Report</button>
								<button class="btn btn-primary waves-effect" onclick='document.getElementById("divrpt").innerHTML=""; showrpttabs(<?php echo  $_POST['year'] ?>,"<?php echo $_POST['company'] ?>","3","<?php echo  $_POST['bs'] ?>")'>Show Quaterly Report</button>
							</p>

							<?php					
								//echo "<p><button onclick=\"window.open('charts.php?company=$company&amp;year=$year&amp;month=$month','_blank');\">Show Chart</button></p>";
								
								echo "<div id='divrpttabs' ></div>";
							
								echo "<script>\n document.getElementById('tab0').click();\n</script>\n";
								
								echo "<div id='divrpt' ></div>";
							} 
							else {
							?>
	  
							<div id="frmmsg">
								<div id="reportfrm">
									<form name="frmrpt" action="reports_menu.php?company=<?php echo urlencode($selectedCompany); ?>&month=<?php echo $selectedMonth; ?>&page=report<?php echo $_GET['bs']==1 ? '-bs' : ''; ?>&bs=<?php echo $_GET['bs']; ?>" method="post" >
										<p class="left-content">Company:</p>
										<p class="right-content">
											<select id="company" name="company">
										
											<?php
												
												$ch = curl_init();
												curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/org.php");
												curl_setopt($ch, CURLOPT_POST, TRUE);
												curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'getorgassigned','userid'=>$_SESSION["userid"]));
												curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
												$result = curl_exec($ch);
												
												//echo $result;
												
												curl_close($ch);

												$xml = simplexml_load_string(trim($result)) ;

												$userresult=$xml->result;
												$authresult=$xml->message; 
												
												$options="";
												foreach ($xml->orgs->org as $org) {
													$orgid=$org->orgid;
													$orgname=$org->orgname;
													echo "<option value=\"$orgname\" >$orgname</option> \r\n";
												}
											?>
											<!-- option value="default">System Default</option -->
											</select>
										</p>
											
										<p class="spacer"></p>
											
										<p class="left-content">Year:</p>
										<p class="right-content">
											<select id="year" name="year">
											<?php
												$year=date("Y");
												for($i=($year-5);$i<($year+5);$i++){
													$selected=($i==$year) ? "selected" : "";
													echo "<option value=\"$i\" $selected>$i</option> \r\n";
												}
												
											?>
											</select>
										</p>
											 
										<p class="spacer"></p>
											
										<?php
											$bs=(!isset($_GET['bs'])) ? "" : "1";
											
											echo "<input type=\"hidden\" value=\"$bs\" id=\"bs\" name=\"bs\">";
										 ?>
										<button class="btn btn-primary waves-effect pull-right m-r-30" type="submit" name="showrpt">Show Report</button>
									</form>  
								</div>
								
								<div style="clear:both"></div>
							</div>
							<?php } ?>
						<?php } ?>
						</div>
						<!-- end panel-body -->
						
					</div>

				</div>

				<!-- end report content -->
				
				
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
