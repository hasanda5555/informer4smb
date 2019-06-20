<?php 

	// set up an object for the summary data
	$summaryData = new stdClass();
	
	$layersData = $xml->layers->layer;
	//echo '<pre>';
	//print_r($xml);
	//echo '</pre>';
	foreach($layersData as $layerData) {
		switch(strtolower($layerData['name'])) {
			case 'make/buy':
				
				foreach($layerData->labels as $labelsData) {
					switch (strtolower($labelsData['type'])) {
						case 'total':
							$labelsMakeBuy = $labelsData;
							
							foreach($labelsData->label as $labelData) {
								if($labelData->title == $GLOBALS['currentmonth']) {
									$summaryData->operations = $labelData->currentamount;
									$summaryData->operationsPercent = $labelData->percentchange;
									$summaryData->operationsTotalPercent = $labelData->currpercent;
									$summaryData->operationsIndicator = $labelData->indicator;

                                    foreach($labelData->charts->chart as $chart) {

                                        if($chart['type'] == "general") {
                                            // 05 Chart  Make-buy - Revenue Tracking
                                            $summaryData->chartMakeBuyRevenueTracking = json_encode($chart);
                                        }else if ($chart['type'] == "crossmarker"){
                                            // 07 Chart  Make-buy - Cross Marker
                                            $summaryData->chartMakeBuyCrossMarker = json_encode($chart);
                                        }
                                    }

									//break;
								} else if($labelData->title == $GLOBALS['longterm']) {

                                    foreach($labelData->charts->chart as $chart) {
                                        if($chart['type'] == "general") {
                                            // 06 Chart  Make-buy - Revenue Tracking S
                                            $summaryData->chartMakeBuyRevenueTrackingS = json_encode($chart);

                                        }
                                    }
                                    //break;
								}
							}
							break;
						case 'purchases':
							$labelsMakeBuyPurchases = $labelsData;

							foreach($labelsData->label as $labelData) {
                                if($labelData->title == $GLOBALS['currentmonth']) {
                                    $summaryData->operationsPurchasesPercent = $labelData->currpercent;
                                   // break;

                                   foreach($labelData->charts->chart as $chart) {

                                       if($chart['type'] == "general") {
                                           // 23 Chart  Make-buy Purchases - Revenue Tracking
                                           $summaryData->chartMakeBuyPurchasesRevenueTracking = json_encode($chart);
                                       }else if ($chart['type'] == "crossmarker"){
                                           // 25 Chart  Make-buy Purchases - Cross Marker
                                           $summaryData->chartMakeBuyPurchasesCrossMarker = json_encode($chart);
                                       }
                                   }
                                } else if($labelData->title == $GLOBALS['longterm']) {

                                    foreach($labelData->charts->chart as $chart) {
                                        if($chart['type'] == "general") {
                                            // 24 Chart  Make-buy Purchases - Revenue Tracking S
                                            $summaryData->chartMakeBuyPurchasesRevenueTrackingS = json_encode($chart);

                                        }
                                    }

                                }
                            }

							break;
						case 'people':
							$labelsMakeBuyPeople = $labelsData;

							foreach($labelsData->label as $labelData) {
                                if($labelData->title == $GLOBALS['currentmonth']) {
                                    $summaryData->operationsPeoplePercent = $labelData->currpercent;
                                    foreach($labelData->charts->chart as $chart) {

                                           if($chart['type'] == "general") {
                                               // 20 Chart  Make-buy People - Revenue Tracking
                                               $summaryData->chartMakeBuyPeopleRevenueTracking = json_encode($chart);
                                           }else if ($chart['type'] == "crossmarker"){
                                               // 22 Chart  Make-buy People - Cross Marker
                                               $summaryData->chartMakeBuyPeopleCrossMarker = json_encode($chart);
                                           }
                                    }
                                   // break;
                                } else if($labelData->title == $GLOBALS['longterm']) {

                                      foreach($labelData->charts->chart as $chart) {
                                          if($chart['type'] == "general") {
                                              // 21 Chart  Make-buy People - Revenue Tracking S
                                              $summaryData->chartMakeBuyPeopleRevenueTrackingS = json_encode($chart);

                                          }
                                      }
                                }
                            }

							break;
					}
				}
				break;
			case 'net profit/(loss)':
				$labelsNetProfitLoss = $layerData->labels;
				
				foreach($layerData->labels[0]->label as $labelData) {
					if($labelData->title == $GLOBALS['currentmonth']) {
						$summaryData->profit = $labelData->currentamount;
						$summaryData->profitPercent = $labelData->percentchange;
						$summaryData->profitTotalPercent = $labelData->currpercent;
						$summaryData->profitIndicator = $labelData->indicator;
						$summaryData->profitForecast=($labelData->cloudindicator*0.25);

						foreach($labelData->charts->chart as $chart) {

                            if($chart['type'] == "general") {
                                // xx Chart  Net Profit - Revenue Tracking
                                $summaryData->chartNetProfitRevenueTracking = json_encode($chart);
                            }else if ($chart['type'] == "crossmarker"){
                                // 29 - 03  Chart  Net Profit - Cross Marker
                                $summaryData->chartNetProfitCrossMarker = json_encode($chart);
                            }
                        }

					} else if ($labelData->title == $GLOBALS['ytd']) {
						$summaryData->profitYTD = $labelData->currentamount; 
						$summaryData->profitYTDPercent = $labelData->percentchange; 
						$summaryData->profitYTDIndicator = $labelData->indicator;
						$summaryData->profitYTDCloudindicator = $labelData->cloudindicator;
					} else if ($labelData->title == $GLOBALS['projection']) {
						$summaryData->profitProjection = $labelData->currentamount; 
						$summaryData->profitProjectionPercent = $labelData->percentchange; 
						$summaryData->profitProjectionIndicator = $labelData->indicator;
						$summaryData->profitProjectionForecast=($labelData->cloudindicator*0.10);
					} else if($labelData->title == $GLOBALS['longterm']) {

                         foreach($labelData->charts->chart as $chart) {
                             if($chart['type'] == "general") {
                                 // 01 - 28 Chart  Net Profit Purchases - Revenue Tracking S
                                 $summaryData->chartNetProfitRevenueTrackingS = json_encode($chart);

                             }
                         }

                    }
				}
				break;
			case 'obtain/retain customers':
				foreach($layerData->labels as $labelsData) {
					switch (strtolower($labelsData['type'])) {
						case 'total':
							$labelsSelling = $labelsData;
							
							foreach($labelsData->label as $labelData) {
								if($labelData->title == $GLOBALS['currentmonth']) {
									$summaryData->selling = $labelData->currentamount;
									$summaryData->sellingPercent = $labelData->percentchange;
									$summaryData->sellingTotalPercent = $labelData->currpercent;
									$summaryData->sellingIndicator = $labelData->indicator;

									foreach($labelData->charts->chart as $chart) {

                                        if($chart['type'] == "general") {
                                            // xx Chart  Obtain Retain Total - Revenue Tracking
                                            $summaryData->chartObtainRetainTotalRevenueTracking = json_encode($chart);
                                        }else if ($chart['type'] == "crossmarker"){
                                            // 09 Chart Obtain Retain Total - Cross Marker
                                            $summaryData->chartObtainRetainTotalCrossMarker = json_encode($chart);
                                        }
                                    }
									//break;
								} else if($labelData->title == $GLOBALS['longterm']) {

                                    foreach($labelData->charts->chart as $chart) {
                                        if($chart['type'] == "general") {
                                            // 08 Chart  Obtain Retain Total - Revenue Tracking S
                                            $summaryData->chartObtainRetainTotalRevenueTrackingS = json_encode($chart);

                                        }
                                    }

                                 }
							}
							break;
						case 'purchases':
							$labelsSellingPurchases = $labelsData;

							foreach($labelsData->label as $labelData) {
                                if($labelData->title == $GLOBALS['currentmonth']) {
                                    $summaryData->sellingPurchasesPercent = $labelData->currpercent;
                                    //break;

                                    foreach($labelData->charts->chart as $chart) {

                                        if($chart['type'] == "general") {
                                            // xx Chart  Obtain Retain Purchases - Revenue Tracking
                                            $summaryData->chartObtainRetainPurchasesRevenueTracking = json_encode($chart);
                                        }else if ($chart['type'] == "crossmarker"){
                                            // 13 Chart Obtain Retain Purchases - Cross Marker
                                            $summaryData->chartObtainRetainPurchasesCrossMarker = json_encode($chart);
                                        }
                                    }
                                } else if($labelData->title == $GLOBALS['longterm']) {

                                      foreach($labelData->charts->chart as $chart) {
                                          if($chart['type'] == "general") {
                                              // 12 Chart  Obtain Retain Purchases - Revenue Tracking S
                                              $summaryData->chartObtainRetainPurchasesRevenueTrackingS = json_encode($chart);

                                          }
                                      }

                                }
                            }

							break;
						case 'people':
							$labelsSellingPeople = $labelsData;

							foreach($labelsData->label as $labelData) {
                                if($labelData->title == $GLOBALS['currentmonth']) {
                                    $summaryData->sellingPeoplePercent = $labelData->currpercent;
                                    //break;

                                    foreach($labelData->charts->chart as $chart) {

                                        if($chart['type'] == "general") {
                                            // xx Chart  Obtain Retain People - Revenue Tracking
                                            $summaryData->chartObtainRetainPeopleRevenueTracking = json_encode($chart);
                                        }else if ($chart['type'] == "crossmarker"){
                                            // 11 Chart Obtain Retain People - Cross Marker
                                            $summaryData->chartObtainRetainPeopleCrossMarker = json_encode($chart);
                                        }
                                    }
                                } else if($labelData->title == $GLOBALS['longterm']) {

                                    foreach($labelData->charts->chart as $chart) {
                                        if($chart['type'] == "general") {
                                            // 10 Chart  Obtain Retain Purchases - Revenue Tracking S
                                            $summaryData->chartObtainRetainPeopleRevenueTrackingS = json_encode($chart);

                                        }
                                    }

                                }
                            }
						break;
					}
				}
				break;
			case 'sales':
				$labelsSales = $layerData->labels;
				
				foreach($layerData->labels[0]->label as $labelData) {
					if($labelData->title == $GLOBALS['currentmonth']) {
						$summaryData->revenue = $labelData->currentamount;
						$summaryData->revenuePercent = $labelData->percentchange;
						$summaryData->revenueIndicator = $labelData->indicator;
						$summaryData->revenueForecast=(	$labelData->cloudindicator*0.35);

						foreach($labelData->charts->chart as $chart) {

                            if($chart['type'] == "general") {
                                // xx Chart  Revenue - Revenue Tracking
                                $summaryData->chartRevenueRevenueTracking = json_encode($chart);
                            }else if ($chart['type'] == "crossmarker"){
                                // 04 - 02 Chart Revenue - Cross Marker
                                $summaryData->chartRevenueCrossMarker = json_encode($chart);
                            }
                        }

					} else if ($labelData->title == $GLOBALS['ytd']) {
						$summaryData->revenueYTD = $labelData->currentamount;
						$summaryData->revenueYTDPercent = $labelData->percentchange;
						$summaryData->revenueYTDIndicator = $labelData->indicator;
					} else if ($labelData->title == $GLOBALS['projection']) {
						$summaryData->revenueProjection = $labelData->currentamount;
						$summaryData->revenueProjectionPercent = $labelData->percentchange;
						$summaryData->revenueProjectionIndicator = $labelData->indicator;
					} else if($labelData->title == $GLOBALS['longterm']) {

                         foreach($labelData->charts->chart as $chart) {
                             if($chart['type'] == "general") {
                                 // xx Chart  Revenue - Revenue Tracking S
                                 $summaryData->chartRevenueRevenueTrackingS = json_encode($chart);

                             }
                         }

                    }
				}
				break;
			case 'gross profit':
				$labelsGrossProfit = $layerData->labels;
				foreach($labelsData->label as $labelData) {
                    if($labelData->title == $GLOBALS['currentmonth']) {

                        foreach($labelData->charts->chart as $chart) {

                            if($chart['type'] == "general") {
                                // xx Chart  Gross Profit Total - Revenue Tracking
                                $summaryData->chartGrossProfitTotalRevenueTracking = json_encode($chart);
                            }else if ($chart['type'] == "crossmarker"){
                                // 27 Chart Gross Profit Total - Cross Marker
                                $summaryData->chartGrossProfitTotalCrossMarker = json_encode($chart);
                            }
                        }
                    } else if($labelData->title == $GLOBALS['longterm']) {

                          foreach($labelData->charts->chart as $chart) {
                              if($chart['type'] == "general") {
                                  // 26 Chart  Gross Profit Total - Revenue Tracking S
                                  $summaryData->chartGrossProfitTotalRevenueTrackingS = json_encode($chart);

                              }
                          }

                    }
                }
				break;
			case 'administration':
				foreach($layerData->labels as $labelsData) {
					switch (strtolower($labelsData['type'])) {
						case 'total':
							$labelsAdministration = $labelsData;
							
							foreach($labelsData->label as $labelData) {
								if($labelData->title == $GLOBALS['currentmonth']) {
									$summaryData->admin = $labelData->currentamount;
									$summaryData->adminPercent = $labelData->percentchange;
									$summaryData->adminTotalPercent = $labelData->currpercent;
									$summaryData->adminIndicator = $labelData->indicator;
									
									//break;
									foreach($labelData->charts->chart as $chart) {

                                        if($chart['type'] == "general") {
                                            // xx Chart Running Bus Total - Revenue Tracking
                                            $summaryData->chartRunningBusTotalRevenueTracking = json_encode($chart);
                                        }else if ($chart['type'] == "crossmarker"){
                                            // 15 Chart Running Bus Total - Cross Marker
                                            $summaryData->chartRunningBusTotalCrossMarker = json_encode($chart);
                                        }
                                    }

								} else if($labelData->title == $GLOBALS['longterm']) {

                                   foreach($labelData->charts->chart as $chart) {
                                       if($chart['type'] == "general") {
                                           // 14 Chart Running Bus Total - Revenue Tracking S
                                           $summaryData->chartRunningBusTotalRevenueTrackingS = json_encode($chart);

                                       }
                                   }

                                }
							}
						    break;
						case 'purchases':
							$labelsAdminPurchases = $labelsData;

							foreach($labelsData->label as $labelData) {
                                if($labelData->title == $GLOBALS['currentmonth']) {
                                    $summaryData->adminPurchasesPercent = $labelData->currpercent;

                                    foreach($labelData->charts->chart as $chart) {

                                        if($chart['type'] == "general") {
                                            // xx Chart Running Bus Purchases - Revenue Tracking
                                            $summaryData->chartRunningBusPurchasesRevenueTracking = json_encode($chart);
                                        }else if ($chart['type'] == "crossmarker"){
                                            // 19 Chart Running Bus Purchases - Cross Marker
                                            $summaryData->chartRunningBusPurchasesCrossMarker = json_encode($chart);
                                        }
                                    }

                                    //break;
                                } else if($labelData->title == $GLOBALS['longterm']) {

                                    foreach($labelData->charts->chart as $chart) {
                                        if($chart['type'] == "general") {
                                            // 18 Chart Running Bus Purchases - Revenue Tracking S
                                            $summaryData->chartRunningBusPurchasesRevenueTrackingS = json_encode($chart);

                                        }
                                    }

                                }
                            }

						    break;
						case 'people':
							$labelsAdminPeople = $labelsData;

							foreach($labelsData->label as $labelData) {
                                if($labelData->title == $GLOBALS['currentmonth']) {
                                    $summaryData->adminPeoplePercent = $labelData->currpercent;

                                    foreach($labelData->charts->chart as $chart) {

                                        if($chart['type'] == "general") {
                                            // xx Chart Running Bus People - Revenue Tracking
                                            $summaryData->chartRunningBusPeopleRevenueTracking = json_encode($chart);
                                        }else if ($chart['type'] == "crossmarker"){
                                            // 17 Chart Running Bus People - Cross Marker
                                            $summaryData->chartRunningBusPeopleCrossMarker = json_encode($chart);
                                        }
                                    }
                                    //break;
                                } else if($labelData->title == $GLOBALS['longterm']) {

                                    foreach($labelData->charts->chart as $chart) {
                                         if($chart['type'] == "general") {
                                              // 16 Chart Running Bus People - Revenue Tracking S
                                              $summaryData->chartRunningBusPeopleRevenueTrackingS = json_encode($chart);

                                         }
                                    }

                                }
                            }

						break;
					}
				}
				break;
		}
	}
	
	// work out percentage for YTD
	if(isset($summaryData->profitYTD) && isset($summaryData->revenueYTD)) {
		$summaryData->percentYTD = $summaryData->profitYTD / $summaryData->revenueYTD * 100;
		$summaryData->percentYTDIndicator = $summaryData->percentYTD < 0 ? -3 : $summaryData->profitYTDIndicator;
		$summaryData->percentYTDCloudindicator = $summaryData->percentYTD < 0 ? 7 : $summaryData->profitYTDCloudindicator;
		$summaryData->forecastYTD=($summaryData->percentYTDCloudindicator*0.20);
	} else {
		$summaryData->percentYTD = 0;
		$summaryData->percentYTDIndicator = 0;
		$summaryData->forecastYTD=0;
	}
	
	// work out percentage for Projection
	if(isset($summaryData->profitProjection) && isset($summaryData->revenueProjection)) {
		$summaryData->percentProjection = $summaryData->profitProjection / $summaryData->revenueProjection * 100;
		$summaryData->percentProjectionIndicator = $summaryData->percentProjection < 0 ? -3 : $summaryData->profitProjectionIndicator;
		$summaryData->forecastProjection=($labelData->cloudindicator*0.10);
	} else {
		$summaryData->percentProjection = 0;
		$summaryData->percentProjectionIndicator = 0;
		$summaryData->forecastProjection=0;
	}
	
	$summaryData->totalForecast=round(($summaryData->forecastProjection+$summaryData->forecastYTD+$summaryData->profitForecast+$summaryData->revenueForecast+$summaryData->profitProjectionForecast)/5);


?>