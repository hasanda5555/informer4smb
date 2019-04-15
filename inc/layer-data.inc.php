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
								if($labelData->title == 'Current Month') {
									$summaryData->operations = $labelData->currentamount;
									$summaryData->operationsPercent = $labelData->percentchange;
									$summaryData->operationsIndicator = $labelData->indicator;
									
									break;
								}
							}
							break;
						case 'purchases':
							$labelsMakeBuyPurchases = $labelsData;
							break;
						case 'people':
							$labelsMakeBuyPeople = $labelsData;
							break;
					}
				}
				break;
			case 'net profit/(loss)':
				$labelsNetProfitLoss = $layerData->labels;
				
				foreach($layerData->labels[0]->label as $labelData) {
					if($labelData->title == 'Current Month') {
						$summaryData->profit = $labelData->currentamount;
						$summaryData->profitPercent = $labelData->percentchange;
						$summaryData->profitIndicator = $labelData->indicator;
					} else if ($labelData->title == 'Year to Date') {
						$summaryData->profitYTD = $labelData->currentamount; 
						$summaryData->profitYTDPercent = $labelData->percentchange; 
						$summaryData->profitYTDIndicator = $labelData->indicator;
					} else if ($labelData->title == 'Projection') {
						$summaryData->profitProjection = $labelData->currentamount; 
						$summaryData->profitProjectionPercent = $labelData->percentchange; 
						$summaryData->profitProjectionIndicator = $labelData->indicator;
					}
				}
				break;
			case 'obtain/retain customers':
				foreach($layerData->labels as $labelsData) {
					switch (strtolower($labelsData['type'])) {
						case 'total':
							$labelsSelling = $labelsData;
							
							foreach($labelsData->label as $labelData) {
								if($labelData->title == 'Current Month') {
									$summaryData->selling = $labelData->currentamount;
									$summaryData->sellingPercent = $labelData->percentchange;
									$summaryData->sellingIndicator = $labelData->indicator;
									
									break;
								}
							}
							break;
						case 'purchases':
							$labelsSellingPurchases = $labelsData;
							break;
						case 'people':
							$labelsSellingPeople = $labelsData;
							break;
					}
				}
				break;
			case 'sales':
				$labelsSales = $layerData->labels;
				
				foreach($layerData->labels[0]->label as $labelData) {
					if($labelData->title == 'Current Month') {
						$summaryData->revenue = $labelData->currentamount;
						$summaryData->revenuePercent = $labelData->percentchange;
						$summaryData->revenueIndicator = $labelData->indicator;
					} else if ($labelData->title == 'Year to Date') {
						$summaryData->revenueYTD = $labelData->currentamount;
						$summaryData->revenueYTDPercent = $labelData->percentchange;
						$summaryData->revenueYTDIndicator = $labelData->indicator;
					} else if ($labelData->title == 'Projection') {
						$summaryData->revenueProjection = $labelData->currentamount;
						$summaryData->revenueProjectionPercent = $labelData->percentchange;
						$summaryData->revenueProjectionIndicator = $labelData->indicator;
					}
				}
				break;
			case 'gross profit':
				$labelsGrossProfit = $layerData->labels;
				break;
			case 'administration':
				foreach($layerData->labels as $labelsData) {
					switch (strtolower($labelsData['type'])) {
						case 'total':
							$labelsAdministration = $labelsData;
							
							foreach($labelsData->label as $labelData) {
								if($labelData->title == 'Current Month') {
									$summaryData->admin = $labelData->currentamount;
									$summaryData->adminPercent = $labelData->percentchange;
									$summaryData->adminIndicator = $labelData->indicator;
									
									break;
								}
							}
							break;
						case 'purchases':
							$labelsAdminPurchases = $labelsData;
							break;
						case 'people':
							$labelsAdminPeople = $labelsData;
							break;
					}
				}
				break;
		}
	}
	
	// work out percentage for YTD
	if(isset($summaryData->profitYTD) && isset($summaryData->revenueYTD)) {
		$summaryData->percentYTD = $summaryData->profitYTD / $summaryData->revenueYTD * 100;
		$summaryData->percentYTDIndicator = $summaryData->percentYTD < 0 ? 7 : 1;
	} else {
		$summaryData->percentYTD = 0;
		$summaryData->percentYTDIndicator = 0;
	}
	
	// work out percentage for Projection
	if(isset($summaryData->profitProjection) && isset($summaryData->revenueProjection)) {
		$summaryData->percentProjection = $summaryData->profitProjection / $summaryData->revenueProjection * 100;
		$summaryData->percentProjectionIndicator = $summaryData->percentProjection < 0 ? 7 : 1;
	} else {
		$summaryData->percentProjection = 0;
		$summaryData->percentProjectionIndicator = 0;
	}


?>