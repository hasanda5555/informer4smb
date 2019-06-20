<?php
	ob_start();
    
	require_once("../includes/config.inc.php");
	require_once("../includes/db_functions.php");
	
	if($_POST) {
	
		$mode=$_POST['mode'];
		$passwd=(isset($_POST['passwd'])) ? $_POST['passwd'] : "";
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."user/user.php");
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array('mode' => $mode,'userid'=>$_POST['userid'],'passwd'=>$passwd));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$result = curl_exec($ch);
		curl_close($ch);
		
		echo $result;
		$xml = simplexml_load_string(trim($result)) ;
		
		$userresult=$xml->result;
		$authresult=$xml->message;
		
		if($userresult=="ok" && $authresult=="User authenticated") {
			$userid=$xml->user->id;
			$gname=$xml->user->first_name;
			$lname=$xml->user->surname;
			$regtype=$xml->user->regtype;
			$resetpwd=$xml->user->reset;
			session_start();
			$_SESSION["userid"]=trim($userid);
			$_SESSION["uname"]=trim($gname)." ".trim($lname);
			$_SESSION["regtype"]=trim($regtype);
			setsession($userid, session_id());
			
			// check for company & period
			if ($resetpwd==1) 
				header("Location: changepass.php");
			elseif (isset($_POST['company']) && isset($_POST['period'])) 
				header("Location: main.php?company=".urlencode($_POST['company'])."&month=".$_POST['period']);
			else 
				header("Location: main.php");
		}
		else{
			$_GET[msg]='Re-login failed.';
		}
		
	}
ob_end_flush();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Informer 4 SMB</title>

        <!-- Vendor CSS -->
        <link href="vendors/bower_components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css" rel="stylesheet">
        
        <!-- CSS -->
        <link href="css/app_1.min.css" rel="stylesheet">
        <link href="css/informer_style.css" rel="stylesheet">
		<link href="vendors/bootstrapDialog/bootstrap-dialog.min.css" rel="stylesheet">
	</head>

    <body>
		<div class="hidden">
			<form id="loginForm" action="#" method="post">
				<input type="hidden" name="mode" value="authenticate"/>
				<input type="hidden" name="userid" value=""/>
				<input type="hidden" name="passwd" value=""/>
			</form>
		</div>
		<div class="wrapper">
            <div class="lc-block">
				<div class="lcb-form">
					
					<!-- PANEL HEADER -->
					<div class="heading">
						<div class="logo-container">
							<img class="logo" src="img/MkIVlogo.png"/>
						</div>
						<div class="text-container">
							<span class="title">Informer 4 SMB<sup>TM</sup></span>
							<span class="subtitle">Innovative Strategic Financial Markers</span>
						</div>
					</div>
					<div class="subheading">
						<div class="sub-container">
							<!-- Hedgerow Tools Pty Ltd -->&nbsp;
						</div>
					</div>
					
					<div class="content">
						<!-- LOGIN -->
						<div id="login" class="content-panel">
							<h2>Login</h2>
							<p class="message <?php echo (isset($_GET[msg]) ? 'error' : 'hidden'); ?>"><?php if(isset($_GET[msg])) echo $_GET[msg]; ?></p>
							
							<form id="formLogin" action="#" method="post">
								<input type="hidden" name="mode" value="authenticate" />
								<input type="hidden" name="company" value="" />
								<input type="hidden" name="period" value="" />
								
								<div class="form-group fg-float">
									<div class="fg-line">
										<input name="userid" type="text" class="form-control fg-username">
										<label class="fg-label">Username</label>
									</div>
								</div>
									
								<div class="form-group fg-float">
									<div class="fg-line">
										<input name="passwd" type="password" class="form-control fg-password">
										<label class="fg-label">Password</label>
									</div>
								</div>

								<div class="row">
									<div class="col-sm-12 text-right">
										<a href="#" class="btn btn-link waves-effect pull-left" data-action="changeContentPanel" data-panel="forget-password">Forgot <br class="visible-xs"/>password</a>
										<button id="btn-login" class="btn btn-lg btn-primary waves-effect" type="submit">Sign in</button>
									</div>
								</div>
							</form>
						</div>
						
						<!-- FORGOT PASSWORD -->
						<div id="forget-password" class="content-panel hidden">
							<h2>Forgot password</h2>
							<p class="message hidden"></p>
							
							<form action="#" method="post">
								<input type="hidden" name="mode" value="forgotpass" />
								
								<p class="text-left">Enter your username and a new password will be emailed to you.</p>
							
								<div class="form-group fg-float">
									<div class="fg-line">
										<input name="userid" type="text" class="form-control fg-email">
										<label class="fg-label">Username</label>
									</div>
								</div>
									
								<div class="row">
									<div class="col-sm-12 text-right">
										<a href="#" class="btn btn-link waves-effect pull-left" data-action="changeContentPanel" data-panel="login">Back to <br class="visible-xs"/>login</a>
										<button id="btn-reset" class="btn btn-lg btn-primary waves-effect">Reset password</button>
									</div>
								</div>
							</form>
						</div>
						
						<!-- HELP -->
						<div id="help" class="content-panel hidden">
							<h2>Help information</h2>
							<p class="text-left">If you need any assistance, or require an account to be set up, please contact Informer 4 SMB by phone on <a href="tel:12345678">1234 56 78</a> or email at <a href="mailto:info@informer4smb.com.au" target="_blank">info@informer4smb.com.au</a> </p>
								
							<div class="row">
								<div class="col-sm-12 text-center">
									<button class="btn btn-lg btn-primary waves-effect" data-action="changeContentPanel" data-panel="previous">Back</button>
								</div>
							</div>
						</div>
					</div>
                </div>

                <div class="lcb-navigation">
                    <a href="" data-action="changeContentPanel" data-panel="help"><i>?</i> <span>Help</span></a>
                </div>
            </div>
		</div>
									
		<div class="hidden">
			<div class="data-dialog" width="450">
				<div class="company-select-container hidden">
					<p class="label">Select a company:</p>
					<select class='company-select' name='company'>
						<option value=""></option>
					</select>
				</div>
				<div class="company-set hidden">
					<p class="label">Company:</p><p class="value company-value"></p>
				</div>
				<div class="period-select-container hidden">
					<p class="label">Select a period:</p>
					<select class='period-select' name='period'>
						<option value=""></option>
					</select>
				</div>
				<div class="period-set hidden">
					<p class="label">Period:</p><p class="value period-value"></p>
				</div>
			</div>
		</div>
		
		
		<!-- Javascript Libraries -->
        <script src="vendors/bower_components/jquery/dist/jquery.min.js"></script>
		
		<script src="vendors/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
		
		<script src="vendors/bower_components/Waves/dist/waves.min.js"></script>
		<script src="vendors/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>

        <script src="js/app.min.js"></script>
    </body>
</html>