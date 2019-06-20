<?php
	ob_start();
	

	require "../includes/db_functions.php";
	require "../includes/common_functions.inc.php";
	require "../includes/interface_functions.inc.php";
	
	$role=logincheck();
	if ($role == "") header("Location: index.php?msg=Please login to continue.");
	
	session_start();
	
	$initialContent = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<!--[if IE 9 ]><html class="ie9"><![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Informer 4 SMB</title>

        <?php 
			include("inc/head.inc.php"); 
			
			//GET EXPENSE REPORT
			$ch = curl_init();
        	curl_setopt($ch,CURLOPT_URL, $GLOBALS['Base_URL']."informer/reports.php");
        	curl_setopt($ch, CURLOPT_POST, TRUE);
        	curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        		'mode' => 'expensereport',
        		'company' => $selectedCompany,
        		'monthyear' => $selectedMonth
        	));
        	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        	$result = curl_exec($ch);
        	curl_close($ch);
        	
        	// clear any preceeding content in the returned data (i.e. test data)
        	$cleanedResultParts = explode('<data>', $result);
        	if(sizeof($cleanedResultParts) > 1) {
        		$cleanedResult = '<data>'.array_pop($cleanedResultParts);
        	} else {
        		$cleanedResult = $result;
        	}
        	
        	echo '<div style="display:none">'. $result.'</div>';



        
        	$xml = simplexml_load_string(trim($cleanedResult)) ;
        	$json = json_encode($xml);

            $chartDataXml = simplexml_load_string(trim($cleanedResult->charts->chart)) ;
        	$chartDataJson = json_encode($chartDataXml);

        	//echo '<per class="json">hello json : '.$chartDataJson.'</pre>';
        	$statusresult=$xml->result;
        	$messageresult = $xml->message;





        	
        	$uname=isset($_SESSION["uname"]) ? $_SESSION["uname"] : "";
        	$regtype=isset($_SESSION["regtype"]) ? $_SESSION["regtype"] : "";
        	$companyname=isset($_GET["company"]) ? $_GET["company"] : "";
        	
        	//$currentPeriod=$xml->period;
        	$currentPeriod; // temp
        	
        	$monthParts = explode('\|', $selectedMonth);
        	if (sizeof($monthParts) > 1) {
        		// get the current period
        		$months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        		
        		$monthIndex = intval($monthParts[0]);
        		$currentPeriod = $months[$monthIndex-1] .' '.$monthParts[1];
        	}
		?>
		
		<script>
			var userid = <?php echo $_SESSION['userid']; ?>
		</script>
    </head>

    <body class="">
		<?php 
			// CHECK PERMISSIONS
			if ($permissions['Summary'] == 0) {
			    
			  	echo $GLOBALS['accesserrormsg']; 
				die;
			}
		?>
		
		<?php 
			include("inc/layer-data.inc.php"); 
			include("inc/navbar.inc.php"); 
		?>

		<?  echo "<script> var administrationTotalPercent = ". round((float)$summaryData->adminTotalPercent,2) ."</script>";
		    echo '<script> var administrationPurchasesPercent = '. round((float)$summaryData->adminPurchasesPercent,2) .'</script>';
		    echo '<script> var administrationPeoplePercent = '. round((float)$summaryData->adminPeoplePercent,2) .'</script>';

		    echo '<script> var obtainRetainTotalPercent = '. round((float)$summaryData->sellingTotalPercent,2) .'</script>';
		    echo '<script> var obtainRetainPurchasesPercent = '. round((float)$summaryData->sellingPurchasesPercent,2) .'</script>';
		    echo '<script> var obtainRetainPeoplePercent = '. round((float)$summaryData->sellingPeoplePercent,2) .'</script>';

            echo '<script> var makeBuyTotalPercent = '. round((float)$summaryData->operationsTotalPercent,2) .'</script>';
		    echo '<script> var makeBuyPurchasesPercent = '. round((float)$summaryData->operationsPurchasesPercent,2) .'</script>';
		    echo '<script> var makeBuyPeoplePercent = '. round((float)$summaryData->operationsPeoplePercent,2) .'</script>';

            echo '<script> var netProfitTotalPercent = '. round((float)$summaryData->profitTotalPercent,2) .'</script>';

            // Net Profit and Loss
                // - Total 28 / 29 is the same
            echo '<script> var chartNetProfitRevenueTrackingS_01 = '.$summaryData->chartNetProfitRevenueTrackingS.'; console.log("chartNetProfitRevenueTrackingS_01 - ",chartNetProfitRevenueTrackingS_01);</script>';
            echo '<script> var chartNetProfitCrossMarker_03 = '.$summaryData->chartNetProfitCrossMarker.'; console.log("chartNetProfitCrossMarker_03 - ",chartNetProfitCrossMarker_03);</script>';

            // Obtain Retain Customers
                // - Total
            echo '<script> var chartObtainRetainTotalRevenueTrackingS_08 = '.$summaryData->chartObtainRetainTotalRevenueTrackingS.'; console.log("chartObtainRetainTotalRevenueTrackingS_08 - ",chartObtainRetainTotalRevenueTrackingS_08);</script>';
            echo '<script> var chartObtainRetainTotalCrossMarker_09 = '.$summaryData->chartObtainRetainTotalCrossMarker.'; console.log("chartObtainRetainTotalCrossMarker_09 - ",chartObtainRetainTotalCrossMarker_09);</script>';
                // - Purchases
            echo '<script> var chartObtainRetainPurchasesRevenueTrackingS_12 = '.$summaryData->chartObtainRetainPurchasesRevenueTrackingS.'; console.log("chartObtainRetainPurchasesRevenueTrackingS_12 - ",chartObtainRetainPurchasesRevenueTrackingS_12);</script>';
            echo '<script> var chartObtainRetainPurchasesCrossMarker_13 = '.$summaryData->chartObtainRetainPurchasesCrossMarker.'; console.log("chartObtainRetainPurchasesCrossMarker_13 - ",chartObtainRetainPurchasesCrossMarker_13);</script>';
              // - People
            echo '<script> var chartObtainRetainPeopleRevenueTrackingS_10 = '.$summaryData->chartObtainRetainPeopleRevenueTrackingS.'; console.log("chartObtainRetainPeopleRevenueTrackingS_10 - ",chartObtainRetainPeopleRevenueTrackingS_10);</script>';
            echo '<script> var chartObtainRetainPeopleCrossMarker_11 = '.$summaryData->chartObtainRetainPeopleCrossMarker.'; console.log("chartObtainRetainPeopleCrossMarker_11 - ",chartObtainRetainPeopleCrossMarker_11);</script>';

           // Running Bus
               // - Total
           echo '<script> var chartRunningBusTotalRevenueTrackingS_14 = '.$summaryData->chartRunningBusTotalRevenueTrackingS.'; console.log("chartRunningBusTotalRevenueTrackingS_14 - ",chartRunningBusTotalRevenueTrackingS_14);</script>';
           echo '<script> var chartRunningBusTotalCrossMarker_15 = '.$summaryData->chartRunningBusTotalCrossMarker.'; console.log("chartRunningBusTotalCrossMarker_15 - ",chartRunningBusTotalCrossMarker_15);</script>';
               // - Purchases
           echo '<script> var chartRunningBusPurchasesRevenueTrackingS_18 = '.$summaryData->chartRunningBusPurchasesRevenueTrackingS.'; console.log("chartRunningBusPurchasesRevenueTrackingS_18 - ",chartRunningBusPurchasesRevenueTrackingS_18);</script>';
           echo '<script> var chartRunningBusPurchasesCrossMarker_19 = '.$summaryData->chartRunningBusPurchasesCrossMarker.'; console.log("chartRunningBusPurchasesCrossMarker_19 - ",chartRunningBusPurchasesCrossMarker_19);</script>';
             // - People
           echo '<script> var chartRunningBusPeopleRevenueTrackingS_16 = '.$summaryData->chartRunningBusPeopleRevenueTrackingS.'; console.log("chartRunningBusPeopleRevenueTrackingS_16 - ",chartRunningBusPeopleRevenueTrackingS_16);</script>';
           echo '<script> var chartRunningBusPeopleCrossMarker_17 = '.$summaryData->chartRunningBusPeopleCrossMarker.'; console.log("chartRunningBusPeopleCrossMarker_17 - ",chartRunningBusPeopleCrossMarker_17);</script>';


           // Revenue
                // - Total
           echo '<script> var chartRevenueCrossMarker_04 = '.$summaryData->chartRevenueCrossMarker.'; console.log("chartRevenueCrossMarker_04 - ",chartRevenueCrossMarker_04);</script>';


           // Make Buy Chart Set
                // - Total
           echo '<script> var chartMakeBuyRevenueTracking_05 = '.$summaryData->chartMakeBuyRevenueTracking.'; console.log("chartMakeBuyRevenueTracking_05 - ",chartMakeBuyRevenueTracking_05);</script>';
           echo '<script> var chartMakeBuyRevenueTrackingS_06 = '.$summaryData->chartMakeBuyRevenueTrackingS.'; console.log("chartMakeBuyRevenueTrackingS_06 - ",chartMakeBuyRevenueTrackingS_06);</script>';
           echo '<script> var chartMakeBuyCrossMarker_07 = '.$summaryData->chartMakeBuyCrossMarker.'; console.log("chartMakeBuyCrossMarker_07 - ",chartMakeBuyCrossMarker_07);</script>';
                // - Purchases
           echo '<script> var chartMakeBuyPurchasesRevenueTracking_23 = '.$summaryData->chartMakeBuyPurchasesRevenueTracking.'; console.log("chartMakeBuyPurchasesRevenueTracking_23 - ",chartMakeBuyPurchasesRevenueTracking_23);</script>';
           echo '<script> var chartMakeBuyPurchasesRevenueTrackingS_24 = '.$summaryData->chartMakeBuyPurchasesRevenueTrackingS.'; console.log("chartMakeBuyPurchasesRevenueTrackingS_24 - ",chartMakeBuyPurchasesRevenueTrackingS_24);</script>';
           echo '<script> var chartMakeBuyPurchasesCrossMarker_25 = '.$summaryData->chartMakeBuyPurchasesCrossMarker.'; console.log("chartMakeBuyPurchasesCrossMarker_25 - ",chartMakeBuyPurchasesCrossMarker_25);</script>';
                // - People
           echo '<script> var chartMakeBuyPeopleRevenueTracking_20 = '.$summaryData->chartMakeBuyRevenueTracking.'; console.log("chartMakeBuyPeopleRevenueTracking_20 - ",chartMakeBuyPeopleRevenueTracking_20);</script>';
           echo '<script> var chartMakeBuyPeopleRevenueTrackingS_21 = '.$summaryData->chartMakeBuyRevenueTrackingS.'; console.log("chartMakeBuyPeopleRevenueTrackingS_21 - ",chartMakeBuyPeopleRevenueTrackingS_21);</script>';
           echo '<script> var chartMakeBuyPeopleCrossMarker_22 = '.$summaryData->chartMakeBuyCrossMarker.'; console.log("chartMakeBuyPeopleCrossMarker_22 - ",chartMakeBuyPeopleCrossMarker_22);</script>';

           // Gross Profit
                // - Total
           echo '<script> var chartGrossProfitTotalRevenueTrackingS_26 = '.$summaryData->chartGrossProfitTotalRevenueTrackingS.'; console.log("chartGrossProfitTotalRevenueTrackingS_26 - ",chartGrossProfitTotalRevenueTrackingS_26);</script>';
           echo '<script> var chartGrossProfitTotalCrossMarker_27 = '.$summaryData->chartGrossProfitTotalCrossMarker.'; console.log("chartGrossProfitTotalCrossMarker_27 - ",chartGrossProfitTotalCrossMarker_27);</script>';

                    //echo '<script> var administrationPeoplePercent = '.$result->layers->layer[0]->labels[1]->label[0]->currpercent.'</script>';
                    //echo '<script> var administrationPurchasesPercent = '.$result->layers->layer[0]->labels[2]->label[0]->currpercent.'</script>';
