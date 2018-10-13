function navigateTo(navData) {
	//alert('navData: '+navData);
	
	var backTarget = 'overview';
	var pageTitle = '';
	var showCommentary = false;
	var targetPage = 'main.php';
	var extraParams = '';
	switch(navData) {
		case 'overview':
			pageTitle = 'Menu';
			backTarget = 'dashboard';
			break;
		case 'user-management':
			pageTitle = 'User Management';
			backTarget = 'dashboard';
			break;
		case 'netprofitloss':
			pageTitle = 'Net Profit/(Loss)';
			showCommentary = true;
			break;
		case 'sales':
			pageTitle = 'Revenue';
			showCommentary = true;
			break;
		case 'makebuy':
			pageTitle = 'Operations';
			showCommentary = true;
			break;
		case 'grossprofit':
			pageTitle = 'Gross Profit';
			showCommentary = true;
			break;
		case 'selling':
			pageTitle = 'Selling Exp';
			showCommentary = true;
			break;
		case 'administration':
			pageTitle = 'Administration';
			showCommentary = true;
			break;
		case 'upload': 
			targetPage = 'uploaddata.php';
			pageTitle = 'Upload';
			break;
		case 'report': 
			targetPage = 'reports_menu.php';
			pageTitle = 'Reports';
			break;
		case 'report-bs': 
			targetPage = 'reports_menu.php';
			pageTitle = 'Reports';
			extraParams = 'bs=1';
			break;
	} 
	
	// check the file path
	var pathname = window.location.pathname; 
	var pathParts = pathname.split('/');
	var filename = pathParts[pathParts.length - 1];
	
	if(targetPage != filename) {
		// redirect to the page
		var getParams = 'page='+navData;
		if((selectedCompany && selectedCompany!='') && (selectedMonth && selectedMonth!='')) {
			getParams += '&company='+encodeURI(selectedCompany)+'&month='+selectedMonth;
		} else {
			// get params from the current URL
			var searchString = window.location.search.substring(1);
			var urlVariables = searchString.split('&');
			
			for (var i = 0; i < urlVariables.length; i++) {
				var pName = urlVariables[i].split('=');
				if (pName[0] == 'page') continue;
				
				if(getParams != '') getParams += '&';
				getParams += urlVariables[i];
			}
		}
		
		if(extraParams != '') getParams += '&'+extraParams;
		window.location = targetPage+'?'+getParams;
	} else {
		// already on the right page
		$('header').removeClass().addClass(navData);
		$('body').attr('data-current', navData);
		
		// update back button
		$('.back-container a[data-ma-action="navigate"]').attr('data-nav-data', backTarget);
		
		if(showCommentary) $('.commentary-menu-item').removeClass('hidden');
		else $('.commentary-menu-item').addClass('hidden');
		
		// highlight any links to that tab/page as being active
		var navLinks = $('a[data-ma-action="navigate"], button[data-ma-action="navigate"]', '.main-menu');
		navLinks.parent().removeClass('active');
		
		var activeNavLinks = $('a[data-nav-data="'+navData+'"], button[data-nav-data="'+navData+'"]', '.main-menu');
		activeNavLinks.parent().addClass('active');
		
		// make sure any sidebars are closed
		$('body').removeClass('sidebar-toggled');
		$('.ma-backdrop').remove();
		$('.sidebar, .ma-trigger').removeClass('toggled');
		
		if(targetPage = 'main.php') {
			// already on main, so show correct content
			
			if($('#'+navData).length > 0) {
				// a tab for this data exists
				
				// hide all tabs
				$('.container > .tab-content > .tab-pane').removeClass('active');
				
				// show the data (as it wouldn't have changed since last shown)
				$('#'+navData ).addClass('active reshown');
				
				// update the title in the navBar
				if(navData == 'dashboard') {
					$('header .page-title, header .back-container').addClass('hidden');
					$('header .default-title').removeClass('hidden');
					
					$('#header').removeClass('hasPeriod');
				} else {
					$('header .default-title').addClass('hidden');
					$('header .back-container').removeClass('hidden');
					$('header .page-title').removeClass('hidden').html(pageTitle);
					 
					//if(currentPeriod && currentPeriod != '') $('#header').addClass('hasPeriod');
				}				
				
				// TODO: plot any graphs (works better when container is visible)
				plotGraph('makebuy', 'total');
			} else {
				// hide all tabs
				$('.tab-pane').removeClass('active');
				
				// create a tab for the content (just so we show something)
				var newTab = $('#template').clone();
				newTab.attr('id', navData)
					//.prepend('<h4>'+navData+'</h4>')
					.addClass('active');
					
				newTab.find('h2 .page-title').html(pageTitle);
					
				$('.container > .tab-content').append(newTab);
				
				$(".collapse").collapse("hide");
			}
		}
	} 
}

$(document).ready(function () {
	
	$('body').on('click', '[data-ma-action]', function (e) {
        e.preventDefault();

        var $this = $(this);
        var action = $(this).data('ma-action');

        switch (action) {
			
			// change the company & period
			case 'changeData':
				promptToChangeData();
				break;
			
			/*-------------------------------------------
                Tab/Page navigation
            ---------------------------------------------*/
            case 'navigate':
				var navData = $this.attr('data-nav-data');
				var pageTarget = $this.attr('data-href');
				
				if(pageTarget) {
					window.location = pageTarget;
					break;
				}
				
				navigateTo(navData);
				break;
				
            /*-------------------------------------------
                Sidebar Open/Close
            ---------------------------------------------*/
            case 'sidebar-open':
                var target = $this.data('ma-target');
                var backdrop = '<div data-ma-action="sidebar-close" class="ma-backdrop" />';
				
				if(target == '#sidebar-right') {
					// get the current active page
					var activePage = $('#content .container > .tab-content > .tab-pane.active');
					if($('.tab-pane.active', activePage).length > 0) activePage = $('.tab-pane.active', activePage);
					
					// copy the generated commentary to the side panel
					var generatedCommDiv = $('.generated-commentary', activePage);
					if(generatedCommDiv.length > 0) $('#sidebar-right .generated-commentary').html(generatedCommDiv.html());
					
					// TODO: set the content & action of the user commentary textarea
					
				}
				
                $('body').addClass('sidebar-toggled');
                $('#header, #header-alt, #main').append(backdrop);
                $this.addClass('toggled');
                $(target).addClass('toggled');
				
                break;

            case 'sidebar-close':
                $('body').removeClass('sidebar-toggled');
                $('.ma-backdrop').remove();
                $('.sidebar, .ma-trigger').removeClass('toggled')
				
                break;
				
			/*-------------------------------------------
                Mainmenu Submenu Toggle
            ---------------------------------------------*/
            case 'submenu-toggle':
                $this.next().slideToggle(200);
                $this.parent().toggleClass('toggled');

                break;

            /*-------------------------------------------
                Login Window Switch
            ---------------------------------------------*/
            case 'login-switch':
                var loginblock = $this.data('ma-block');
                var loginParent = $this.closest('.lc-block');

                loginParent.removeClass('toggled');

                setTimeout(function(){
                    $(loginblock).addClass('toggled');
                });

                break;
        }
    });
	
	// TEST - an example of changing the style by adding a class to 'body'
	$(".style-change").val(''); 
	$(".style-change").change(function (e) {
		$('body').removeClass().addClass($(this).val());
	});
});