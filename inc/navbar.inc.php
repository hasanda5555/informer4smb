<script>
	var selectedCompany = '<?php echo isset($_GET['company']) ? $_GET['company'] : ''; ?>';
	var selectedMonth = '<?php echo isset($_GET['month']) ? $_GET['month'] : ''; ?>';
</script>

<!-- commentary side panel -->
<aside id="sidebar-right" class="sidebar sidebar-alt sidebar-right c-overflow">
	<header id="sidebar-header" class="clearfix" data-ma-theme="">
		<ul class="h-inner">
			<li class="pull-right">
				<ul class="hi-menu">
					<li>
						<a href="" class=" ma-trigger" data-ma-action="sidebar-close" data-ma-target="#sidebar-right"><i class="him-icon zmdi zmdi-close"></i></a>
					</li>
				</ul>
			</li>
			
			<li class="hi-logo hidden-xs-">
				<a href="main.php" disabled="disabled">Commentary</a>
			</li>
		</ul>
	</header>
	
	<div class="sidebar-content">
		<div class="generated-commentary">
			<p>[ Commentary content not operational ]</p>
		</div>
		
		<textarea class="editable inline commentary"></textarea>
		
		<div class='button-panel'><button class='btn-save btn btn-lg btn-primary waves-effect' type='submit'>Save</button></div>
	</div>
</aside>

<!-- side menu -->
<aside id="sidebar-menu" class="sidebar sidebar-alt sidebar-right c-overflow">
	<header id="sidebar-header" class="clearfix" data-ma-theme="">
		<ul class="h-inner">			
			<li class="pull-right">
				<ul class="hi-menu">
					<li>
						<a href="" class=" ma-trigger" data-ma-action="sidebar-close" data-ma-target="#sidebar-right"><i class="him-icon zmdi zmdi-close"></i></a>
					</li>
				</ul>
			</li>
		</ul>
	</header>
	
	<div class="sidebar-content">
		<ul class="main-menu">
			<li><a href="#dashboard" data-ma-action="navigate" data-nav-data="dashboard">Summary</a></li>
			
			<?php if(!$noData) { ?>
			<li class="sub-menu">
				<a href="" data-ma-action="submenu-toggle"><i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-down hidden"></i> Menu</a>
				<ul>
					<?php if(isset($labelsSales) || $demo) { ?>
					<li><a href="#sales" data-ma-action="navigate" data-nav-data="sales">Revenue</a></li>
					<?php } if(isset($labelsNetProfitLoss) || $demo) { ?>
					<li><a href="#netprofitloss" data-ma-action="navigate" data-nav-data="netprofitloss">Net Profit/(Loss)</a></li>
					<?php } if(true || isset($labelsMakeBuy) || $demo) { ?>
					
					<li><a href="#makebuy" data-ma-action="navigate" data-nav-data="makebuy">Operations</a></li> <!---->
					<?php } if(isset($labelsGrossProfit) || $demo) { ?>
					<li><a href="#grossprofit" data-ma-action="navigate" data-nav-data="grossprofit">Gross Profit</a></li>
					<?php }	if(isset($labelsSelling) || $demo) { ?>
					<li><a href="#selling" data-ma-action="navigate" data-nav-data="selling">Selling Exp</a></li>
					<?php } if(isset($labelsAdmin) || $demo) { ?>
					<li><a href="#administration" data-ma-action="navigate" data-nav-data="administration">Administration</a></li>
					<?php } ?>
					
				</ul>
			</li>
			<?php } ?>
			
			<li><a href="#upload" data-ma-action="navigate" data-nav-data="upload">Upload</a></li>
			
			<hr/>
			
			<li><a href="#" data-action="changeData"><i class="zmdi zmdi-refresh"></i> Change data source</a></li>
			<li><a href="index.php" ><i class="zmdi zmdi-close-circle-o"></i> Log out</a></li>
		</ul>
				
		<!--  -->
		<hr/>
		Apply a different style:<br/>
		<select class="style-change">
			<option value="">Default</option>
			<option value="variant1">Example 1</option>
		</select>
		<br/>
		(Note: style reverted when navigating to/from 'upload' or 'reports'.)
	</div>
