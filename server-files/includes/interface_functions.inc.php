<?php

function drawLabelSectionOld($label) {
	$title = $label->title;
	
	$col1Heading = $label->currmonth;
	$col1Value = prettifyDollarAmount($label->currentamount);
	$col1Sales = prettifyPercentSales($label->currpercent);
	$col1NegClass = $label->currentamount < 0 ? 'negative' : '';
	
	$col2Heading = $label->pastmonth;
	$col2Value = prettifyDollarAmount($label->pastamount);
	$col2Sales = prettifyPercentSales($label->pastpercent);
	$col2NegClass = $label->pastamount < 0 ? 'negative' : '';
	
	$col3Heading = '% change';
	$col3Value = prettifyPercent($label->percentchange, true);
	$col3NegClass = $label->percentchange < 0 ? 'negative' : '';
	
	$indicator = $label->indicator;
	if ($indicator > 2) {
		$alertClass = 'has-alert alert-5';
		$alertIcon = '<div class="alert-icon"><i class="zmdi zmdi-alert-octagon"></i></div>';
	} else if ($indicator > 1) {
		$alertClass = 'has-alert alert-3';
		$alertIcon = '<div class="alert-icon"><i class="zmdi zmdi-alert-triangle"></i></div>';		
	} else {
		$alertClass = '';
		$alertIcon = '';
	}
	
	if ($col3Value < 0) $percentIcon = '<i class="him-icon zmdi zmdi-caret-down"></i> ';
	else if ($col3Value > 0) $percentIcon = '<i class="him-icon zmdi zmdi-caret-up"></i> ';
	else $percentIcon = '';
	
	echo '<div class="card primary '.$alertClass.'">
			<div class="card-body card-padding">
				<div class="row">
					<div class="col-sm-12"><h3>'.$title.'</h3></div>
						<div class="col-sm-12">
							<div class="table-responsive">
								'.$alertIcon.'
								<div class="table-wrapper">
									<table class="figures table table-condensed table-bordered">
										<tr>
											<td class="col-sm-3 first">
												<span class="number '.$col1NegClass.'">'.$col1Value.'</span>
											</td>	
											<td class="col-sm-3">
												<span class="heading">LY</span>
												<span class="number '.$col2NegClass.'">'.$col2Value.'</span>
											</td>	
											<td class="col-sm-3">
												<span class="number '.$col3NegClass.'">'.$percentIcon.$col3Value.'</span>
											</td>
										</tr>
										<tr>
											<td class="col-sm-3">
												<span class="following">'.$col1Sales.'</span>
											</td>
											<td class="col-sm-3">
												<span class="following">'.$col2Sales.'</span>
											</td>
											<td class="col-sm-3">
												<span class="following">&nbsp;</span>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>';
}

function drawFullLabel($labels) {
	echo "<div class='col-md-8'>";
		
	// change the order of the individual labels (so current month is first)
	$orderedLabelData = array();
	foreach($labels->label as $labelData) {
		switch(strtolower($labelData->title)) {
			case 'current month':
				array_unshift($orderedLabelData, $labelData);
				break;
			default:
				array_push($orderedLabelData, $labelData);
		}
	}
	
	// draw the individual labels
	foreach($orderedLabelData as $orderedData) {
		drawLabelSection($orderedData); 
	}
	
	echo "</div>";
	
	// add commentary
	$commentary = $labels->commentary;
	echo "<div class='col-md-4 visible-md visible-lg commentary-panel'>";
	echo "<header id='sidebar-header'>
				<ul class='h-inner'>
					<li class='hi-logo'>
						<a href='#' disabled='disabled'>Commentary</a>
					</li>
				</ul>
			</header>
			<div class='commentary-body'>								
				<div class='generated-commentary'>$commentary</div>
						
				<textarea class='editable inline commentary'></textarea>
			</div>";
	
	echo "<div class='button-panel'><button class='btn-save btn btn-lg btn-primary waves-effect' type='submit'>Save</button></div>";
	echo "</div>";
}

function drawLayer($layer) {
	// change the order of the labels (so current month is first)
	$orderedLabelData = array();
	foreach($layer->labels->label as $labelData) {
		switch(strtolower($labelData->title)) {
			case 'current month':
				array_unshift($orderedLabelData, $labelData);
				break;
			default:
				array_push($orderedLabelData, $labelData);
		}
	
		//drawLabelSection($labelData); 
	}
	
	// draw the labels
	foreach($orderedLabelData as $orderedData) {
		drawLabelSection($orderedData); 
	}
}

function drawCommentary($layer) {
	$commentary = $layer->commentary;
	if (isset($commentary) && $commentary != '') {
		echo "<div class='generated-commentary'>$commentary</div>";
	}
}

