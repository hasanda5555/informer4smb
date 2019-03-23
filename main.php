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
		?>
		
		<script>
			var userid = <?php echo $_SESSION['userid']; ?>
		</script>
    </head>

    <body class="">
		<?php 
			// CHECK PERMISSIONS
			if ($permissions['Reports'] == 0) {
				echo $GLOBALS['accesserrormsg']; 
				die;
			}
		?>
		
		<?php 
			include("inc/layer-data.inc.php"); 
			include("inc/navbar.inc.php"); 
		?>
		
		<section id="main">
           <section id="content">
                <div class="container">
                    <div class="tab-content">
						<div role="tabpanel" class="tab-pane <?php if($initialContent == 'dashboard') echo 'active'; ?>" id="dashboard">
							<div class="row">
								<?php if(!$noData) { ?>
								<!-- show the summary data -->
								<div class="col-xs-12 col-md-8">
									<div class="row clouds">
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
										<table class="layer-table disabled">
											<tr class="title-row">
												<td colspan="4">
													Cash Flow
												</td>
											</tr>
											<tr class="number-row">
												<td class="secondary-cell" colspan="3">
													<span class="number">Coming soon...</span>
												</td>
											</tr>
											<tr class="bottom-row">
												<td colspan="4"></td>
											</tr>
										</table>
										<div class="layer-spacer"></div>
									</div>
									
									<!-- a sample graph (currently using sample data) -->
									<div class="row">
										<div class="col-md-12">
											<div class="graph-container">
												<div class="dashboard-graph" style="width:100%;height:150px;"></div>
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
                                <div class="col-xs-6 col-md-2 right"><button class="btn btn-primary btn-inverted btn-lg" disabled="disabled"><i class="zmdi zmdi-card"></i> Comming soon</button></div>


								<div class="col-xs-6 col-md-2 left"><button class="btn btn-primary btn-inverted btn-lg" disabled="disabled"><i class="zmdi zmdi-directions"></i> Planning Assistant</button></div>
								<div class="col-xs-6 col-md-2 right"><button class="btn btn-primary btn-inverted btn-lg" disabled="disabled"><i class="zmdi zmdi-trending-up"></i> Forecasting Module</button></div>
							</div>
						</div>
						
						<div role="tabpanel" class="tab-pane <?php if($initialContent == 'menu') echo 'active'; ?>" id="overview">
							<!-- the menu content, linking to the layer screens -->
							<div class="row">
								<?php if(isset($layerSales) || $demo) { ?>
								<div class="col-xs-6 col-md-4 col-md-offset-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="sales"><i class="zmdi zmdi-shopping-cart"></i> Revenue</button></div>
								<?php } if(isset($layerNetProfitLoss) || $demo) { ?>
								<div class="col-xs-6 col-md-4 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="netprofitloss"><i class="zmdi zmdi-money"></i> Net Profit/(Loss)</button></div>
								<?php } if(isset($layerMakeBuy) || $demo) { ?>
								<div class="col-xs-6 col-md-4 col-md-offset-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="makebuy"><i class="zmdi zmdi-label"></i> Operations</button></div>
								<?php } if(isset($labelsGrossProfit) || $demo) { ?>
								<div class="col-xs-6 col-md-4 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="grossprofit"><i class="zmdi zmdi-money-box"></i> Gross Profit</button></div>
								<?php } if(isset($layerSelling) || $demo) { ?>
								<div class="col-xs-6 col-md-4 col-md-offset-2 left"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="selling"><i class="zmdi zmdi-receipt"></i> Selling Exp</button></div>
								<?php } if(isset($layerAdmin) || $demo) { ?>
								<div class="col-xs-6 col-md-4 right"><button class="btn btn-primary btn-inverted btn-lg" data-ma-action="navigate" data-nav-data="administration"><i class="zmdi zmdi-assignment"></i> Administration</button></div>
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
									//	drawGraphContainer('makebuy', 'total', false);
										drawCommentaryMini($labelsMakeBuy,'makebuy');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="makebuypurchases">
									<?php
										drawFullLabel($labelsMakeBuyPurchases);
									//	drawGraphContainer('makebuy', 'purchases');
										drawCommentaryMini($labelsMakeBuyPurchases,'makebuypurchases');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="makebuypeople">
									<?php
										drawFullLabel($labelsMakeBuyPeople);
									//	drawGraphContainer('makebuy', 'people');
										drawCommentaryMini($labelsMakeBuyPeople,'makebuypeople');
									?>
								</div>
							</div>
						</div>
						<?php } if(isset($layerNetProfitLoss) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="netprofitloss">
							<?php
								drawFullLabel($labelsNetProfitLoss);
							//	drawGraphContainer('netprofitloss', 'total', false);
								drawCommentaryMini($labelsNetProfitLoss,'netprofitloss');
							?>
						</div>
						<?php } if(isset($layerSales) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="sales">
							<?php
								drawFullLabel($labelsSales);
							//	drawGraphContainer('sales',  'total', false);
								drawCommentaryMini($labelsSales,'sales');
							?>
						</div>
						<?php } if(isset($labelsGrossProfit) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="grossprofit">
							<?php
								drawFullLabel($labelsGrossProfit);
                              //  drawGraphContainer('grossprofit', 'total', false);
                                drawCommentaryMini($labelsGrossProfit, 'grossprofit');
							?>
						</div>
						<?php } if(isset($labelsSelling) || $demo) { ?>
						<div role="tabpanel" class="tab-pane has-alert" id="selling">
							<div class="tab-content">
								<div role="tabpanel" class="tab-pane has-alert active <?php if(isset($labelsSelling) && hasAlert($labelsSelling)) echo 'hasAlert'; ?>" id="sellingtotal">
									<?php
										drawFullLabel($labelsSelling);
										drawCommentaryMini($labelsSelling, 'selling');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="sellingpurchases">
									<?php
										drawFullLabel($labelsSellingPurchases);
									//	drawGraphContainer('sellingtotal', 'purchases');
										drawCommentaryMini($labelsSellingPurchases, 'sellingpurchases');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="sellingpeople">
									<?php
										drawFullLabel($labelsSellingPeople);
									//	drawGraphContainer('sellingtotal', 'people');
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
									//	drawGraphContainer('administrationtotal', 'total', false);
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="administrationpurchases">
									<?php
										drawFullLabel($labelsAdminPurchases);
										//drawGraphContainer('administrationtotal', 'purchases');
									?>
								</div>
								
								<div role="tabpanel" class="tab-pane has-alert" id="administrationpeople">
									<?php
										drawFullLabel($labelsAdminPeople);
									//	drawGraphContainer('administrationtotal', 'people');
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