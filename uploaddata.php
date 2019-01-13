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

        <?php 
			include("inc/head.inc.php"); 
			require_once "../vendor/autoload.php";
			
			 //============================================================================================
    //   XERO PHP IMPORTS
    //============================================================================================
    
    use XeroPHP\Application\PublicApplication;
    use XeroPHP\Remote\Request;
    use XeroPHP\Remote\Exception\UnauthorizedException;
    use XeroPHP\Remote\URL;
    use XeroPHP\Models\Accounting\Report;
    
    //============================================================================================
    //   XERO OAUTH CONFIGURATION
    //============================================================================================
    $xeroConfig = [
    		'oauth' => [
    				'callback'        => 'http://informer4smb.test/pla/uploaddata.php?completed=1',
    				'consumer_key'    => 'WVYLNFGVYWIY8XETSEOQDFX62A8GRI',
    				'consumer_secret' => '7TZZ7CFLFVW62WVSCHTKSIAHGNNZ8S',
    		],
    		'curl' => [
    				CURLOPT_CAINFO => '../config/xero/certs/ca-bundle.crt',
    		],
    ];
    
    //=============================================================================================
    //   END XERO OAUTH CONFIGURATION
    //=============================================================================================
		?>
		
		<script>
			var userid = <?php echo $_SESSION['userid']; ?>
		</script>

    </head>
			
	<body class="">
		<?php 
		
    		    $ch = curl_init();
    	curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/userrolepermissions.php");
    	curl_setopt($ch, CURLOPT_POST, TRUE);
    	curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'rolepermission','roleid' => $role,'category'=>'Upload Data'));
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    	$result = curl_exec($ch);
    	curl_close($ch);
    	//echo $result;
    	
    	$xml = simplexml_load_string(trim($result)) ;
    		
    	$userresult=$xml->result;
    	$permissions=array();
    	if($userresult=="ok") {
    		foreach ($xml->permissions->permission as $element) {
    			//$permissions[$element->category]=$element->access;
    			$category=trim($element->category);
    			$access=trim($element->access);
    			$permissions[$category]=$access;
    		}
    		//print_r($permissions);
    	}
			// CHECK PERMISSIONS
			if ($permissions['Upload Data']==0) {
				echo $GLOBALS['accesserrormsg']; 
				die;
			}
	 
			$defaultTitle = 'Upload';
			include("inc/navbar.inc.php"); 
		?>


<section id="main">
<div id="content" class="">
	<div class="container">

	
<div class="tab-content">
	<?php if($_POST && isset($_POST["upload"])) { ?>
	<div class=" content-panel">
	<?php } else { ?>
	<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 content-panel">
	<?php } ?>
		<div class="panel-body">
