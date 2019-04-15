<?php
	require_once("../includes/db_functions.php");
	require_once("../includes/common_functions.inc.php");	
?>

<!-- Vendor CSS -->
<link href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" rel="stylesheet">
<link href="vendors/bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css" rel="stylesheet">
<link href="vendors/bower_components/datatables.net-dt/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
<link href="https://cdnjs.cloudflare.com/ajax/libs/hint.css/2.5.0/hint.min.css" rel="stylesheet">


<!-- CSS -->
<link href="css/app_1.min.css" rel="stylesheet">
<link href="css/informer_style.css" rel="stylesheet">

<script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css" rel="stylesheet">


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>


<?php 
	// CHECK PERMISSIONS
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/userrolepermissions.php");
	curl_setopt($ch, CURLOPT_POST, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'rolepermission','roleid' => $role,'category'=>'Reports'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	$result = curl_exec($ch);
	curl_close($ch);
	
	$xml = simplexml_load_string(trim($result)) ;
		
	$userresult=$xml->result;
	$permissions=array();
	if($userresult=="ok") {
		foreach ($xml->permissions->permission as $element) {
			$category=trim($element->category);
			$access=trim($element->access);
			$permissions[$category]=$access;
		}
		//print_r($permissions);
	}
	
	// check if user has access to reports
	if ($permissions['Reports']==0) {
		echo $GLOBALS['accesserrormsg']; 
		die;
	}
	
	// GET REPORT VALUES
	$selectedCompany = (isset($_GET['company'])) ? $_GET['company']:$_POST['company'];
	$selectedMonth = (isset($_GET['month'])) ? $_GET['month']:$_POST['month'];
	$uname=isset($_SESSION["uname"]) ? $_SESSION["uname"] : "";
	
	// flag whether or not there is data to load/show
	$noData = true;
	if ((isset($selectedCompany) && $selectedCompany!='') && (isset($selectedMonth) && $selectedMonth!='')) {
		$noData = false;
	}
	
	
	
	// TEMP
	$demo = true;
?>