function drawLabelSection($label) {
	global $currentPeriod;
	$isCurrMonth = false;
	
	$title = $label->title;
	if (isset($currentPeriod) && strtolower(trim($title)) == 'current month') {
		$title = $currentPeriod;
		$isCurrMonth = true;
	}
	
	$col1Heading = $label->currmonth;
	$col1Value = prettifyDollarAmount($label->currentamount);
	$col1Sales = prettifyPercentSales($label->currpercent);
	$col1NegClass = (float) $label->currentamount < 0 ? 'negative' : '';
	
	$col2Heading = $label->pastmonth;
	$col2Value = prettifyDollarAmount($label->pastamount);
	$col2Sales = prettifyPercentSales($label->pastpercent);
	$col2NegClass = (float) $label->pastamount < 0 ? 'negative' : '';
	
	$col3Heading = '% change';
	$col3Value = prettifyPercent($label->percentchange, true);
	$col3NegClass = (float) $label->percentchange < 0 ? 'negative' : '';
	
	$indicator = (float) $label->indicator;
	if ($indicator > 5) {
		$alertClass = 'has-alert alert-5';
		$alertIcon = '<img src="img/alert-icon.png" class="alert-icon"/>';	
	} else {
		$alertClass = '';
		$alertIcon = '';
	}
	
	$percentgeNum = (float) $label->percentchange;
	$percentIcon = '<img class="arrow-icon" src="img/arrow-'.($percentgeNum < 0 ? 'down' : 'up').'-'.$indicator.'.png"/>';
	//if ($indicator != 4 && $indicator != 0) $percentIcon = '<img class="arrow-icon" src="img/arrow-'.$indicator.'.png"/>';
	//else $percentIcon = '';
		
	echo 	'<table class="layer-table expense-overview '.$alertClass.' '.($isCurrMonth ? 'current-month' : '').'">
				<tr class="title-row">
					<td colspan="4">
						'.$title.'
					</td>
				</tr>
				<tr class="number-row">
					<td class="icon-cell">
						'.$alertIcon.'
					</td>';
	
	if(!strpos(strtolower($title), 'marker') && !strpos(strtolower($title), 'growth')) {
			echo	'<td class="primary-cell">
						<span class="number '.$col1NegClass.'">'.$col1Value.'</span>
						<span class="support '.((float) $col1Sales < 0 ? 'negative' : '').'">'.$col1Sales.'</span>
					</td>
					<td class="secondary-cell">
						<span class="support">LY</span>
						<span class="number '.$col2NegClass.'">'.$col2Value.'</span>
						<span class="support '.((float) $col2Sales < 0 ? 'negative' : '').'">'.$col2Sales.'</span>
					</td>
					<td class="tertiary-cell">
						<span class="number '.$col3NegClass.'">'.$percentIcon.$col3Value.'</span>
						<span class="support">&nbsp;</span>
					</td>';
	} else {
			/*
			echo	'<td class="primary-cell">
						<span class="number '.$col3NegClass.'">'.$percentIcon.$col3Value.'</span>
						<span class="support">Change</span>
					</td>
					<td class="secondary-cell">
						<span class="support">CY</span>
						<span class="number '.$col1NegClass.'">'.$col1Value.'</span>
						<span class="support '.((float) $col1Sales < 0 ? 'negative' : '').'">'.$col1Sales.'</span>
					</td>
					<td class="tertiary-cell">
						<span class="support">LY</span>
						<span class="number '.$col2NegClass.'">'.$col2Value.'</span>
						<span class="support '.((float) $col2Sales < 0 ? 'negative' : '').'">'.$col2Sales.'</span>
					</ td > ';
			*/		
			echo	'<td class="secondary-cell primary-width">
						<span class="number '.$col1NegClass.'">'.$col1Value.'</span>
						<span class="support '.((float) $col1Sales < 0 ? 'negative' : '').'">'.$col1Sales.'</span>
					</td>
					<td class="secondary-cell">
						<span class="support">LY</span>
						<span class="number '.$col2NegClass.'">'.$col2Value.'</span>
						<span class="support '.((float) $col2Sales < 0 ? 'negative' : '').'">'.$col2Sales.'</span>
					</td>
						<td class="primary-cell tertiary-cell">
						<span class="number '.$col3NegClass.'">'.$percentIcon.$col3Value.'</span>
						<span class="support">Change</span>
					</td>';
	}
			
	echo		'</tr>
				<tr class="bottom-row">
					<td colspan="4"></td>
				</tr>
			</table>
			<div class="layer-spacer"></div> ';
	
}

function hasAlert($labels) {
	foreach($labels->label as $labelData) {
		$indicator = $labelData->indicator;
		
		if ($indicator > 1) return true;
	}
	
	return false;
}

function getRandomAmount() {
	return rand(10, 180);
} 

function getRandomPercent() {
	return rand(50, 1000) / 10;
}

function prettifyDollarAmount($amount) {
	if (!isset($amount) || $amount == '') return '&nbsp;';
	
	if (abs($amount) >= 1000000) {
		$rounded = round($amount/1000000, 2);
		$prettyAmount = ($rounded < 0 ? '-' : '') . '$'.abs($rounded).'M';
	} else if (abs($amount) >= 1000) {
		$rounded = round($amount/1000);
		$prettyAmount = ($rounded < 0 ? '-' : '') . '$'.abs($rounded).'K';
	} else {
		$rounded = round($amount);
		$prettyAmount = ($rounded < 0 ? '-' : '') . '$'.abs($rounded);
	}

	return $prettyAmount;
}

function prettifyPercentSales($amount, $doABS) {
	if (!isset($amount) || $amount == '') return '&nbsp;';
	
	$amountNum = (float) $amount;
	if ($doABS) $amountNum = abs($amountNum);
	//return round($amountNum, 1) . '%';
	
	if (abs($amountNum) < 10) return round($amountNum, 2) . '%';
	else if (abs($amountNum) < 100) return round($amountNum, 1) . '%';
	else return round($amountNum, 0) . '%';
}

function prettifyPercent($amount, $doABS) {
	if (!isset($amount) || $amount == '') return '&nbsp;';
	
	$amountNum = (float) $amount;
	if ($doABS) $amountNum = abs($amountNum);
	
	if (abs($amountNum) < 10) return round($amountNum, 2) . '%';
	else if (abs($amountNum) < 100) return round($amountNum, 1) . '%';
	else return round($amountNum, 0) . '%';
}

?>