</aside>

<!-- logo & title -->
<div class="company-header">
	<div class="heading">
		<div class="logo-container">
			<img class="logo" src="img/MkIVlogo.png" />
		</div>
		<div class="text-container">
			<span class="title">Informer 4 SMB<sup>TM</sup></span>
			<span class="subtitle">Innovative Strategic Financial Markers</span>
		</div>
	</div>
	
	<div class="subheading">
		<div class="sub-container">
			<span class="company-name"><?php echo isset($companyname) ? $companyname : '&nbsp;'; ?></span>
			<span class="greeting"><?php echo isset($uname) ? 'Logged in as '.$uname : '&nbsp;'; ?></span>
		</div>
	</div>
</div>

<!-- main nav bar -->
<header id="header" class="clearfix" data-ma-theme="">
	<ul class="h-inner">
		<li class="pull-left "> 
			<ul class="hi-menu back-container <?php if($initialContent == '' || $initialContent == 'dashboard') echo 'hidden'; ?>">
				<li>
					<a href="" class=" ma-trigger" data-ma-action="navigate" data-nav-data="dashboard"><i class="him-icon zmdi zmdi-arrow-left"></i></a>
				</li>
			</ul>
		</li>
		
		<li class="pull-right">
			<ul class="hi-menu">
				<li class="commentary-menu-item hidden">
					<a href="" class=" ma-trigger" data-ma-action="sidebar-open" data-ma-target="#sidebar-right"><i class="him-icon zmdi zmdi-comment-text-alt"></i></a>
				</li>
				<li class="hi-trigger- ma-trigger" data-ma-action="sidebar-open" data-ma-target="#sidebar-menu">
					<div class="line-wrap">
						<div class="line top"></div>
						<div class="line center"></div>
						<div class="line bottom"></div>
					</div>
				</li>
			</ul>
		</li>
		
		<li class="hi-logo main hidden-xs-">
			<div class="default-title"> <?php echo (isset($defaultTitle) ? $defaultTitle : 'Summary'); ?> <?php echo (isset($currentPeriod) ? '- <span class="current-period">'.$currentPeriod .'</span>': ''); ?></div>
			<div class="page-title hidden"></div>
		</li>
		
	</ul>

	<!-- tabs -->
	<div class="makebuy-tabs" role="tabpanel">
		<ul class="tab-nav" role="tablist" data-tab-color="white">
			<li class="active">
				<a href="#makebuytotal" aria-controls="makebuytotal" role="tab" data-toggle="tab">Total</a>
			</li>
			<li>
				<a href="#makebuypurchases" aria-controls="makebuypurchases" role="tab" data-toggle="tab">Purchases</a>
			</li>
			<li>
				<a href="#makebuypeople" aria-controls="makebuypeople" role="tab" data-toggle="tab">People</a>
			</li>
		</ul>
	</div>
	
	<div class="administration-tabs" role="tabpanel">
		<ul class="tab-nav" role="tablist" data-tab-color="white">
			<li class="active">
				<a href="#administrationtotal" aria-controls="administrationtotal" role="tab" data-toggle="tab">Total</a>
			</li>
			<li>
				<a href="#administrationpurchases" aria-controls="administrationpurchases" role="tab" data-toggle="tab">Purchases</a>
			</li>
			<li>
				<a href="#administrationpeople" aria-controls="administrationpeople" role="tab" data-toggle="tab">People</a>
			</li>
		</ul>
	</div>
	
	<div class="selling-tabs" role="tabpanel">
		<ul class="tab-nav" role="tablist" data-tab-color="white">
			<li class="active">
				<a href="#sellingtotal" aria-controls="sellingtotal" role="tab" data-toggle="tab">Total</a>
			</li>
			<li>
				<a href="#sellingpurchases" aria-controls="sellingpurchases" role="tab" data-toggle="tab">Purchases</a>
			</li>
			<li>
				<a href="#sellingpeople" aria-controls="sellingpeople" role="tab" data-toggle="tab">People</a>
			</li>
		</ul>
	</div>
</header>