?>

		<section id="main">
           <section id="content">
                <div class="container">
                    <div class="tab-content">
						<div role="tabpanel" class="tab-pane <?php if($initialContent == 'dashboard') echo 'active'; ?>" id="dashboard">
							<div class="row">
								<?php if(!$noData) { ?>
								<!-- show the summary data -->

								    <?php //print_r($summaryData); ?>

								<div class="col-xs-12 col-md-8">
									<div class="row clouds cloud-<?php echo $summaryData->totalForecast; ?>">
										<div class="col-xs-9 col-xs-offset-1-5 col-sm-6 col-sm-offset-3">
											<table class="layer-table feature">
												<tr class="title-row">
													<td class="first">
														Revenue
													</td>
													<td class="second" rowspan="3">
														<span class="number <?php if($summaryData->revenuePercent < 0) echo 'negative'; ?>">
															<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->revenuePercent < 0 ? 'down-' : 'up-') . $summaryData->revenueIndicator; ?>.png"/>
															<?php echo prettifyPercentSales($summaryData->revenuePercent, true); ?>
														</span>
													</td>
												</tr>
												<tr class="number-row">
													<td class="primary-cell">
														<span class="number <?php if($summaryData->revenue < 0) echo 'negative'; ?>">
															<?php echo prettifyDollarAmount($summaryData->revenue); ?>
														</span>
													</td>
												</tr>
												<tr class="bottom-row">
													<td class="first"></td>
												</tr>
											</table>
										</div>
										<div class="col-xs-9 col-xs-offset-1-5 col-sm-6 col-sm-offset-3">
											<table class="layer-table feature">
												<tr class="title-row">
													<td class="first">
														Profit
													</td>
													<td class="second" rowspan="3">
														<span class="number <?php if($summaryData->profitPercent < 0) echo 'negative'; ?>">
															<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->profitPercent < 0 ? 'down-' : 'up-') . $summaryData->profitIndicator; ?>.png"/>
															<?php echo prettifyPercentSales($summaryData->profitPercent, true); ?>
														</span>
													</td>
												</tr>
												<tr class="number-row">
													<td class="primary-cell">
														<span class="number <?php if($summaryData->profit < 0) echo 'negative'; ?>">
															<?php echo prettifyDollarAmount($summaryData->profit); ?>
														</span>
													</td>
												</tr>
												<tr class="bottom-row">
													<td class="first"></td>
												</tr>
											</table>
										</div>
									</div>
								<?php if(isset($summaryData->revenueProjection)) {?>
									<div class="layer-wrapper">
										<table class="layer-table">
											<tr class="title-row">
												<td colspan="4">
													Probable Year End Projection
												</td>
											</tr>
											<tr class="number-row">
												<td class="primary-cell">
													<span class="number <?php if($summaryData->revenueProjection < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->revenueProjectionPercent < 0 ? 'down-' : 'up-') . $summaryData->revenueProjectionIndicator; ?>.png"/>
														<?php echo prettifyDollarAmount($summaryData->revenueProjection); ?>
													</span>
													<span class="support">Revenue</span>
												</td>
												<td class="secondary-cell">
													<span class="number <?php if($summaryData->profitProjection < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->profitProjectionPercent < 0 ? 'down-' : 'up-') . $summaryData->profitProjectionIndicator; ?>.png"/>
														<?php echo prettifyDollarAmount($summaryData->profitProjection); ?>
													</span>
													<span class="support">Profit</span>
												</td>
												<td class="tertiary-cell">
													<span class="number <?php if($summaryData->percentProjection < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->percentProjection < 0 ? 'down-' : 'up-') . $summaryData->percentProjectionIndicator; ?>.png"/>
														<?php echo prettifyPercent($summaryData->percentProjection, true); ?>
													</span>
													<span class="support">Sales</span>
												</td>
											</tr>
											<tr class="bottom-row">
												<td colspan="4"></td>
											</tr>
										</table>
										<div class="layer-spacer"></div>
									</div>
								<?php } ?>
								<?php if(isset($summaryData->revenueYTD)) {?>
									<div class="layer-wrapper">
										<table class="layer-table">
											<tr class="title-row">
												<td colspan="4">
													Year-to-Date Progress
												</td>
											</tr>
											<tr class="number-row">
												<td class="primary-cell">
													<span class="number <?php if($summaryData->revenueYTD < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->revenueYTDPercent < 0 ? 'down-' : 'up-') . $summaryData->revenueYTDIndicator; ?>.png"/>
														<?php echo prettifyDollarAmount($summaryData->revenueYTD); ?>
													</span>
													<span class="support">Revenue</span>
												</td>
												<td class="secondary-cell">
													<span class="number <?php if($summaryData->profitYTD < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->profitYTDPercent < 0 ? 'down-' : 'up-') . $summaryData->profitYTDIndicator; ?>.png"/>
														<?php echo prettifyDollarAmount($summaryData->profitYTD); ?>
													</span>
													<span class="support">Profit</span>
												</td>
												<td class="tertiary-cell">
													<span class="number <?php if($summaryData->percentYTD < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->percentYTD < 0 ? 'down-' : 'up-') . $summaryData->percentYTDIndicator; ?>.png"/>
														<?php echo prettifyPercent($summaryData->percentYTD, true); ?>
													</span>
													<span class="support">Sales</span>
												</td>
											</tr>
											<tr class="bottom-row">
												<td colspan="4"></td>
											</tr>
										</table>
										<div class="layer-spacer"></div>
									</div>
								<?php } ?>
								<?php if(isset($summaryData->operations)) {?>	
									<div class="layer-wrapper">
										<table class="layer-table">
											<tr class="title-row">
												<td colspan="4">
													Expenses
												</td>
											</tr>
											<tr class="number-row">
												<td class="primary-cell">
													<span class="number <?php if($summaryData->operations < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->operationsPercent < 0 ? 'down-' : 'up-') . $summaryData->operationsIndicator; ?>.png"/>
														<?php echo prettifyDollarAmount($summaryData->operations); ?>
													</span>
													<span class="support">Operations</span>
												</td>
												<td class="secondary-cell">
													<span class="number <?php if($summaryData->selling < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->sellingPercent < 0 ? 'down-' : 'up-') . $summaryData->sellingIndicator; ?>.png"/>
														<?php echo prettifyDollarAmount($summaryData->selling); ?>
													</span>
													<span class="support">Selling</span>
												</td>
												<td class="tertiary-cell">
													<span class="number <?php if($summaryData->admin < 0) echo 'negative'; ?>">
														<img class="arrow-icon" src="img/arrow-<?php echo ($summaryData->adminPercent < 0 ? 'down-' : 'up-') . $summaryData->adminIndicator; ?>.png"/>
															<?php echo prettifyDollarAmount($summaryData->admin); ?>
													</span>
													<span class="support">Admin</span>
												</td>
											</tr>
											<tr class="bottom-row">
												<td colspan="4"></td>
											</tr>
										</table>
										<div class="layer-spacer"></div>
									</div>
								<?php } ?>
									
									<!-- placeholder for 'cash flow' -->
									<div class="layer-wrapper">
										<table class="layer-table ">
											<tr class="title-row">
												<td colspan="4">Cash Flow</td>
											</tr>
											<tr class="number-row">
												<td class="secondary-cell" colspan="3">
													<span class="number ">Coming soon...</span>
													<!-- div id="cashFlowchart"> </div -->
												</td>
											</tr>
											<tr class="bottom-row">
												<td colspan="4"></td>
											</tr>
										</table>
										<div class="layer-spacer"></div>
									</div>

									<!-- Dendogram chart -->
									<div class="row">
                                        <div class="col-md-12">

                                            <div class="graph-container">
                                               <h4 class="widget-titel">Inspector</h4>
                                               <div class="svg-chart-holder">
                                                   <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                   	 viewBox="0 0 664.3 443.5" style="enable-background:new 0 0 664.3 443.5;" xml:space="preserve">
                                                   <style type="text/css">
                                                   	.st0{fill:none;stroke:#25457D;stroke-width:33;stroke-miterlimit:10;}
                                                   	.st1{fill:none;stroke:#25457D;stroke-width:30;stroke-miterlimit:10;}
                                                   	.st2{fill:none;stroke:#25457D;stroke-width:4;stroke-miterlimit:10;}
                                                   	.st3{fill:none;stroke:#25457D;stroke-width:10;stroke-miterlimit:10;}
                                                   	.st4{fill:none;stroke:#25457D;stroke-width:5;stroke-miterlimit:10;}
                                                   	.st5{fill:#FFFFFF;stroke:#25457D;stroke-width:3;stroke-miterlimit:10;}
                                                   	.st6{font-family:'MyriadPro-Regular';}
                                                   	.st7{font-size:15px;}
                                                   	.st8{fill:none;}
                                                   	.st9{font-size:13px;}
                                                   	.st10{fill:#25457D;}
                                                   </style>
                                                   <title>Inspector</title>
                                                   <g id="Layer_2_1_">
                                                   	<g id="Layer_1-2">
                                                   		<line class="st0" x1="19.2" y1="285.8" x2="131.9" y2="285.8"/>
                                                   		<path class="st1" d="M303.2,161.1c-117.2,5-102,122.5-174.3,123"/>
                                                   		<path id="profitline" class="st2 profitline" d="M128.9,300.7c48.1,0.5,38,118,116,123h399.6"/>
                                                   		<path class="st3" d="M439.2,54.6C347,58.5,358.9,151,302,151.4"/>
                                                   		<path class="st3" d="M439.2,268c-92.2-3.9-80.3-96.4-137.2-96.8"/>
                                                   		<line class="st3" x1="303.3" y1="161.3" x2="433.8" y2="161.3"/>
                                                   		<path class="st4" d="M437.1,51.6c32.7-0.1,25.8-31.5,78.9-32.8h128.5"/>
                                                   		<path class="st4" d="M430.5,159.3c32.7-0.1,25.8-30.5,78.9-30.5h132.1"/>
                                                   		<path class="st4" d="M433.7,164.3c32.7,0.1,25.8,24.5,78.9,25.5h129"/>
                                                   		<path class="st4" d="M439.1,266.3c32.7-0.1,25.8-26.4,78.9-27.5h122.6"/>
                                                   		<path class="st4" d="M436.9,270c32.7,0.1,25.8,35.5,78.9,37h124.8"/>
                                                   		<path class="st4" d="M516,80.8c-53-1-46.2-23.7-78.9-23.8"/>
                                                   		<line class="st4" x1="516" y1="80.8" x2="644.5" y2="80.8"/>
                                                   		<circle id="revenueC" class="st5" cx="32" cy="286.8" r="30.5"/>
                                                   		<circle id="expenditureC" class="st5" cx="284.2" cy="159.3" r="30.5"/>
                                                   		<circle id="netProfitC" class="st5 profitline" cx="644.5" cy="423.7" r="18.3"/>
                                                   		<circle class="st5" cx="644.5" cy="303.4" r="18.3"/>
                                                   		<circle class="st5" cx="644.5" cy="189.9" r="18.3"/>
                                                   		<circle class="st5" cx="644.5" cy="238.8" r="18.3"/>
                                                   		<circle class="st5" cx="644.5" cy="128.8" r="18.3"/>
                                                   		<circle class="st5" cx="644.5" cy="81.4" r="18.3"/>
                                                   		<circle class="st5" cx="644.5" cy="19.8" r="18.3"/>
                                                   		<text transform="matrix(1 0 0 1 16 291.8)" class="st6 st7">100%</text>
                                                   		<rect x="254.5" y="151.7" class="st8" width="60.3" height="16.3"/>
                                                   		<text id="revenueNum" transform="matrix(1 0 0 1 272.9045 164.3)" class="st6 st7">00%</text>
                                                   		<rect x="626.5" y="415.7" class="st8" width="36.1" height="16.3"/>
                                                   		<text id="netProfitNum" transform="matrix(1 0 0 1 633.2003 428.22)" class="st6 st9">00%</text>
                                                   		<rect x="626.5" y="295.2" class="st8" width="36.1" height="16.3"/>
                                                   		<text id="administrationPeoplePercentNum" transform="matrix(1 0 0 1 632.2002 307.7623)" class="st6 st9">00%</text>
                                                   		<rect x="626.5" y="230.6" class="st8" width="36.1" height="16.3"/>
                                                   		<text id="administrationPurchasesPercentNum" transform="matrix(1 0 0 1 632.2002 243.2023)" class="st6 st9">00%</text>
                                                   		<rect x="626.5" y="181.7" class="st8" width="36.1" height="16.3"/>
                                                   		<text id="obtainRetainPeoplePercentNum" transform="matrix(1 0 0 1 633.2002 194.3023)" class="st6 st9">00%</text>
                                                   		<rect x="626.5" y="120.6" class="st8" width="36.1" height="16.3"/>
                                                   		<text id="obtainRetainPurchasesPercentNum" transform="matrix(1 0 0 1 633.2002 133.2023)" class="st6 st9">00%</text>
                                                   		<rect x="626.5" y="73.6" class="st8" width="36.1" height="16.3"/>
                                                   		<text id="makeBuyPeoplePercentNum" transform="matrix(1 0 0 1 633.2002 86.2023)" class="st6 st9">00%</text>
                                                   		<rect x="626.5" y="11.6" class="st8" width="36.1" height="16.3"/>
                                                   		<text id="makeBuyPurchasesPercentNum" transform="matrix(1 0 0 1 633.2002 24.2023)" class="st6 st9">00%</text>
                                                   		<text transform="matrix(1 0 0 1 66.5 322.5)" class="st10 st6 st7">Revenue</text>
                                                   		<text transform="matrix(1 0 0 1 257.5 210.2)" class="st10 st6 st7">Expenses</text>
                                                   		<text transform="matrix(1 0 0 1 568.709 440.6994)" class="st10 st6 st7">Profit</text>
                                                   		<text transform="matrix(1 0 0 1 564.0068 324.5004)" class="st10 st6 st7">People</text>
                                                   		<text transform="matrix(1 0 0 1 554.2568 257.2998)" class="st10 st6 st7">Purchases</text>
                                                   		<text transform="matrix(1 0 0 1 564.0068 207.2003)" class="st10 st6 st7">People</text>
                                                   		<text transform="matrix(1 0 0 1 554.2568 147.05)" class="st10 st6 st7">Purchases</text>
                                                   		<text transform="matrix(1 0 0 1 554.2568 37.2)" class="st10 st6 st7">Purchases</text>
                                                   		<text transform="matrix(1 0 0 1 564.0068 98.7002)" class="st10 st6 st7">People</text>
                                                   		<circle id="expenditureC_1_" class="st5" cx="441" cy="161.1" r="25.9"/>
                                                   		<circle id="expenditureC_2_" class="st5" cx="441" cy="268" r="25.9"/>
                                                   		<circle id="expenditureC_3_" class="st5" cx="441" cy="54.6" r="25.9"/>
                                                   		<text id="obtainRetainTotalPercentNum" transform="matrix(1 0 0 1 429.2834 166.0152)" class="st6 st7">00%</text>
                                                   		<text id="administrationTotalPercentNum" transform="matrix(1 0 0 1 428.2831 273.7303)" class="st6 st7">00%</text>
                                                   		<text id="makeBuyTotalPercentNum" transform="matrix(1 0 0 1 428.2834 58.6522)" class="st6 st7">00%</text>
                                                   		<text transform="matrix(1 0 0 1 402.3416 317.3)" class="st10 st6 st7">Run Business</text>
                                                   		<text transform="matrix(1 0 0 1 405.2591 206.2002)" class="st10 st6 st7">Ob/Retain C</text>
                                                   		<text transform="matrix(1 0 0 1 411.7764 99.6998)" class="st10 st6 st7">Make/Buy</text>
                                                   	</g>
                                                   </g>
                                                   </svg>


                                                </div>
                                            </div>
                                        </div>
                                    </div>

									<!-- a sample graph (currently using sample data) -->
									<div class="row">
										<div class="col-md-12">
											<div class="graph-container second">
												<div class="dashboard-graph " style="width:100%;height:auto;"></div>
											</div>
										</div>
									</div>



								</div>
								<?php } ?>
								
								<!-- side menu items -->
								<div class="col-xs-12 col-md-4"><button class="btn btn-primary btn-inverted btn-lg" <?php if($noData) { ?> data-action="changeData" data-page="menu" <?php } else {?> data-ma-action="navigate" data-nav-data="overview"<?php }?>><i class="zmdi zmdi-money"></i> Menu</button></div>
								
								<div class="col-xs-6 col-md-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="upload" ><i class="zmdi zmdi-upload"></i> Upload</button></div>
								<div class="col-xs-6 col-md-2 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="user-management"><i class="zmdi zmdi-account"></i> User Management</button></div>
								
								<div class="col-xs-6 col-md-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="report" ><i class="zmdi zmdi-collection-text"></i> P/L Reports</button></div>
								<div class="col-xs-6 col-md-2 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="report-bs" ><i class="zmdi zmdi-collection-item"></i> BS Reports</button></div>

								<div class="col-xs-6 col-md-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="mapdata" ><i class="zmdi zmdi-shuffle"></i> Map Data</button></div>
                                <div class="col-xs-6 col-md-2 right"><button class="btn btn-primary btn-inverted btn-lg" disabled="disabled"><i class="zmdi zmdi-card"></i> Coming soon</button></div>


								<div class="col-xs-6 col-md-2 left"><button class="btn btn-primary btn-inverted btn-lg" disabled="disabled"><i class="zmdi zmdi-directions"></i> Planning Assistant</button></div>
								<div class="col-xs-6 col-md-2 right"><button class="btn btn-primary btn-inverted btn-lg" disabled="disabled"><i class="zmdi zmdi-trending-up"></i> Forecasting Module</button></div>
							</div>
						</div>
						
						<div role="tabpanel" class="tab-pane <?php if($initialContent == 'menu') echo 'active'; ?>" id="overview">
							<!-- the menu content, linking to the layer screens -->
							<div class="row">
								<?php if(isset($layerSales) || $demo) { ?>
								<div class="col-xs-6 col-md-4 col-md-offset-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="sales"><i class="zmdi zmdi-shopping-cart"></i> <?php echo $GLOBALS['revenue_label']; ?> </button></div>
								<?php } if(isset($layerNetProfitLoss) || $demo) { ?>
								<div class="col-xs-6 col-md-4 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="netprofitloss"><i class="zmdi zmdi-money"></i> <?php echo $GLOBALS['netprofit_label']; ?> </button></div>
								<?php } if(isset($layerMakeBuy) || $demo) { ?>
								<div class="col-xs-6 col-md-4 col-md-offset-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="makebuy"><i class="zmdi zmdi-label"></i> <?php echo $GLOBALS['operations_label']; ?></button></div>
								<?php } if(isset($labelsGrossProfit) || $demo) { ?>
								<div class="col-xs-6 col-md-4 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="grossprofit"><i class="zmdi zmdi-money-box"></i> <?php echo $GLOBALS['grossprofit_label']; ?></button></div>
								<?php } if(isset($layerSelling) || $demo) { ?>
								<div class="col-xs-6 col-md-4 col-md-offset-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="selling"><i class="zmdi zmdi-receipt"></i> <?php echo $GLOBALS['sellingexp_label']; ?></button></div>
								<?php } if(isset($layerAdmin) || $demo) { ?>
								<div class="col-xs-6 col-md-4 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="administration"><i class="zmdi zmdi-assignment"></i> <?php echo $GLOBALS['administration_label']; ?></button></div>
								<?php } ?>
								
							</div>
						</div>
							
						<div role="tabpanel" class="tab-pane" id="user-management">
							 <div class="tab-content">
                                    		<div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2 content-panel">
                                    			<div class="panel-body">
                                    			    <ul class="nav nav-tabs">
                                                      <li class="active"><a data-toggle="tab" href="#userlist">User List</a></li>
                                                      <li><a data-toggle="tab" href="#organisationlist" >Organisation List</a></li>
                                                    </ul>
                                                    <!-- user management content -->
                                    	            <div id="content" class="tab-content">
                                    	              <div id="userlist" class="tab-pane fade in active">
                                    	                <h2 class="step">Current User List <button class="btn btn-primary pull-right m-r-30 waves-effect" data-toggle="modal" data-target="#addNewUser">Add new User</button></h2>
                                                        <!-- user list -->
                                                        <div id="usr_list">ff</div>
                                                        <table class="table user-management-list-table">
                                                           <tbody>

                                                                      <tr>
                                                                        <td class="middle">
                                                                          <div class="media">
                                                                            <div class="media-body">
                                                                              <h4 class="media-heading">Contact 1</h4>
                                                                              <address class="no-margin">contact1@sample.com</address>
                                                                            </div>
                                                                          </div>
                                                                        </td>
                                                                        <td width="100" class="middle">
                                                                          <div>
                                                                            <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit" onclick="simpleFunction(4)">
                                                                              <i class="glyphicon glyphicon-edit"></i>
                                                                            </a>
                                                                            <a href="#" class="btn btn-circle btn-default btn-xs icon-only-btn" title="Edit">
                                                                              <i class="glyphicon glyphicon-trash"></i>
                                                                            </a>
                                                                          </div>
                                                                        </td>
                                                                      </tr>

                                                            </tbody>
                                                        </table>
                                                    </div>

                                                     <div id="organisationlist" class="tab-pane fade">
                                                            <h2 class="step">Current Organisation List <button class="btn btn-primary pull-right m-r-30 waves-effect" data-toggle="modal" data-target="#addNewUser">Add new Organisation</button></h2>

                                                     </div>


                                                    </div>
                                                 </div>
                                             </div>
                                     </div>
						</div>
						
						<div role="tabpanel" class="tab-pane" id="template">
							<h2 class=""><span class="page-title">Page title</span></h2>
							
							<p>[ Content not yet defined - created from the blank template ]</p>
						</div>
						
						<!-- set up the tabs for the layer data -->
						<?php if(isset($labelsMakeBuy) || $demo) { ?>
						<div role="tabpanel" class="tab-pane" id="makebuy">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane has-alert active <?php if(isset($labelsMakeBuy) && hasAlert($labelsMakeBuy)) echo 'hasAlert'; ?>" id="makebuytotal">
									<?php
										drawFullLabel($labelsMakeBuy);
										drawGraphContainer('makebuy', 'total', false);
										drawCommentaryMini($labelsMakeBuy,'makebuy');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="makebuypurchases">
									<?php
										drawFullLabel($labelsMakeBuyPurchases);
										drawGraphContainer('makebuy', 'purchases',false);
										drawCommentaryMini($labelsMakeBuyPurchases,'makebuypurchases');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="makebuypeople">
									<?php
										drawFullLabel($labelsMakeBuyPeople);
										drawGraphContainer('makebuy', 'people',false);
										drawCommentaryMini($labelsMakeBuyPeople,'makebuypeople');
									?>
								</div>
							</div>
						</div>
						<?php } if(isset($layerNetProfitLoss) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="netprofitloss">
							<?php
								drawFullLabel($labelsNetProfitLoss);
								drawGraphContainer('netprofitloss', 'total', false);
								drawCommentaryMini($labelsNetProfitLoss,'netprofitloss');
							?>
						</div>
						<?php } if(isset($layerSales) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="sales">
							<?php
								drawFullLabel($labelsSales);
								drawGraphContainer('sales',  'total', false);
								drawCommentaryMini($labelsSales,'sales');
							?>
						</div>
						<?php } if(isset($labelsGrossProfit) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="grossprofit">
							<?php
								drawFullLabel($labelsGrossProfit);
                                drawGraphContainer('grossprofit', 'total', false);
                                drawCommentaryMini($labelsGrossProfit, 'grossprofit');
							?>
						</div>
						<?php } if(isset($labelsSelling) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="selling">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane has-alert active <?php if(isset($labelsSelling) && hasAlert($labelsSelling)) echo 'hasAlert'; ?>" id="sellingtotal">
									<?php
										drawFullLabel($labelsSelling);
										drawGraphContainer('selling', 'total', false);
										drawCommentaryMini($labelsSelling, 'selling');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="sellingpurchases">
									<?php
										drawFullLabel($labelsSellingPurchases);
										drawGraphContainer('selling', 'purchases', false);
										drawCommentaryMini($labelsSellingPurchases, 'sellingpurchases');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="sellingpeople">
									<?php
										drawFullLabel($labelsSellingPeople);
										drawGraphContainer('selling', 'people', false);
										drawCommentaryMini($labelsSellingPeople, 'sellingpeople');
									?>
								</div>
							</div>
						</div>
						<?php } if(isset($labelsAdministration) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="administration">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane has-alert active <?php if(isset($labelsAdministration) && hasAlert($labelsAdministration)) echo 'hasAlert'; ?>" id="administrationtotal">
									<?php
										drawFullLabel($labelsAdministration);
										drawGraphContainer('administration', 'total', false);
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="administrationpurchases">
									<?php
										drawFullLabel($labelsAdminPurchases);
										drawGraphContainer('administration', 'purchases', false);
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="administrationpeople">
									<?php
										drawFullLabel($labelsAdminPeople);
										drawGraphContainer('administration', 'people', false);
									?>
								</div>
							</div>
						</div>
						<?php } ?>
					</div>
                </div>
            </section>
        </section>
			
		<!-- footer include -->
		<?php 
			include("inc/footer.inc.php"); 
		?>
		<!-- end footer -->
		
		<script>
			<?php if($initialContent != '' && $initialContent != 'dashboard') { ?>
				// see if a page has been defined (in the URL) and show it
				navigateTo('<?php echo $initialContent; ?>');
			<?php } ?>
		</script>
	
	</body>
  </html>
<?php
	ob_end_flush();
?>