<?php if ($permissions['Upload Data']==0) { ?>
			<p>You do not have permission to upload data.</p>
<?php } else { ?>
<!-- upload content -->
	
<?php


$msg=false;
$upload=false;
$company="";
$msgtext="";
$reportData = "";
$path = "";
$fromDate=null;
$toDate=null;

if($_POST) {
    
    //print_r($_POST);

	if(isset($_POST["upload"])) {
	    //require_once("lib/PHPExcel/IOFactory.php");
	    
	    $company=urldecode($_POST['company']);	    
	    $source=$_POST['source'];
	    $filetype=$_POST['xlstype'];
	    
		$target_dir = "../uploads/$company/$source/$filetype/";
		
		
		//echo $target_dir.
		$target_file = basename($_FILES["fileToUpload"]["name"]);
		
		$upload=upload($target_dir,$target_file);
		
		//echo $upload;
		
		if($upload){
		    
		    $filename = pathinfo($_FILES['fileToUpload']['name'], PATHINFO_FILENAME);
		    $msg=true;
			$msgtext="<h2 class='step'>File upload successful.</h2><br>
    		<form action=\"mapdata.php\" method=\"post\" name=\"mapfrm\" id=\"mapfrm\">
            <input type=\"hidden\" name=\"company\" id=\"company\" value=\"$company\" />
            <input type=\"hidden\" name=\"source\" id=\"source\" value=\"$source\" />
            <input type=\"hidden\" name=\"xlstype\" id=\"xlstype\" value=\"$filetype\" />
            <input type=\"hidden\" name=\"filename\" id=\"filename\" value=\"".urlencode($filename)."\" />
            <input type=\"submit\" class=\"btn btn-primary pull-left m-r-30 waves-effect\" value=\"Map uploaded file\" name=\"getfile\">
            </form>";
            
            //echo $msgtext;
		}

	} else if(isset($_POST["uploadXero"])) {
		//===================================================
		//   CODE FOR HANDLING XERO UPLOADED DATA
		//===================================================
		//require_once("lib/PHPExcel/IOFactory.php");
		 
		$company=urldecode($_POST['company']);		
		//echo $company;
		$fromDate=$_POST['fromDate'];
		$toDate=$_POST['toDate'];
		//echo $fromDate;
		
		$filetype=$_POST['xlstype']; //e.g Balance Sheet or Profit & Loss
		$source = 'xero';
		$target_dir = "../uploads/$company/$source/$filetype/";
		$target_file = 'xero-'.$filetype.rand(0, 1000).'.csv';//TBD - change file naming convention
		
		function getBalanceSheet($xeroConfig, $fromDate, $toDate)
		{
			//use output buffering to convert output to string
			ob_start();
			
			//select 'From' and 'To' dates
			$fromTS = strtotime($fromDate);
			$toTS = strtotime($toDate);
				
			$from = new \DateTime();
			$from->setTimestamp($fromTS);
				
			$to = new \DateTime();
			$to->setTimestamp($toTS);
				
			//calculate the number of months between the two dates
			$year1 = date('Y', $fromTS);
			$year2 = date('Y', $toTS);
				
			$month1 = date('m', $fromTS);
			$month2 = date('m', $toTS);

			//query date strings
			$toDateString = date('Y-m-d', $toTS);
				
			$periods = $year2 - $year1;
			$periods = $periods == 0 ? 1 : $periods;
			$timeframe = 'YEAR';
				
			try {
				$xero = getXeroAuth($xeroConfig);
				$data = $xero->load('Accounting\\Report\\BalanceSheet')
				             ->setParameter('date', $toDateString)
				             ->setParameter('periods', $periods)
				             ->setParameter('timeframe', $timeframe)
				             ->execute();
					
				$format = 'csv';
				if(strtoupper($format) == 'DEBUG'){
					print("<pre>".print_r($data,true)."</pre>");
				}
				else if(strtoupper($format) == 'JSON'){
					echo "<pre>";
					//just output rows as JSON
					echo json_encode($data[0]->Rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
					echo "</pre>";
				}
				//csv is default format
				else {
					printCSV($data);
				}
			} catch (\Exception $e){
				//reinitialise - user needs to authenticate again
				unset($_SESSION['oauth']);
				session_destroy();
				//force re-auth flow - TBD
			}
			
			$output = ob_get_clean();
			return $output;
		}
		
		function getProfitLoss($xeroConfig, $fromDate, $toDate)
		{
			//use output buffering to convert output to string
			ob_start();
			
			//select 'From' and 'To' dates
			if($fromDate){
			   $fromTS = strtotime($fromDate);			
			} else {
			   $fromTS = strtotime($fromDate.' -12 months');
			}
			
			$from = new \DateTime();
			$from->setTimestamp($fromTS);
			
			$to = new \DateTime();
			if($toDate){
			   $toTS = strtotime($toDate);	
			   $to->setTimestamp($toTS);
			}
			   			
			//calculate the number of months between the two dates			
			$year1 = date('Y', $fromTS);
			$year2 = date('Y', $toTS);
			
			$month1 = date('m', $fromTS);
			$month2 = date('m', $toTS);
			$periods = (($year2 - $year1) * 12) + ($month2 - $month1);			
			$timeframe = 'MONTH';
		
			try
			{
				$xero = getXeroAuth($xeroConfig);
				$data = $xero->load('Accounting\\Report\\ProfitLoss')
                             ->fromDate($from)
				             ->toDate($to)
				             ->setParameter('periods', $periods)
				             ->setParameter('timeframe', $timeframe)
				             ->execute();
				 
				$format = 'csv';
					
				if(strtoupper($format) == 'DEBUG'){
					print("<pre>".print_r($data,true)."</pre>");
				}
				else if(strtoupper($format) == 'JSON'){
					echo "<pre>";
					//just output rows as JSON
					echo json_encode($data[0]->Rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
					echo "</pre>";
				}
				//csv is default format
				else {
					printCSV($data);
				}
			} catch (\Exception $e){
				//reinitialise - user needs to authenticate again
				unset($_SESSION['oauth']);
				session_destroy();
			}
			
			$output = ob_get_clean();
			return $output;
		}
		
		
		
		$len = -1;
		
		if($filetype){
			if($filetype == 'bs'){
				$reportData = getBalanceSheet($xeroConfig, $fromDate, $toDate);
				$breaks = array("<br />","<br>","<br/>");
				//replace HTML line breaks with CRLF for output file
                $reportData = str_ireplace($breaks, "\r\n", $reportData);  
				
				$path = $target_dir.$target_file;
				//echo $path;
				try{
				   $len = file_put_contents($path, $reportData);
				}catch(Exception $e){
					echo 'Exception calling file_put_contents';
				}
			} 
			elseif($filetype == 'pl'){
				$reportData = getProfitLoss($xeroConfig, $fromDate, $toDate);
				
				$breaks = array("<br />","<br>","<br/>");
				//replace HTML line breaks with CRLF for output file
				$reportData = str_ireplace($breaks, "\r\n", $reportData);
				
				$path = $target_dir.$target_file;
				//echo $path;
				try{
					$len = file_put_contents($path, $reportData);
				}catch(Exception $e){
					echo 'Exception calling file_put_contents';
				}
			}
			
			if($len !== FALSE && $len >= 0){
				$msg=true;
				$filename = pathinfo($target_file, PATHINFO_FILENAME);
				$msgtext="<h2 class='step'>Xero data upload successful.</h2><br>
				<form action=\"mapdata.php\" method=\"post\" name=\"mapfrm\" id=\"mapfrm\">
				<input type=\"hidden\" name=\"company\" id=\"company\" value=\"$company\" />
				<input type=\"hidden\" name=\"source\" id=\"source\" value=\"$source\" />
				<input type=\"hidden\" name=\"xlstype\" id=\"xlstype\" value=\"$filetype\" />
				<input type=\"hidden\" name=\"filename\" id=\"filename\" value=\"".urlencode($filename)."\" />
                    <input type=\"submit\" class=\"btn btn-primary pull-left m-r-30 waves-effect\" value=\"Map uploaded file\" name=\"getfile\">
                    </form>";
			}
		}
		
		//$upload=upload($target_dir,$target_file);
		
		//====================================================
		//   END - CODE FOR HANDLING XERO UPLOADED DATA
		//====================================================
	}
}

?>

  
       <div id="frmmsg">
<?php 

if(!$upload && !$msg) { 
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/org.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'getorgassigned','userid'=>$_SESSION["userid"]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
 
    curl_close($ch);
    
    $xml = simplexml_load_string(trim($result)) ;
    
    $userresult=$xml->result;
    $authresult=$xml->message; 
    $options="<option value=\"\">(please select)</option> \r\n";
    foreach ($xml->orgs->org as $org) {
        $orgid=$org->orgid;
        $orgname=$org->orgname;
        $selected = strcasecmp($orgname, $company) == 0 ? 'selected' : '';
	    $options.="<option value=\"$orgname\" $selected>$orgname</option> \r\n";
	}
?>
<div id="uploadfrm">
	<form name="frmupload" action="uploaddata.php?company=<?php echo urlencode($selectedCompany); ?>&month=<?php echo $selectedMonth; ?>&page=upload" method="post" enctype="multipart/form-data">
	    <input type="hidden" name="company" id="company" value="<?php echo urlencode($selectedCompany); ?>" />
	    <h2 class="step">Step 1: Upload file</h2>
		
		<p class="left-content">Select CSV to upload:</p>
		<p class="right-content"><input type="file"  name="fileToUpload" id="fileToUpload"></p>
		<p class="spacer">&nbsp;</p>
		
		<p class="left-content">Source:</p>
		<p class="right-content">
			<select id="source" name="source">
				<option value="myob">MYOB</option>
				<option value="xero" selected>XERO</option>
			</select>
		</p>
		<p class="spacer">&nbsp;</p>
		
		<p class="left-content">Type:</p>
		<p class="right-content">
			<select id="xlstype" name="xlstype">
				<option value="bs">Balance Sheet</option>
				<option value="pl" selected>Profit & Loss</option>
			</select>
		</p>
		<p class="spacer">&nbsp;</p>
		
		<button type="submit" name="upload" class="btn btn-primary pull-right m-r-30" >Upload CSV</button>
	    
	</form>
</div>
<?php 
} 
else if(isset($_POST["uploadXero"])) {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/org.php");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'getorgassigned','userid'=>$_SESSION["userid"]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
	
		curl_close($ch);
	
		$xml = simplexml_load_string(trim($result)) ;
	
		$userresult=$xml->result;
		$authresult=$xml->message;
		$options="<option value=\"\">(please select)</option> \r\n";
		foreach ($xml->orgs->org as $org) {
			$orgid=$org->orgid;
			$orgname=$org->orgname;
			$selected = strcasecmp($orgname, $company) == 0 ? 'selected' : '';
			$options.="<option value=\"$orgname\" $selected>$orgname</option> \r\n";
		}
    }
else
	echo '<div style="padding-left:10px;"><h5>'.$msgtext.'</h5></div>';
?>

<div style="clear:both"></div>
</div>
</div>
<!-- ============================================================ -->
<!--              SUPPORT FOR XERO INTEGRATION                    -->
<!-- ============================================================ -->
<?php 	
	//for demo only
	function getAllowedAbns(){
		//return ["11111111138", "11111111150"];
		$abns = array();
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/org.php");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => 'orglist','userid'=>$_SESSION["userid"]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		$xml = simplexml_load_string(trim($result)) ;
		
		$userresult=$xml->result;
		foreach ($xml->orgs->org as $org) {
			$abns[] = $org->abn;
		}
		return $abns;
	}
			
	/**
	 * Show the 'Connect to <Service>' button on the Demo -> API Auth page.
	 *
	 * @return Response
	 */
	function index($xeroConfig, $initOnLoad = false)
	{
		$data = array();
	
		if(isset($_REQUEST['completed']) && $_REQUEST['completed'] == '1'){
			$data['authCompleted'] = 1;
			//echo 'SWAP';
			
			//run the verify step to swap REQUEST token for ACCESS token
			//- this is a VITAL step
			try{
				swapTokens($xeroConfig);
			} catch(UnauthorizedException $e){
				//Log and ignore
				error_log('swapTokens() cannot be called twice in a session (back button clicked maybe?): '.$e->getMessage());
				error_log('swapTokens() ACCESS token is used where a REQUEST token is expected');
			} catch(\Exception $e){
				//Log and ignore
				error_log('swapTokens() called - general exception : '.$e->getMessage());
			}
		}
			
		//Start a session for the oauth session storage
		session_start();
		$xero = new PublicApplication($xeroConfig);
		
		$oauth_session = getOAuthSession();
		
		//check if session has expired
		if(isset($_SESSION['oauth']) && 
		   isset($_SESSION['oauth']['expires']) && 
		   $_SESSION['oauth']['expires'] != NULL && 
		   $_SESSION['oauth']['expires'] <= time()){
			$oauth_session = null;
		}
		
		//$oauth_session = null;
				
		if (null === $oauth_session) {
				
			$url = new URL($xero, URL::OAUTH_REQUEST_TOKEN);
			$request = new Request($xero, $url);
	
			try {
				$request->send();
			} catch (Exception $e) {
				error_log($e->getMessage());
				if ($request->getResponse()) {
					error_log($request->getResponse()->getOAuthResponse());
				}
			}
	
			$oauth_response = $request->getResponse()->getOAuthResponse();
				
			setOAuthSession(
					$oauth_response['oauth_token'],
					$oauth_response['oauth_token_secret']
			);
	
			$data['url'] = $xero->getAuthorizeURL($oauth_response['oauth_token']);
		}
		else
		{
			//echo '4';
			$data['url'] = '';
			$_SESSION['oauth']['authenticated-with-xero'] = '1';
		}
				 
		return $data;
	}
	
	
	function setOAuthSession($token, $secret, $expires = null)
	{
		// expires sends back an int
		if ($expires !== null) {
			$expires = time() + intval($expires);
		}
	
		$_SESSION['oauth'] = [
				'token' => $token,
				'token_secret' => $secret,
				'expires' => $expires
		];
	}
	
	function getOAuthSession()
	{
		//If it doesn't exist or is expired, return null
		if (!isset($_SESSION['oauth'])
				|| ($_SESSION['oauth']['expires'] !== null
						&& $_SESSION['oauth']['expires'] <= time())
		) {
			return null;
		}
		
		return $_SESSION['oauth'];
	}
	
	/**
	 * Fetch an ACCESS token from Xero and swap with the current REQUEST token in the session.
	 *
	 * @return Response
	 */
	function swapTokens($xeroConfig)
	{
		// Start a session for the oauth session storage
		session_start();
		$xero = new PublicApplication($xeroConfig);
	
		$oauth_session = getOAuthSession();
	
		$xero->getOAuthClient()
		->setToken($oauth_session['token'])
		->setTokenSecret($oauth_session['token_secret']);
	
		if (isset($_REQUEST['oauth_verifier'])) {
	
			$xero->getOAuthClient()->setVerifier($_REQUEST['oauth_verifier']);
	
			$url = new URL($xero, URL::OAUTH_ACCESS_TOKEN);
			$request = new Request($xero, $url);
	
			$request->send();
			$oauth_response = $request->getResponse()->getOAuthResponse();
	
			setOAuthSession(
					$oauth_response['oauth_token'],
					$oauth_response['oauth_token_secret'],
					$oauth_response['oauth_expires_in']
			);
	
			$xero->getOAuthClient()
			->setToken($oauth_session['token'])
			->setTokenSecret($oauth_session['token_secret']);
		}
	}
	
	/**
	 * Cross check that organisation is inside allowed set. i.e. the organisation is
	 * one which the user has adviser access to.
	 *
	 * @return Response
	 */
	function checkOrganisationAllowed($xero, $xeroConfig)
	{
		$isAllowed = false;
		$organisation = null;
		try {
			$organisation = $xero->load('Accounting\\Organisation')->execute();
		} catch (Exception $e) {
			//print_r($e);
			error_log($e->getMessage());
			//back to 'Connect to Xero' button
			return index($xeroConfig);
		}
				
		//ABN is Xero API 'Registration Number'
		$organisationAbn = $organisation[0]->RegistrationNumber;
		//echo $organisationAbn;
		$allowedArr = getAllowedAbns();
		if(in_array($organisationAbn, $allowedArr)){
			$isAllowed = true;
		}
		
		return $isAllowed;
		
	}//end of function
	
	function getXeroAuth($xeroConfig)
	{
		// Start a session for the oauth session storage
		session_start();
		$xero = new PublicApplication($xeroConfig);
	
		$oauth_session = getOAuthSession();
        $xero->getOAuthClient()
			  ->setToken($oauth_session['token'])
			  ->setTokenSecret($oauth_session['token_secret']);
			
		return $xero;
	}
	//end of function
	
    if(!$upload && !$msg ) {
    	//echo '1';
	    $data = index($xeroConfig);
    }

    //only show blue 'Connect to Xero' button if
    //there is no oauth session or xero auth is not complete
    if(!isset($_SESSION['oauth']) || 
       !isset($_SESSION['oauth']['authenticated-with-xero']) || 
              $_SESSION['oauth']['authenticated-with-xero'] !== '1'){
?>

<div class="container content-box" style="margin-top: 20px;">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default no-bg">
				<div class="panel-body">
					 <a href="<?php echo $data['url']; ?>"><img src="img/connect_xero_button_blue.png" alt="Connect to Xero"></img></a>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
} else {
	//echo '6';
	
	if(checkOrganisationAllowed(getXeroAuth($xeroConfig), $xeroConfig)){
		//var_dump($_SESSION['oauth']);
		//var_dump($GLOBALS);
?>
<div class="container content-box" style="margin-top: 20px;">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-default">
				<div class="panel-body">
					 <div id="uploadfrm">
					 <div style="padding:10px 0px 20px 0px;"><b>Upload Xero Data (Direct)</b></div>
					 <div style="color:#13B5EA;padding-bottom:20px;">&#10003;&nbsp;Connected to Xero <span id="api-logout" style="cursor: pointer;">[<u>Logout</u>]</span></div>					 
					 <form name="frmupload" action="uploaddata.php" method="post" enctype="multipart/form-data" class="form-horizontal">
                                  <input type="hidden" name="company" id="company" value="<?php echo urlencode($selectedCompany); ?>" />
	                             <div class="form-group">
	                                <label class="col-lg-3 control-label">Type:</label>
	                                <div class="col-lg-9">
	                                  <select id="xlstype" name="xlstype" class="form-control">
	   	                                <option value="bs">Balance Sheet</option>
	   	                                <option value="pl" selected>Profit & Loss</option>
	                                  </select>
                                    </div>
	                              </div>
	                              <div class="form-group">
	                                <label class="col-lg-3 control-label">From:</label>
	                                <div class="col-lg-9"><input id="fromDate" name="fromDate" type="date" class="form-control"></div>
	                              </div>
	                              <div class="form-group">
	                                <label class="col-lg-3 control-label">To:</label>
	                                <div class="col-lg-9"><input id="toDate" name="toDate" type="date" class="form-control"></div>
	                              </div>
	                              <div class="form-group">
	                                <div class="col-lg-6">
	                                   <input type="submit" value="Upload From Xero" name="uploadXero" class="btn btn-primary">
	                                </div>
	                              </input>
	                 </form>
	                 <?php
	                   if($reportData) 
	                   {
	                 ?>
	                     <br/>
	                     <span style="color:#000; font-size:12px;"> 
	                     
	                     <b>FROM: <?php echo $fromDate; ?> TO: <?php echo $toDate; ?></b><br/>
	                     <u>Raw CSV Data from Xero (copied to: <b><?php echo $path; ?></b>)</u> <br/>
	                 <?php 
	                       echo $reportData;
	                 ?>
	                    </span>
	                 <?php 
	                    }
	                 ?>
				</div>
			</div>
		</div>
	</div>
  </div>
</div>
<?php 
	} else {
?>
<div class="container content-box">
	<div class="row">
		<div class="col-md-11">
			<div class="panel panel-default">
				<div class="panel-body" style="color:red;">
					 &nbsp;Access to organisation not allowed via Informer
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
	}
}
?>
<!-- ============================================================ -->
<!--          END - SUPPORT FOR XERO INTEGRATION                  -->
<!-- ============================================================ -->
</div>

<!-- end upload content -->
<?php } ?>
		</div>
	</div>
</div>
</section>


	<!-- footer include -->
	<?php 
		include("inc/footer.inc.php"); 
	?>
	<!-- end footer -->
	
	<script>

	function apiLogout() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'apilogout.php', true);
        xhr.send();

        //redirect to upload page
        window.location = window.location.href;
    }


    $(document).ready(function() {
    	$('#api-logout').click(function() {
    		apiLogout();
    		console.log('api logout');
    	});
    });
 </script>


</body>
</html>
<?php
ob_end_flush;
?>
