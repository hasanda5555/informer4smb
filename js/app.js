var navTitle = '';

function navigateTo(navData) {
	//alert('navData: '+navData);
	
	var backTarget = 'overview';
	var pageTitle = '';
	var showCommentary = false;
	var targetPage = 'main.php';
	var extraParams = '';

    navTitle = navData ;

	switch(navData) {
		case 'overview':
			pageTitle = 'Menu';
			backTarget = 'dashboard';
			break;
		case 'user-management':
            targetPage = 'usermanagement.php';
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
			pageTitle = 'Make Buy';
			showCommentary = true;
			break;
		case 'grossprofit':
			pageTitle = 'Gross Profit';
			showCommentary = true;
			break;
		case 'selling':
			pageTitle = 'Obtain Retain Customers';
			showCommentary = true;
			break;
		case 'administration':
			pageTitle = 'Running Bus';
			showCommentary = true;
			break;
		case 'upload': 
			targetPage = 'uploaddata.php';
			pageTitle = 'Upload';
			break;
        case 'mapdata':
            targetPage = 'mapdata.php';
            pageTitle = 'Map Data';
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
        var getParams = 'page='+navData;
		// redirect to the page
        /* if(typeof navData != 'undefined'){
           getParams = 'page='+navData;
        }else {
           getParams = 'page=dashboard'
       }*/
        

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
				plotGraph(navData, 'total');
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
$(document).ready(function () {
    /*-------------------------------------------
        Sparkline - example charts (came with the template)
    ---------------------------------------------*/
    function sparklineBar(id, values, height, barWidth, barColor, barSpacing) {
        $('.'+id).sparkline(values, {
            type: 'bar',
            height: height,
            barWidth: barWidth,
            barColor: barColor,
            barSpacing: barSpacing
        })
    }
    
    function sparklineLine(id, values, width, height, lineColor, fillColor, lineWidth, maxSpotColor, minSpotColor, spotColor, spotRadius, hSpotColor, hLineColor) {
        $('.'+id).sparkline(values, {
            type: 'line',
            width: width,
            height: height,
            lineColor: lineColor,
            fillColor: fillColor,
            lineWidth: lineWidth,
            maxSpotColor: maxSpotColor,
            minSpotColor: minSpotColor,
            spotColor: spotColor,
            spotRadius: spotRadius,
            highlightSpotColor: hSpotColor,
            highlightLineColor: hLineColor
        });
    }
    
    function sparklinePie(id, values, width, height, sliceColors) {
        $('.'+id).sparkline(values, {
            type: 'pie',
            width: width,
            height: height,
            sliceColors: sliceColors,
            offset: 0,
            borderWidth: 0
        });
    }    
    
    /* Mini Chart - Bar Chart 1 */
    if ($('.stats-bar')[0]) {
        sparklineBar('stats-bar', [6,4,8,6,5,6,7,8,3,5,9,5,8,4], '35px', 3, '#fff', 2);
    }
    
    /* Mini Chart - Line Chart 1 */
    if ($('.stats-line')[0]) {
        sparklineLine('stats-line', [9,4,6,5,6,4,5,7,9,3,6,5], 68, 35, '#fff', 'rgba(0,0,0,0)', 1.25, 'rgba(255,255,255,0.4)', 'rgba(255,255,255,0.4)', 'rgba(255,255,255,0.4)', 3, '#fff', 'rgba(255,255,255,0.4)');
    }
    
    /* Mini Chart - Pie Chart 1 */
    if ($('.stats-pie')[0]) {
        sparklinePie('stats-pie', [20, 35, 30, 5], 45, 45, ['#fff', 'rgba(255,255,255,0.7)', 'rgba(255,255,255,0.4)', 'rgba(255,255,255,0.2)']);
    }

    /*-------------------------------------------
        Easy Pie Charts - example charts (came with the template)
    ---------------------------------------------*/
    function easyPieChart(id, trackColor, scaleColor, barColor, lineWidth, lineCap, size) {
        $('.'+id).easyPieChart({
            trackColor: trackColor,
            scaleColor: scaleColor,
            barColor: barColor,
            lineWidth: lineWidth,
            lineCap: lineCap,
            size: size
        });
    }
    
    /* Main Pie Chart */
    if ($('.main-pie')[0]) {
        easyPieChart('main-pie', 'rgba(255,255,255,0.2)', 'rgba(255,255,255,0)', 'rgba(255,255,255,0.7)', 2, 'butt', 148);
    }
});


// plot a graph for one of the layers
function plotGraph(page, section) {
	var graphName = section == '' ? '.'+page+'-graph' : '.'+page+'-'+section+'-graph';
    //var highchartCont = section == '' ? ''+page+'-graph' : ''+page+'-'+section+'-graph';
    var legendSmall = {
        align: 'center',
        verticalAlign: 'bottom',
        layout: 'horizontal'
    }
    console.log("graphName " + graphName);
    /*$.ajax({
        url: "../report.php",
        type: "get", //send it through get method
        data: {
            mode : 'charts',
            term : 'longterm',
            layer : 'summary',
            month : selectedMonth,
            comany : selectedCompany,
            type: "json"
        },
        success: function(response) {
            console.log(response);
        },
        error: function(xhr) {
        }
    });*/
    // plot chart Revenue Tracking
    var plotchartRevenueTracking = function (obj,chartid){
        Highcharts.setOptions({
            colors: ['#E87E2B', '#2287e8']
        });

        console.log(chartid, obj);
        var RevenueTrackingTemp = {
            "chartName": "Revenue Tracking",
            "Period": obj.months.split(","),
            "properties": {
                "line_1": {
                    "lineName": "Revenue",
                    "values": obj.line1.split(",").map(function (item) {
                        return parseFloat(item);
                    })
                },
                "line_2": {
                    "lineName": "Make Buy",
                    "values": obj.line2.split(",").map(function (item) {
                        return parseFloat(item);
                    })
                }
            }
        };

        setTimeout(function() {
            //Chart for Revenue Tracking
            Highcharts.chart({
                chart: {
                    renderTo: chartid,
                    zoomType: 'xy'
                },
                title: {
                    text: RevenueTrackingTemp.chartName
                },
                xAxis: [{
                    categories: RevenueTrackingTemp.Period,
                    crosshair: true
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value} $',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    title: {
                        text: RevenueTrackingTemp.properties.line_1.lineName,
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }, { // Secondary yAxis
                    title: {
                        text: RevenueTrackingTemp.properties.line_2.lineName,
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    labels: {
                        format: '{value} $',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    x: 120,
                    verticalAlign: 'bottom',
                    y: 100,
                    floating: true,
                    backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: RevenueTrackingTemp.properties.line_1.lineName,
                    type: 'line',
                    yAxis: 1,
                    data: RevenueTrackingTemp.properties.line_1.values,
                    tooltip: {
                        valueSuffix: ' $'
                    }

                }, {
                    name: RevenueTrackingTemp.properties.line_2.lineName,
                    type: 'line',
                    data: RevenueTrackingTemp.properties.line_2.values,
                    tooltip: {
                        valueSuffix: '$'
                    }
                }]
            });

        });

    }

    // plot chart Revenue Tracking S
    var plotchartRevenueTrackingS = function (obj,chartid){

        console.log(chartid, obj);
        var RevenueTrackingSTemp = {
            "chartName": "Revenue Tracking Growing and Slowing",
            "Period": obj.months.split(","),
            "properties": {
                "line_1": {
                    "lineName": "Revenue",
                    "values": obj.line1.split(",").map(function (item) {
                        return parseFloat(item);
                    })
                },
                "line_2": {
                    "lineName": "Make Buy",
                    "values": obj.line2.split(",").map(function (item) {
                        return parseFloat(item);
                    })
                }
            }
        };

        setTimeout(function() {
            //Chart for Revenue Tracking S
            Highcharts.chart({
                chart: {
                    renderTo: chartid
                },
                title: {
                    text: RevenueTrackingSTemp.chartName
                },
                xAxis: [{
                    categories: RevenueTrackingSTemp.Period
                }],
                yAxis: {
                    title: {
                        text: 'Revenue',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },

                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                series: [{
                    name: RevenueTrackingSTemp.properties.line_1.lineName,
                    color: Highcharts.getOptions().colors[1],
                    data: RevenueTrackingSTemp.properties.line_1.values
                }, {
                    name: RevenueTrackingSTemp.properties.line_2.lineName,
                    color: Highcharts.getOptions().colors[0],
                    data: RevenueTrackingSTemp.properties.line_2.values
                }],
                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 900
                        },
                        chartOptions: {
                            legend: {
                                layout: 'horizontal',
                                align: 'center',
                                verticalAlign: 'bottom'
                            }
                        }
                    }]
                }

            });
        });

    }

    // plot chart Cross Marker
    var plotchartCrossMarker = function (obj,chartid){

        console.log(chartid, obj);
        var CrossMakerTemp = {
            "chartName": "Cross Marker",
            "Period": obj.months.split(","),
            "properties": {
                "line_1": {
                    "lineName": "GOS Smooth",
                    "values": obj.line1.split(",").map(function (item) {
                        return parseFloat(item);
                    })
                },
                "line_2": {
                    "lineName": "FOS Smooth",
                    "values": obj.line2.split(",").map(function (item) {
                        return parseFloat(item);
                    })
                }
            }
        };

        setTimeout(function() {
            //Chart for Cross Marker
            Highcharts.chart({
                chart: {
                    renderTo: chartid
                },
                title: {
                    text: CrossMakerTemp.chartName
                },
                xAxis: [{
                    categories: CrossMakerTemp.Period
                }],
                yAxis: {
                    title: {
                        text: 'Revenue',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },

                    labels: {
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom'
                },
                series: [{
                    name: CrossMakerTemp.properties.line_1.lineName,
                    color: Highcharts.getOptions().colors[1],
                    data: CrossMakerTemp.properties.line_1.values
                }, {
                    name: CrossMakerTemp.properties.line_2.lineName,
                    color: Highcharts.getOptions().colors[0],
                    data: CrossMakerTemp.properties.line_2.values
                }]

            });

        });

    }

    if($(graphName)[0]) {

        var grapId =  graphName.substr(1);
        $(graphName).attr('id', grapId);

        var chartDataObjRevenueTracking = null;
        var chartDataObjRevenueTrackingS =  null;
        var chartDataObjCrossMaker = null;



        if( graphName == '.dashboard-graph') {
            console.log("dashboard chart set rendering");
            if (!$('#dashboard-netprofitloss-CrossMarker').length){
                $('#dashboard-graph').append('<div class="dash-chart-holder" id="dashboard-makebuy-total-RevenueTrackingS"></div>');
                $('#dashboard-graph').append('<div class="dash-chart-holder"  id="dashboard-selling-CrossMarker"></div>');
                $('#dashboard-graph').append('<div class="dash-chart-holder" id="dashboard-netprofitloss-CrossMarker"></div>');
                plotchartRevenueTracking(chartNetProfitRevenueTrackingS_01, 'dashboard-makebuy-total-RevenueTrackingS');
                plotchartCrossMarker(chartRevenueCrossMarker_04, 'dashboard-selling-CrossMarker');
                plotchartCrossMarker(chartNetProfitCrossMarker_03, 'dashboard-netprofitloss-CrossMarker');
            }
        }

        if (graphName ==  ".sales-total-graph") {
            console.log("sales chart set rendering");
            if (!$('#sales-total-CrossMarker').length) {
                $('.sales-total-graph').attr('id', 'sales-total-graph');
                //$('#sales-total-graph').append('<div id="sales-total-RevenueTracking"></div>');
                //$('#sales-total-graph').append('<div id="sales-total-RevenueTrackingS"></div>');
                $('#sales-total-graph').append('<div id="sales-total-CrossMarker"></div>');
                plotchartCrossMarker(chartRevenueCrossMarker_04, 'sales-total-CrossMarker');
            }
        }

        if (graphName ==  ".netprofitloss-total-graph") {
            console.log("netprofitloss chart set rendering");
            if (!$('#netprofitloss-total-CrossMarker').length) {
                $('.netprofitloss-total-graph').attr('id', 'netprofitloss-total-graph');
                //$('#netprofitloss-total-graph').append('<div id="netprofitloss-total-RevenueTracking"></div>');
                $('#netprofitloss-total-graph').append('<div id="netprofitloss-total-RevenueTrackingS"></div>');
                $('#netprofitloss-total-graph').append('<div id="netprofitloss-total-CrossMarker"></div>');
                plotchartRevenueTrackingS(chartNetProfitRevenueTrackingS_01, 'netprofitloss-total-RevenueTrackingS');
                plotchartCrossMarker(chartNetProfitCrossMarker_03, 'netprofitloss-total-CrossMarker');
            }
        }

        if (graphName ==  ".grossprofit-total-graph") {
            console.log("grossprofit chart set rendering");
            if (!$('#grossprofit-total-CrossMarker').length) {
                $('.grossprofit-total-graph').attr('id', 'grossprofit-total-graph');
                //$('#netprofitloss-total-graph').append('<div id="netprofitloss-total-RevenueTracking"></div>');
                $('#grossprofit-total-graph').append('<div id="grossprofit-total-RevenueTrackingS"></div>');
                $('#grossprofit-total-graph').append('<div id="grossprofit-total-CrossMarker"></div>');
                plotchartRevenueTrackingS(chartGrossProfitTotalRevenueTrackingS_26, 'grossprofit-total-RevenueTrackingS');
                plotchartRevenueTrackingS(chartGrossProfitTotalRevenueTrackingS_26, 'grossprofit-total-RevenueTrackingS');
                plotchartCrossMarker(chartGrossProfitTotalCrossMarker_27, 'grossprofit-total-CrossMarker');
            }
        }

        if (graphName ==  ".makebuy-total-graph") {
            console.log("make buy chart set rendering");
            if (!$('#makebuy-purchases-CrossMarker').length) {

                $('.makebuy-total-graph').attr('id', 'makebuy-total-graph');
                $('#makebuy-total-graph').append('<div id="makebuy-total-RevenueTracking"></div>');
                $('#makebuy-total-graph').append('<div id="makebuy-total-RevenueTrackingS"></div>');
                $('#makebuy-total-graph').append('<div id="makebuy-total-CrossMarker"></div>');
                plotchartRevenueTracking(chartMakeBuyRevenueTracking_05, 'makebuy-total-RevenueTracking');
                plotchartRevenueTrackingS(chartMakeBuyRevenueTrackingS_06, 'makebuy-total-RevenueTrackingS');
                plotchartCrossMarker(chartMakeBuyCrossMarker_07, 'makebuy-total-CrossMarker');

                $('.makebuy-people-graph').attr('id', 'makebuy-people-graph');
                $('#makebuy-people-graph').append('<div id="makebuy-people-RevenueTracking"></div>');
                $('#makebuy-people-graph').append('<div id="makebuy-people-RevenueTrackingS"></div>');
                $('#makebuy-people-graph').append('<div id="makebuy-people-CrossMarker"></div>');
                plotchartRevenueTracking(chartMakeBuyPeopleRevenueTracking_20, 'makebuy-people-RevenueTracking');
                plotchartRevenueTrackingS(chartMakeBuyPeopleRevenueTrackingS_21, 'makebuy-people-RevenueTrackingS');
                plotchartCrossMarker(chartMakeBuyPeopleCrossMarker_22, 'makebuy-people-CrossMarker');

                $('.makebuy-purchases-graph').attr('id', 'makebuy-purchases-graph');
                $('#makebuy-purchases-graph').append('<div id="makebuy-purchases-RevenueTracking"></div>');
                $('#makebuy-purchases-graph').append('<div id="makebuy-purchases-RevenueTrackingS"></div>');
                $('#makebuy-purchases-graph').append('<div id="makebuy-purchases-CrossMarker"></div>');
                plotchartRevenueTracking(chartMakeBuyPurchasesRevenueTracking_23, 'makebuy-purchases-RevenueTracking');
                plotchartRevenueTrackingS(chartMakeBuyPurchasesRevenueTrackingS_24, 'makebuy-purchases-RevenueTrackingS');
                plotchartCrossMarker(chartMakeBuyPurchasesCrossMarker_25, 'makebuy-purchases-CrossMarker');
            }

        }

        if(graphName == ".administration-total-graph"){
            console.log("administration chart set rendering");

                if (!$('#administration-purchases-CrossMarker').length){

                    $('.administration-total-graph').attr('id', 'administration-total-graph');
                    //$('#administration-total-graph').append('<div id="administration-total-RevenueTracking"></div>');
                    $('#administration-total-graph').append('<div id="administration-total-RevenueTrackingS"></div>');
                    $('#administration-total-graph').append('<div id="administration-total-CrossMarker"></div>');
                    //plotchartRevenueTracking(chartMakeBuyRevenueTracking_05,'administration-total-RevenueTracking');
                    plotchartRevenueTrackingS (chartRunningBusTotalRevenueTrackingS_14 ,'administration-total-RevenueTrackingS');
                    plotchartCrossMarker(chartRunningBusTotalCrossMarker_15,'administration-total-CrossMarker');

                    $('.administration-people-graph').attr('id', 'administration-people-graph');
                    //$('#administration-people-graph').append('<div id="administration-people-RevenueTracking"></div>');
                    $('#administration-people-graph').append('<div id="administration-people-RevenueTrackingS"></div>');
                    $('#administration-people-graph').append('<div id="administration-people-CrossMarker"></div>');
                    //plotchartRevenueTracking(chartMakeBuyPeopleRevenueTracking_20,'administration-people-RevenueTracking');
                    plotchartRevenueTrackingS (chartRunningBusPeopleRevenueTrackingS_16 ,'administration-people-RevenueTrackingS');
                    plotchartCrossMarker(chartRunningBusPeopleCrossMarker_17,'administration-people-CrossMarker');

                    $('.administration-purchases-graph').attr('id', 'administration-purchases-graph');
                    //$('#administration-purchases-graph').append('<div id="administration-purchases-RevenueTracking"></div>');
                    $('#administration-purchases-graph').append('<div id="administration-purchases-RevenueTrackingS"></div>');
                    $('#administration-purchases-graph').append('<div id="administration-purchases-CrossMarker"></div>');
                    //plotchartRevenueTracking(chartMakeBuyPurchasesRevenueTracking_23,'administration-purchases-RevenueTracking');
                    plotchartRevenueTrackingS (chartRunningBusPurchasesRevenueTrackingS_18 ,'administration-purchases-RevenueTrackingS');
                    plotchartCrossMarker(chartRunningBusPurchasesCrossMarker_19,'administration-purchases-CrossMarker');

                }

            }

        if(graphName == ".selling-total-graph"){
            console.log("selling chart set rendering");

            if (!$('#selling-purchases-CrossMarker').length) {
                $('.selling-total-graph').attr('id', 'selling-total-graph');
                //$('#selling-total-graph').append('<div id="selling-total-RevenueTracking"></div>');
                $('#selling-total-graph').append('<div id="selling-total-RevenueTrackingS"></div>');
                $('#selling-total-graph').append('<div id="selling-total-CrossMarker"></div>');
                //plotchartRevenueTracking(chartMakeBuyRevenueTracking_05,'selling-total-RevenueTracking');
                plotchartRevenueTrackingS(chartObtainRetainTotalRevenueTrackingS_08, 'selling-total-RevenueTrackingS');
                plotchartCrossMarker(chartObtainRetainTotalCrossMarker_09, 'selling-total-CrossMarker');

                $('.selling-people-graph').attr('id', 'selling-people-graph');
                //$('#selling-people-graph').append('<div id="selling-people-RevenueTracking"></div>');
                $('#selling-people-graph').append('<div id="selling-people-RevenueTrackingS"></div>');
                $('#selling-people-graph').append('<div id="selling-people-CrossMarker"></div>');
                //plotchartRevenueTracking(chartMakeBuyPeopleRevenueTracking_20,'selling-people-RevenueTracking');
                plotchartRevenueTrackingS(chartObtainRetainPeopleRevenueTrackingS_10, 'selling-people-RevenueTrackingS');
                plotchartCrossMarker(chartObtainRetainPeopleCrossMarker_11, 'selling-people-CrossMarker');

                $('.selling-purchases-graph').attr('id', 'selling-purchases-graph');
                //$('#selling-purchases-graph').append('<div id="selling-purchases-RevenueTracking"></div>');
                $('#selling-purchases-graph').append('<div id="selling-purchases-RevenueTrackingS"></div>');
                $('#selling-purchases-graph').append('<div id="selling-purchases-CrossMarker"></div>');
                //plotchartRevenueTracking(chartMakeBuyPurchasesRevenueTracking_23,'selling-purchases-RevenueTracking');
                plotchartRevenueTrackingS(chartObtainRetainPurchasesRevenueTrackingS_12, 'selling-purchases-RevenueTrackingS');
                plotchartCrossMarker(chartObtainRetainPurchasesCrossMarker_13, 'selling-purchases-CrossMarker');
            }
        }

        //console.log('Months ', JSON.parse(JSON.stringify(chartDataObj.months)));
        //console.log('Months ', chartDataObj.months.split(","));
       /* if (chartDataObjRevenueTracking) {

            var RevenueTrackingTemp = {
                "chartName": "Revenue Tracking",
                "Period": chartDataObjRevenueTracking.months.split(","),
                "properties": {
                    "line_1": {
                        "lineName": "Revenue",
                        "values": chartDataObjRevenueTracking.line1.split(",").map(function (item) {
                            return parseFloat(item);
                        })
                    },
                    "line_2": {
                        "lineName": "Make Buy",
                        "values": chartDataObjRevenueTracking.line2.split(",").map(function (item) {
                            return parseFloat(item);
                        })
                    }
                }
            };
        }

        if (chartDataObjRevenueTrackingS) {
            var RevenueTrackingSTemp = {
                "chartName": "Revenue Tracking Grown and Slowing",
                "Period": chartDataObjRevenueTrackingS.months.split(","),
                "properties": {
                    "line_1": {
                        "lineName": "Revenue",
                        "values": chartDataObjRevenueTrackingS.line1.split(",").map(function (item) {
                            return parseFloat(item);
                        })
                    },
                    "line_2": {
                        "lineName": "Make Buy",
                        "values": chartDataObjRevenueTrackingS.line2.split(",").map(function (item) {
                            return parseFloat(item);
                        })
                    }
                }
            };
        }
        if (chartDataObjCrossMaker) {

            var CrossMakerTemp = {
                "chartName": "Cross Marker",
                "Period": chartDataObjCrossMaker.months.split(","),
                "properties": {
                    "line_1": {
                        "lineName": "GOS Smooth",
                        "values": chartDataObjCrossMaker.line1.split(",").map(function (item) {
                            return parseFloat(item);
                        })
                    },
                    "line_2": {
                        "lineName": "FOS Smooth",
                        "values": chartDataObjCrossMaker.line2.split(",").map(function (item) {
                            return parseFloat(item);
                        })
                    }
                }
            };
        }
        */
       // var $chartcontainer = $('<div>').appendTo(document.body);



        setTimeout(function() {

            $("#revenueNum").text(Math.round(administrationTotalPercent + obtainRetainTotalPercent + makeBuyTotalPercent) + "%");
            $("#netProfitNum").text(Math.round(netProfitTotalPercent) + "%");

            $("#makeBuyTotalPercentNum").text(Math.round(makeBuyTotalPercent) + "%");
            $("#obtainRetainTotalPercentNum").text(Math.round(obtainRetainTotalPercent) + "%");
            $("#administrationTotalPercentNum").text(Math.round(administrationTotalPercent) + "%");

            $("#makeBuyPurchasesPercentNum").text(Math.round(makeBuyPurchasesPercent) + "%");
            $("#makeBuyPeoplePercentNum").text(Math.round(makeBuyPeoplePercent) + "%");
            $("#obtainRetainPurchasesPercentNum").text(Math.round(obtainRetainPurchasesPercent) + "%");
            $("#obtainRetainPeoplePercentNum").text(Math.round(obtainRetainPeoplePercent) + "%");
            $("#administrationPurchasesPercentNum").text(Math.round(administrationPurchasesPercent) + "%");
            $("#administrationPeoplePercentNum").text(Math.round(administrationPeoplePercent) + "%");

            if (Math.round(netProfitTotalPercent) < 0) {
                $(".profitline").css({stroke: "#E87E2B"});
            }


            // console.log(administrationTotalPercent + obtainRetainTotalPercent + makeBuyTotalPercent + netProfitTotalPercent );
            Highcharts.setOptions({
                colors: ['#E87E2B']
            });

            /*Highcharts.chart('cashFlowchartnull', {

                title: {
                    text: 'Cash Flow'
                },

                series: [{
                    keys: ['from', 'to', 'weight'],
                    data: [
                        ['Start', 'Revenue', (administrationTotalPercent + obtainRetainTotalPercent + makeBuyTotalPercent ) ],
                        ['Revenue','Expenditure',(administrationTotalPercent + obtainRetainTotalPercent + makeBuyTotalPercent )],
                        ['Expenditure','Make/Buy',makeBuyTotalPercent],
                        ['Make/Buy','Products MB',makeBuyPurchasesPercent],
                        ['Make/Buy','People MB',makeBuyPeoplePercent],
                        ['Expenditure','Ob/Retain Cust',obtainRetainTotalPercent],
                        ['Ob/Retain Cust','Products OB',obtainRetainPurchasesPercent],
                        ['Ob/Retain Cust','People OB',obtainRetainPeoplePercent],
                        ['Expenditure','Running Bus',administrationTotalPercent],
                        ['Running Bus','Products E',administrationPurchasesPercent],
                        ['Running Bus','People E',administrationPeoplePercent],
                        ['Revenue','Net profit',netProfitTotalPercent]
                    ],
                    type: 'sankey',
                    name: 'Cash Flow'
                }]

            });*/

            Highcharts.setOptions({
                colors: ['#E87E2B', '#2287e8']
            });

               /*if (graphName ==  ".makebuy-total-graph"){
                    $(graphName).append('<div id="'+grapId+'-RevenueTracking"></div>');
                    $(graphName).append('<div id="'+grapId+'-RevenueTrackingS"></div>');
                    $(graphName).append('<div id="'+grapId+'-CrossMarker"></div>');

               }*/

                /*if (graphName ==  ".makebuy-people-graph"){
                    $(graphName).append('<div id="'+grapId+'-RevenueTracking"></div>');
                    plotchartSingleAx ('hello');
                    $(graphName).append('<div id="'+grapId+'-RevenueTrackingS"></div>');
                    $(graphName).append('<div id="'+grapId+'-CrossMarker"></div>');

                }

                if (graphName ==  ".makebuy-people-graph"){
                    $(graphName).append('<div id="'+grapId+'-RevenueTracking"></div>');
                    plotchartSingleAx ('hello');
                    $(graphName).append('<div id="'+grapId+'-RevenueTrackingS"></div>');
                    $(graphName).append('<div id="'+grapId+'-CrossMarker"></div>');

                }*/

                /*if (chartDataObjRevenueTracking) {



                    //Chart for Revenue Tracking
                    Highcharts.chart({
                        chart: {
                            renderTo: grapId+'-RevenueTracking',
                            zoomType: 'xy'
                        },
                        title: {
                            text: RevenueTrackingTemp.chartName
                        },
                        xAxis: [{
                            categories: RevenueTrackingTemp.Period,
                            crosshair: true
                        }],
                        yAxis: [{ // Primary yAxis
                            labels: {
                                format: '{value} $',
                                style: {
                                    color: Highcharts.getOptions().colors[1]
                                }
                            },
                            title: {
                                text: RevenueTrackingTemp.properties.line_1.lineName,
                                style: {
                                    color: Highcharts.getOptions().colors[1]
                                }
                            }
                        }, { // Secondary yAxis
                            title: {
                                text: RevenueTrackingTemp.properties.line_2.lineName,
                                style: {
                                    color: Highcharts.getOptions().colors[0]
                                }
                            },
                            labels: {
                                format: '{value} $',
                                style: {
                                    color: Highcharts.getOptions().colors[0]
                                }
                            },
                            opposite: true
                        }],
                        tooltip: {
                            shared: true
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'left',
                            x: 120,
                            verticalAlign: 'top',
                            y: 100,
                            floating: true,
                            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
                        },
                        series: [{
                            name: RevenueTrackingTemp.properties.line_1.lineName,
                            type: 'line',
                            yAxis: 1,
                            data: RevenueTrackingTemp.properties.line_1.values,
                            tooltip: {
                                valueSuffix: ' $'
                            }

                        }, {
                            name: RevenueTrackingTemp.properties.line_2.lineName,
                            type: 'line',
                            data: RevenueTrackingTemp.properties.line_2.values,
                            tooltip: {
                                valueSuffix: '$'
                            }
                        }]
                    });
                }*/

                /*if (chartDataObjRevenueTrackingS) {

                    //Chart for Revenue Tracking S
                    Highcharts.chart({
                        chart: {
                            renderTo: grapId+'-RevenueTrackingS'
                        },
                        title: {
                            text: RevenueTrackingSTemp.chartName
                        },
                        xAxis: [{
                            categories: RevenueTrackingSTemp.Period
                        }],
                        yAxis: {
                            title: {
                                text: 'Revenue'
                            }
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'middle'
                        },
                        series: [{
                            name: RevenueTrackingSTemp.properties.line_1.lineName,
                            data: RevenueTrackingSTemp.properties.line_1.values
                        }, {
                            name: RevenueTrackingSTemp.properties.line_2.lineName,
                            data: RevenueTrackingSTemp.properties.line_2.values
                        }]

                    });


                    Highcharts.chart({
                        chart: {
                            renderTo: grapId+'-RevenueTrackingS',
                            zoomType: 'xy'
                        },
                        title: {
                            text: RevenueTrackingSTemp.chartName
                        },
                        xAxis: [{
                            categories: RevenueTrackingSTemp.Period,
                            crosshair: true
                        }],
                        yAxis: [{ // Primary yAxis
                            labels: {
                                format: '{value} $',
                                style: {
                                    color: Highcharts.getOptions().colors[1]
                                }
                            },
                            title: {
                                text: RevenueTrackingSTemp.properties.line_1.lineName,
                                style: {
                                    color: Highcharts.getOptions().colors[1]
                                }
                            }
                        }, { // Secondary yAxis
                            title: {
                                text: RevenueTrackingSTemp.properties.line_2.lineName,
                                style: {
                                    color: Highcharts.getOptions().colors[0]
                                }
                            },
                            labels: {
                                format: '{value} $',
                                style: {
                                    color: Highcharts.getOptions().colors[0]
                                }
                            },
                            opposite: true
                        }],
                        tooltip: {
                            shared: true
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'left',
                            x: 120,
                            verticalAlign: 'top',
                            y: 100,
                            floating: true,
                            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || 'rgba(255,255,255,0.25)'
                        },
                        series: [{
                            name: RevenueTrackingSTemp.properties.line_1.lineName,
                            type: 'line',
                            yAxis: 1,
                            data: RevenueTrackingSTemp.properties.line_1.values,
                            tooltip: {
                                valueSuffix: ' $'
                            }

                        }, {
                            name: RevenueTrackingSTemp.properties.line_2.lineName,
                            type: 'line',
                            data: RevenueTrackingSTemp.properties.line_2.values,
                            tooltip: {
                                valueSuffix: '$'
                            }
                        }]
                    });
            }*/

            /*if (chartDataObjCrossMaker) {
                    //Chart for CrossMarker
                    Highcharts.chart({
                        chart: {
                            renderTo: grapId+'-CrossMarker'
                        },
                        title: {
                            text: CrossMakerTemp.chartName
                        },
                        xAxis: [{
                            categories: CrossMakerTemp.Period
                        }],
                        yAxis: {
                            title: {
                                text: 'Revenue'
                            }
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'middle'
                        },
                        series: [{
                            name: CrossMakerTemp.properties.line_1.lineName,
                            data: CrossMakerTemp.properties.line_1.values
                        }, {
                            name: CrossMakerTemp.properties.line_2.lineName,
                            data: CrossMakerTemp.properties.line_2.values
                        }]

                    });


                }*/

            });
		// TEST - example graph with hard-coded data (the data would be passed in as a parameter)
		/*$.plot($(graphName),[
			{ label:'data1', data: [[0,0.5],[1,1],[2,4],[3,3],[4,5],[5,7],[6,9],[7,10],[8,8],[9,12]], color: 'white'},
			{ label:'data2', data: [[0,2],[1,1.5],[2,6],[3,3.5],[4,2.5],[5,2],[6,4.5],[7,7],[8,6],[9,8]], color: 'yellow'}
		], {
			series: {
				lines: { show: true, fill: true, fillColor: 'rgba(255, 255, 255, 0.3)' },
				points: { show: true}
			},
			grid: {
				color: 'transparent',
				hoverable: true,
				clickable: true
			},
			xaxis: { 
				color: 'rgba(255, 255, 255, 0.5)',
				font: { color: 'white', family: 'sans-serif', size: 11},
				ticks: [[0, "Jan"], [1, "Feb"], [2, "Mar"], [3, "Apr"], [4, "May"], [5, "Jun"], [6, "Jul"], [7, "Aug"], [8, "Sep"], [9, "Oct"]]
			},
			yaxis: { 
				color: 'rgba(255, 255, 255, 0.5)',
				font: { color: 'white', family: 'sans-serif', size: 11}
			}
		});

		$("<div id='tooltip'></div>").css({
			position: "absolute",
			display: "none",
			border: "1px solid #fdd",
			padding: "2px",
			"background-color": "#fee",
			opacity: 0.80
		}).appendTo("body");

		$(graphName).bind("plothover", function (event, pos, item) {

			if (item) {
				var x = item.datapoint[0].toFixed(2),
					y = item.datapoint[1].toFixed(2);

				//$("#tooltip").html(item.series.label + " of " + x + " = " + y)
				//$("#tooltip").html(x + ": " + y)
				$("#tooltip").html(y)
					.css({top: item.pageY+5, left: item.pageX+5})
					.fadeIn(200);
			} else {
				$("#tooltip").hide();
			}
		});*/
	}
}

$(document).ready(function(){

    /*----------------------------------------------
        Make some random data for Flot Line Chart
    ----------------------------------------------*/
    var data1 = [[1,60], [2,30], [3,50], [4,100], [5,10], [6,90], [7,85]];
    var data2 = [[1,20], [2,90], [3,60], [4,40], [5,100], [6,25], [7,65]];
    var data3 = [[1,100], [2,20], [3,60], [4,90], [5,80], [6,10], [7,5]];
    
    // Create an Array push the data + Draw the bars

    var barData = [
        {
            label: 'Tokyo',
            data: data1,
            color: '#8BC34A'
        },
        {
            label: 'Seoul',
            data: data2,
            color: '#00BCD4'
        },
        {
            label: 'Beijing',
            data: data3,
            color: '#FF9800'
        }
    ]

    /*---------------------------------
        Let's create the chart
    ---------------------------------*/
    if ($('#bar-chart')[0]) {
        $.plot($("#bar-chart"), barData, {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.05,
                    order: 1,
                    fill: 1
                }
            },
            grid : {
                borderWidth: 1,
                borderColor: '#eee',
                show : true,
                hoverable : true,
                clickable : true
            },
            
            yaxis: {
                tickColor: '#eee',
                tickDecimals: 0,
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f",
                },
                shadowSize: 0
            },
            
            xaxis: {
                tickColor: '#fff',
                tickDecimals: 0,
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f"
                },
                shadowSize: 0,
            },
    
            legend:{
                container: '.flc-bar',
                backgroundOpacity: 0.5,
                noColumns: 0,
                backgroundColor: "white",
                lineWidth: 0
            }
        });
    }

    /*---------------------------------
        Tooltips for Flot Charts
    ---------------------------------*/
    if ($(".flot-chart")[0]) {
        $(".flot-chart").bind("plothover", function (event, pos, item) {
            if (item) {
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                $(".flot-tooltip").html(item.series.label + " of " + x + " = " + y).css({top: item.pageY+5, left: item.pageX+5}).show();
            }
            else {
                $(".flot-tooltip").hide();
            }
        });
        
        $("<div class='flot-tooltip' class='chart-tooltip'></div>").appendTo("body");
    }
});
$(document).ready(function(){

    /*-----------------------------------------
        Make some random data for the Chart
    -----------------------------------------*/
    var d1 = [];
    for (var i = 0; i <= 10; i += 1) {
        d1.push([i, parseInt(Math.random() * 30)]);
    }
    var d2 = [];
    for (var i = 0; i <= 20; i += 1) {
        d2.push([i, parseInt(Math.random() * 30)]);
    }    
    var d3 = [];
    for (var i = 0; i <= 10; i += 1) {
        d3.push([i, parseInt(Math.random() * 30)]);
    }

    /*---------------------------------
        Chart Options
    ---------------------------------*/
    var options = {
        series: {
            shadowSize: 0,
            curvedLines: { //This is a third party plugin to make curved lines
                apply: true,
                active: true,
                monotonicFit: true
            },
            lines: {
                show: false,
                lineWidth: 0,
                fill: 1
            },
        },
        grid: {
            borderWidth: 0,
            labelMargin:10,
            hoverable: true,
            clickable: true,
            mouseActiveRadius:6,
            
        },
        xaxis: {
            tickDecimals: 0,
            ticks: false
        },
        
        yaxis: {
            tickDecimals: 0,
            ticks: false
        },
        
        legend: {
            show: false
        }
    };

    /*---------------------------------
        Let's create the chart
    ---------------------------------*/
    if ($("#curved-line-chart")[0]) {
        $.plot($("#curved-line-chart"), [
            {data: d1, lines: { show: true, fill: 0.98 }, label: 'Product 1', stack: true, color: '#e3e3e3' },
            {data: d3, lines: { show: true, fill: 0.98 }, label: 'Product 2', stack: true, color: '#f1dd2c' }
        ], options);
    }

    /*---------------------------------
         Tooltips for Flot Charts
    ---------------------------------*/
    if ($(".flot-chart")[0]) {
        $(".flot-chart").bind("plothover", function (event, pos, item) {
            if (item) {
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                $(".flot-tooltip").html(item.series.label + " of " + x + " = " + y).css({top: item.pageY+5, left: item.pageX+5}).show();
            }
            else {
                $(".flot-tooltip").hide();
            }
        });
        
        $("<div class='flot-tooltip' class='chart-tooltip'></div>").appendTo("body");
    }
});
$(document).ready(function(){
    var data1 = [
        [gd(2016, 1, 2), 1800], [gd(2016, 1, 3), 1790], [gd(2016, 1, 4), 1810],
        [gd(2016, 1, 7), 1750], [gd(2016, 1, 8), 1805], [gd(2016, 1, 9), 1800],
        [gd(2016, 1, 10), 1794], [gd(2016, 1, 11), 1794], [gd(2016, 1, 14), 1807],
        [gd(2016, 1, 15), 1788], [gd(2016, 1, 16), 1799], [gd(2016, 1, 17), 1804],
        [gd(2016, 1, 18), 1811], [gd(2016, 1, 21), 1801], [gd(2016, 1, 22), 1805],
        [gd(2016, 1, 23), 1770], [gd(2016, 1, 24), 1799], [gd(2016, 1, 25), 1804],
        [gd(2016, 1, 28), 1810], [gd(2016, 1, 29), 1788], [gd(2016, 1, 30), 1804],
        [gd(2016, 1, 31), 1775]
    ];

    var data2 = [
        [gd(2016, 1, 2), 1674], [gd(2016, 1, 3), 1680], [gd(2016, 1, 4), 1643],
        [gd(2016, 1, 7), 1652], [gd(2016, 1, 8), 1640], [gd(2016, 1, 9), 1652],
        [gd(2016, 1, 10), 1652], [gd(2016, 1, 11), 1664], [gd(2016, 1, 14), 1660],
        [gd(2016, 1, 15), 1664], [gd(2016, 1, 16), 1673], [gd(2016, 1, 17), 1671],
        [gd(2016, 1, 18), 1682], [gd(2016, 1, 21), 1680], [gd(2016, 1, 22), 1685],
        [gd(2016, 1, 23), 1684], [gd(2016, 1, 24), 1670], [gd(2016, 1, 25), 1664],
        [gd(2016, 1, 28), 1652], [gd(2016, 1, 29), 1655], [gd(2016, 1, 30), 1659],
        [gd(2016, 1, 31), 1668]
    ];

    var dataset = [
        {
            label: "Students",
            data: data1,
            color: "#26A69A",
            points: {
                fillColor: "#26A69A",
                show: true,
                radius: 0
            },
            lines: {
                show: true,
                lineWidth: 2
            }
        },
        {
            label: "Teachers",
            data: data2,
            xaxis:2,
            color: "#FFC107",
            points: {
                fillColor: "#FFC107",
                show: true,
                radius: 0
            },
            lines: {
                show: true,
                lineWidth: 2
            }
        }
    ];

    var dayOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thr", "Fri", "Sat"];

    var options = {
        series: {
            shadowSize: 0,
            curvedLines: { //This is a third party plugin to make curved lines
                apply: true,
                active: true,
                monotonicFit: true
            }
        },
        grid : {
            borderWidth: 1,
            borderColor: '#f3f3f3',
            show : true,
            clickable : true,
            hoverable: true,
            mouseActiveRadius: 20,
            labelMargin: 10
        },

        xaxes: [
            {
                color: '#f3f3f3',
                mode: "time",
                tickFormatter: function (val, axis) {
                    return dayOfWeek[new Date(val).getDay()];
                },
                position: "top",
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f"
                }
            },
            {
                color: '#f3f3f3',
                mode: "time",
                timeformat: "%m/%d",
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f"
                }
            }
        ],
        yaxis: {
            ticks: 2,
            color: "#f3f3f3",
            tickDecimals: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f"
            },


        },
        legend: {
            container: '.flc-visits',
            backgroundOpacity: 0.5,
            noColumns: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f"
            },
        }
    };

    function gd(year, month, day) {
        return new Date(year, month - 1, day).getTime();
    }

    if ($('#attendance')[0]) {
        $.plot($("#attendance"), dataset, options);
    }
});
$(document).ready(function(){

    var data1 = [[2010,60], [2011,50], [2012,80], [2013,30], [2014,70], [2015,40], [2016,55]];

    var dataset = [
        {
            label: "Index Value",
            data: data1,
            color: "#00BCD4",
            points: {
                fillColor: "#00BCD4",
                show: true,
                radius: 0
            },
            lines: {
                show: true,
                lineWidth: 1,
                fill: 1,
                fillColor: {
                    colors: ["rgba(255,255,255,0.0)","#00BCD4"]
                }
            }
        }
    ];

    var options = {
        series: {
            shadowSize: 0,
            curvedLines: { //This is a third party plugin to make curved lines
                apply: true,
                active: true,
                monotonicFit: true
            }
        },

        grid : {
            borderWidth: 1,
            borderColor: '#eee',
            show : true,
            hoverable : true,
            clickable : true
        },

        yaxis: {
            tickColor: '#eee',
            tickDecimals: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f",
            },
            shadowSize: 0
        },

        xaxis: {
            tickColor: '#fff',
            tickDecimals: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f"
            },
            shadowSize: 0,
        },

        legend:{
            container: '.flc-sei',
            backgroundOpacity: 0.5,
            noColumns: 0,
            backgroundColor: "white",
            lineWidth: 0
        }
    }

    if ($('#effective-index')[0]) {
        $.plot($("#effective-index"), dataset, options);
    }
});
$(document).ready(function(){
    var feeData = [
        {data: 5, color: '#03A9F4', label: 'Collected'},
        {data: 2, color: '#F44336', label: 'Not Collected'},
        {data: 1, color: '#8BC34A', label: 'Pending'}
    ];


    /*---------------------------------
     Pie Chart
     ---------------------------------*/
    if($('#fee-collected')[0]){
        $.plot('#fee-collected', feeData, {
            series: {
                pie: {
                    show: true,
                    stroke: {
                        width: 2
                    }
                }
            },
            legend: {
                container: '.flc-pie',
                backgroundOpacity: 0.5,
                noColumns: 0,
                backgroundColor: "white",
                lineWidth: 0
            },
            grid: {
                hoverable: true,
                clickable: true
            },
            tooltip: false,
            tooltipOpts: {
                content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
                defaultTheme: false,
                cssClass: 'flot-tooltip'
            }

        });
    }
});
$(document).ready(function(){
    var data = [[1,60], [2,30], [3,50], [4,100], [5,10], [6,90], [7,85], [8, 10], [9, 25],[10, 65],[11, 69],[12, 104],[13, 94],[14, 32],[15, 10],[16, 45],[17, 34],[18, 22],[19, 100],[20, 43],[21, 98],[22, 60],[23, 51],[24,30],];

    var dataset = [{
        data : data,
        label: 'Visits',
        bars : {
            show : true,
            barWidth : 0.4,
            order : 1,
            lineWidth: 0,
            fillColor: '#ff766c'
        }
    }];

    var options = {
        grid : {
            borderWidth: 1,
            borderColor: '#f3f3f3',
            show : true,
            hoverable : true,
            clickable : true,
            labelMargin: 10
        },

        yaxis: {
            tickColor: '#f3f3f3',
            tickDecimals: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f",
            },
            shadowSize: 0
        },

        xaxis: {
            tickFormatter: function (value, axis) {
                return value+'h'
            },
            tickColor: '#fff',
            tickDecimals: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f"
            },
            shadowSize: 0,
        },

        legend:{
            show: false
        }
    }

    if ($('#visit-server-time')[0]) {
        $.plot($("#visit-server-time"), dataset, options);
    }

});
$(document).ready(function(){
    var data1 = [
        [gd(2013, 1, 2), 1690.25], [gd(2013, 1, 3), 1696.3], [gd(2013, 1, 4), 1659.65],
        [gd(2013, 1, 7), 1668.15], [gd(2013, 1, 8), 1656.1], [gd(2013, 1, 9), 1668.65],
        [gd(2013, 1, 10), 1668.15], [gd(2013, 1, 11), 1680.2], [gd(2013, 1, 14), 1676.7],
        [gd(2013, 1, 15), 1680.7], [gd(2013, 1, 16), 1689.75], [gd(2013, 1, 17), 1687.25],
        [gd(2013, 1, 18), 1698.3], [gd(2013, 1, 21), 1696.8], [gd(2013, 1, 22), 1701.3],
        [gd(2013, 1, 23), 1700.8], [gd(2013, 1, 24), 1686.75], [gd(2013, 1, 25), 1680],
        [gd(2013, 1, 28), 1668.65], [gd(2013, 1, 29), 1671.2], [gd(2013, 1, 30), 1675.7],
        [gd(2013, 1, 31), 1684.25]
    ];

    var data2 = [
        [gd(2013, 1, 2), 1674.15], [gd(2013, 1, 3), 1680.15], [gd(2013, 1, 4), 1643.8],
        [gd(2013, 1, 7), 1652.25], [gd(2013, 1, 8), 1640.3], [gd(2013, 1, 9), 1652.75],
        [gd(2013, 1, 10), 1652.25], [gd(2013, 1, 11), 1664.2], [gd(2013, 1, 14), 1660.7],
        [gd(2013, 1, 15), 1664.7], [gd(2013, 1, 16), 1673.65], [gd(2013, 1, 17), 1671.15],
        [gd(2013, 1, 18), 1682.1], [gd(2013, 1, 21), 1680.65], [gd(2013, 1, 22), 1685.1],
        [gd(2013, 1, 23), 1684.6], [gd(2013, 1, 24), 1670.65], [gd(2013, 1, 25), 1664],
        [gd(2013, 1, 28), 1652.75], [gd(2013, 1, 29), 1655.25], [gd(2013, 1, 30), 1659.7],
        [gd(2013, 1, 31), 1668.2]
    ];

    var dataset = [
        {
            label: "Visits",
            data: data1,
            color: "#ff766c",
            points: {
                fillColor: "#ff766c",
                show: true,
                radius: 2
            },
            lines: {
                show: true,
                lineWidth: 1
            }
        },
        {
            label: "Unique Visitors",
            data: data2,
            xaxis:2,
            color: "#03A9F4",
            points: {
                fillColor: "#03A9F4",
                show: true,
                radius: 2
            },
            lines: {
                show: true,
                lineWidth: 1
            }
        }
    ];

    var dayOfWeek = ["Sun", "Mon", "Tue", "Wed", "Thr", "Fri", "Sat"];

    var options = {
        series: {
            shadowSize: 0
        },
        grid : {
            borderWidth: 1,
            borderColor: '#f3f3f3',
            show : true,
            clickable : true,
            hoverable: true,
            mouseActiveRadius: 20,
            labelMargin: 10
        },

        xaxes: [
            {
                color: '#f3f3f3',
                mode: "time",
                tickFormatter: function (val, axis) {
                    return dayOfWeek[new Date(val).getDay()];
                },
                position: "top",
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f"
                }
            },
            {
                color: '#f3f3f3',
                mode: "time",
                timeformat: "%m/%d",
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f"
                }
            }
        ],
        yaxis: {
            ticks: 2,
            color: "#f3f3f3",
            tickDecimals: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f"
            },


        },
        legend: {
            container: '.flc-visits',
            backgroundOpacity: 0.5,
            noColumns: 0,
            font :{
                lineHeight: 13,
                style: "normal",
                color: "#9f9f9f"
            },
        }
    };

    function gd(year, month, day) {
        return new Date(year, month - 1, day).getTime();
    }

    if ($('#visit-over-time')[0]) {
        $.plot($("#visit-over-time"), dataset, options);
    }
});
$(document).ready(function(){

    /*---------------------------------
        Make some random data
     ---------------------------------*/
    var data = [];
    var totalPoints = 300;
    var updateInterval = 30;
    
    function getRandomData() {
        if (data.length > 0)
            data = data.slice(1);

        while (data.length < totalPoints) {
    
            var prev = data.length > 0 ? data[data.length - 1] : 50,
                y = prev + Math.random() * 10 - 5;
            if (y < 0) {
                y = 0;
            } else if (y > 90) {
                y = 90;
            }

            data.push(y);
        }

        var res = [];
        for (var i = 0; i < data.length; ++i) {
            res.push([i, data[i]])
        }

        return res;
    }

    /*---------------------------------
        Create Chart
     ---------------------------------*/
    if ($('#dynamic-chart')[0]) {
        var plot = $.plot("#dynamic-chart", [ getRandomData() ], {
            series: {
                label: "Server Process Data",
                lines: {
                    show: true,
                    lineWidth: 0.2,
                    fill: 0.6
                },
    
                color: '#00BCD4',
                shadowSize: 0,
            },
            yaxis: {
                min: 0,
                max: 100,
                tickColor: '#eee',
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f",
                },
                shadowSize: 0,
    
            },
            xaxis: {
                tickColor: '#eee',
                show: true,
                font :{
                    lineHeight: 13,
                    style: "normal",
                    color: "#9f9f9f",
                },
                shadowSize: 0,
                min: 0,
                max: 250
            },
            grid: {
                borderWidth: 1,
                borderColor: '#eee',
                labelMargin:10,
                hoverable: true,
                clickable: true,
                mouseActiveRadius:6
            },
            legend:{
                show: false
            }
        });

        /*---------------------------------
            Update
         ---------------------------------*/
        function update() {
            plot.setData([getRandomData()]);
            // Since the axes don't change, we don't need to call plot.setupGrid()

            plot.draw();
            setTimeout(update, updateInterval);
        }
        update();
    }
});
$(document).ready(function(){

    /*---------------------------------------------------
        Make some random data for Recent Items chart
    ---------------------------------------------------*/
    var data = [];
    var totalPoints = 100;
    var updateInterval = 30;
    
    function getRandomData() {
        if (data.length > 0)
            data = data.slice(1);

        while (data.length < totalPoints) {
    
            var prev = data.length > 0 ? data[data.length - 1] : 50,
                y = prev + Math.random() * 10 - 5;
            if (y < 0) {
                y = 0;
            } else if (y > 90) {
                y = 90;
            }

            data.push(y);
        }

        var res = [];
        for (var i = 0; i < data.length; ++i) {
            res.push([i, data[i]])
        }

        return res;
    }

    /*---------------------------------------------------
        Make some random data for Flot Line Chart
    ---------------------------------------------------*/
    
    var d1 = [];
    for (var i = 0; i <= 10; i += 1) {
        d1.push([i, parseInt(Math.random() * 30)]);
    }
    var d2 = [];
    for (var i = 0; i <= 20; i += 1) {
        d2.push([i, parseInt(Math.random() * 30)]);
    }    
    var d3 = [];
    for (var i = 0; i <= 10; i += 1) {
        d3.push([i, parseInt(Math.random() * 30)]);
    }

    /*---------------------------------
        Chart Options
    ---------------------------------*/
    var options = {
        series: {
            shadowSize: 0,
            lines: {
                show: false,
                lineWidth: 0,
            },
        },
        grid: {
            borderWidth: 0,
            labelMargin:10,
            hoverable: true,
            clickable: true,
            mouseActiveRadius:6,
            
        },
        xaxis: {
            tickDecimals: 0,
            ticks: false
        },
        
        yaxis: {
            tickDecimals: 0,
            ticks: false
        },
        
        legend: {
            show: false
        }
    };

    /*---------------------------------
        Regular Line Chart
    ---------------------------------*/
    if ($("#line-chart")[0]) {
        $.plot($("#line-chart"), [
            {data: d1, lines: { show: true, fill: 0.98 }, label: 'Product 1', stack: true, color: '#e3e3e3' },
            {data: d3, lines: { show: true, fill: 0.98 }, label: 'Product 2', stack: true, color: '#FFC107' }
        ], options);
    }


    /*---------------------------------
        Recent Items Table Chart
    ---------------------------------*/
    if ($("#recent-items-chart")[0]) {
        $.plot($("#recent-items-chart"), [
            {data: getRandomData(), lines: { show: true, fill: 0.8 }, label: 'Items', stack: true, color: '#00BCD4' },
        ], options);
    }


    /*---------------------------------
        Tooltips for Flot Charts
    ---------------------------------*/
    if ($(".flot-chart")[0]) {
        $(".flot-chart").bind("plothover", function (event, pos, item) {
            if (item) {
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);
                $(".flot-tooltip").html(item.series.label + " of " + x + " = " + y).css({top: item.pageY+5, left: item.pageX+5}).show();
            }
            else {
                $(".flot-tooltip").hide();
            }
        });
        
        $("<div class='flot-tooltip' class='chart-tooltip'></div>").appendTo("body");
    }
});
$(document).ready(function(){
    var pieData = [
        {data: 1, color: '#F44336', label: 'Toyota'},
        {data: 2, color: '#03A9F4', label: 'Nissan'},
        {data: 3, color: '#8BC34A', label: 'Hyundai'},
        {data: 4, color: '#FFEB3B', label: 'Scion'},
        {data: 4, color: '#009688', label: 'Daihatsu'},
    ];


    /*---------------------------------
        Pie Chart
    ---------------------------------*/
    if($('#pie-chart')[0]){
        $.plot('#pie-chart', pieData, {
            series: {
                pie: {
                    show: true,
                    stroke: { 
                        width: 2,
                    },
                },
            },
            legend: {
                container: '.flc-pie',
                backgroundOpacity: 0.5,
                noColumns: 0,
                backgroundColor: "white",
                lineWidth: 0
            },
            grid: {
                hoverable: true,
                clickable: true
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false,
                cssClass: 'flot-tooltip'
            }
            
        });
    }

    /*---------------------------------
        Donut Chart
    ---------------------------------*/
    if($('#donut-chart')[0]){
        $.plot('#donut-chart', pieData, {
            series: {
                pie: {
                    innerRadius: 0.5,
                    show: true,
                    stroke: { 
                        width: 2,
                    },
                },
            },
            legend: {
                container: '.flc-donut',
                backgroundOpacity: 0.5,
                noColumns: 0,
                backgroundColor: "white",
                lineWidth: 0
            },
            grid: {
                hoverable: true,
                clickable: true
            },
            tooltip: true,
            tooltipOpts: {
                content: "%p.0%, %s", // show percentages, rounding to 2 decimal places
                shifts: {
                    x: 20,
                    y: 0
                },
                defaultTheme: false,
                cssClass: 'flot-tooltip'
            }
            
        });
    }
});
/*----------------------------------------------------------
    Detect Mobile Browser
-----------------------------------------------------------*/
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
   $('html').addClass('ismobile');
}

$(window).load(function () {
    /*----------------------------------------------------------
        Page Loader (no longer used)
     -----------------------------------------------------------*/
    if(!$('html').hasClass('ismobile')) {
        if($('.page-loader')[0]) {
            setTimeout (function () {
                $('.page-loader').fadeOut();
            }, 500);

        }
    }
	
	// have the navbar sticky when the page is scrolled past it
	$(document).scroll(function() {
		var scrollPosition = $(document).scrollTop();
		
		if(scrollPosition > $('.company-header').height()) {
			$('body').addClass('is-fixed');
		} else if($('body').hasClass('is-fixed')) {
			$('body').removeClass('is-fixed');
		}
		//alert($(document).scrollTop());
	});
	
})

$(document).ready(function(){
	
	/*----------------------------------------------------------
        Scrollbar
    -----------------------------------------------------------*/
    function scrollBar(selector, theme, mousewheelaxis) {
        $(selector).mCustomScrollbar({
            theme: theme,
            scrollInertia: 100,
            axis:'yx',
            mouseWheel: {
                enable: true,
                axis: mousewheelaxis,
                preventDefault: true
            }
        });
    }

    if (!$('html').hasClass('ismobile')) {
        //On Custom Class
        if ($('.c-overflow')[0]) {
            scrollBar('.c-overflow', 'minimal-dark', 'y');
        }
    }
	
   
    /*----------------------------------------------------------
        Bootstrap Accordion Fix
    -----------------------------------------------------------*/
    if ($('.collapse')[0]) {

        //Add active class for opened items
        $('.collapse').on('show.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });

        $('.collapse').on('hide.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').removeClass('active');
        });

        //Add active class for pre opened items
        $('.collapse.in').each(function(){
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });
    }


    /*----------------------------------------------------------
        Text Field
    -----------------------------------------------------------*/
    //Add blue animated border and remove with condition when focus and blur
	if($('.fg-line')[0]) {
	    $('body').on('change', '.fg-line .form-control', function(){
			var i = $(this).val();

			if (!i.length == 0) $(this).closest('.fg-line').addClass('fg-toggled');
        })

        $('body').on('focus', '.fg-line .form-control', function(){
            $(this).closest('.fg-line').addClass('fg-toggled');
		})

        $('body').on('blur', '.form-control', function(){
            var p = $(this).closest('.form-group, .input-group');
            var i = p.find('.form-control').val();

            if (p.hasClass('fg-float')) {
                if (i.length == 0) {
                    $(this).closest('.fg-line').removeClass('fg-toggled');
                }
            }
            else {
                $(this).closest('.fg-line').removeClass('fg-toggled');
            }
        });
    }

    //Add blue border for pre-valued fg-float text feilds
    if($('.fg-float')[0]) {
		setTimeout(function() {
			$('.fg-float .form-control').each(function(){
				var i = $(this).val();

				if (!i.length == 0) {
					$(this).closest('.fg-line').addClass('fg-toggled');
				}

			});
		}, 100);
    }

	/*----------------------------------------------------------
        Data tables
    -----------------------------------------------------------*/
	$('.data-table').DataTable({
		"searching": false,
		"ordering":  false,
		"paging":  false,
		"info":  false,
		"lengthChange": false
		//"dom": 'rtilp'
	});


    /*-----------------------------------------------------------
        Link prevent
    -----------------------------------------------------------*/
    $('body').on('click', '.a-prevent', function(e){
        e.preventDefault();
    });

	/*-----------------------------------------------------------
        create layer elements  (no longer used)
    -----------------------------------------------------------*/
	function createFeatureItem(header, amount, percentage) {
		// create the element (from the templates)
		var featureItem = $('#templates .mini-charts-item').clone();
		
		// apply the values
		$('.header', featureItem).html(header);
		$('.amount', featureItem).html(amount);
		
		var absPercentage = Math.abs(percentage);
		if(percentage < 0) {
			// negative value
			featureItem.addClass('bgm-red negative');
			$('.percentage', featureItem).html('('+absPercentage+'%)');
			$('.movement .zmdi', featureItem).addClass('zmdi-triangle-down');
			
		} else {
			featureItem.addClass('bgm-lightgreen');
			$('.percentage', featureItem).html(percentage+'%');
			$('.movement .zmdi', featureItem).addClass('zmdi-triangle-up');
			
		}
		
		if(absPercentage < 10) featureItem.addClass('lt10');
		else if(absPercentage < 100) featureItem.addClass('lt100');
		
		// temp - add the item to the current tab
		$('.tab-pane.active').append(featureItem);
		
		return featureItem;
    }
	
	/*-----------------------------------------------------------
        Waves
    -----------------------------------------------------------*/
    (function(){
         Waves.attach('.btn');
         Waves.attach('.btn-icon, .btn-float', ['waves-circle', 'waves-float']);
        Waves.init();
    })();
	
	/*----------------------------------------------------------
        TEMP - plot graph on dashboard
    -----------------------------------------------------------*/
	plotGraph('dashboard', '');
	
	/*----------------------------------------------------------
        WYSIWYG
    -----------------------------------------------------------*/
	tinymce.init({
	  selector: 'textarea.editable',
	  inline: false,
	  plugins: 'lists advlist textcolor ',
	  toolbar: 'bold italic underline forecolor | bullist numlist outdent indent',
	  menubar: false
	});
	//toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
	
});


/*----------------------------------------------------------
        Bootstrap Accordion Fix
    -----------------------------------------------------------*/
function prepareAccordions(context) {
    if ($('.collapse', context)[0]) {

        //Add active class for opened items
        $('.collapse', context).on('show.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });

        $('.collapse', context).on('hide.bs.collapse', function (e) {
            $(this).closest('.panel').find('.panel-heading').removeClass('active');
        });

        //Add active class for pre opened items
        $('.collapse.in', context).each(function(){
            $(this).closest('.panel').find('.panel-heading').addClass('active');
        });
    }
}

(function() {


	// force the page to refresh if the user has got there using the browser's 'Back' button
	window.addEventListener( "pageshow", function ( event ) {
	  var historyTraversal = event.persisted || ( typeof window.performance != "undefined" && window.performance.navigation.type === 2 );
	  if ( historyTraversal ) {
		// Handle page restore.
		window.location.reload();
	  }
	});
	
	var previousContentPanel;
	var dataDialog;
	var isAdmin = true;


	//varibale to store user data
    var usermanager_usr_list;
    var usermanager_org_list;
	
	// button actions
	$('[data-action]').click( function (e) {
        e.preventDefault();
		
		var action = $(this).data('action');
		switch(action) {
			case 'changeContentPanel':
				// get the ID of the target panel
				var panelID = $(this).data('panel');
				
				if(panelID == 'previous') {
					panelID = previousContentPanel ? previousContentPanel : 'login';
				} else {
					// store a reference to current (soon to be previous) content panel
					previousContentPanel = $('.content-panel').not('.hidden').attr('id');
				}
				
				// clear any messages
				$('.message').html('').addClass('hidden');
				
				// change what content is visible on the login page
				$('.content-panel').not('#'+panelID).addClass('hidden');
				$('#'+panelID).removeClass('hidden');
				
				if(panelID == 'help') $('[data-panel="help"]').addClass('hidden');
				else $('[data-panel="help"]').removeClass('hidden');
				
				break;
		}
	});
	
	$('#btn-login').click(function(e) {
		e.preventDefault();
		if($(this).hasClass('disabled')) return;
		
		// clear any previous message
		var message = '';
		$('#login .message').html('').addClass('hidden').removeClass('error');
		
		var username = $('#login input[name="userid"]').val();
		var pwd = $('#login input[name="passwd"]').val();
		
		// check whether both username & pwd have been entered
		if(username == '' || pwd == '') {
			// show an error message
			message = 'You need to enter ' + (username == '' ? 'a username.' : 'your password.');			
			$('#login .message').html(message).addClass('error').removeClass('hidden');
			
			return;
		}
		
		// try the login
		$.ajax({
		  method: "POST",
		  url: "../user/user.php",
		  data: { 
			  mode: 'authenticate',
			  userid: username, 
			  passwd: pwd 
			}
		})
		.done(function(data) {
		//alert( "success - " +data);
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
				var userid = xml.find( "id" ).text();
				
				// prompt the user to select company & period
				promptForData(userid);

                //get user list
                //getUserlistData(userid,usermanager_usr_list);

				// TEMP - go to main page
				//window.location.assign("main.php?company=Company C&month=1|2017")
			} else {
				message = 'Incorrect username or password.';			
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to authenticate with the server.';			
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	});

    function getUserlistData(userid, usermanager_usr_list){
        $.ajax({
            method: "POST",
            url: "../user/user.php",
            data: {
                mode: 'list',
                format: 'json'
            }
        }).done(function(data) {
                var userlistjson = $.parseJSON(data);
                console.log("userlist",userlistjson.user);
                usermanager_usr_list = userlistjson.user;
                $("#usr_list").html(usermanager_usr_list);
                return userlistjson.user
        }).fail(function (data) {
                return null;
        })
    }

	function promptForData(userid) {
		// clear the selects in the dialog
		$('.data-dialog select').val('');
		$('.data-dialog option').not('.data-dialog option[value=""]').remove();
		$('.period-select-container').addClass('hidden');
		
		$.ajax({
		  method: "POST",
		  url: "../user/org.php",
		  data: { 
			  mode: 'getorgassigned',
			  userid: userid
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
				var dialogContent = $('.data-dialog').clone();
                $('.company-select', dialogContent).find("option").remove().end();
                $('.company-select', dialogContent).append('<option selected="true" disabled="disabled">Select Organisation</option>');
				
				xml.find('org').each(function(index) {
					//alert($(this).find('orgname').text());
					var orgid = $(this).find('orgid').text();
					var orgname = $(this).find('orgname').text();
					$('.company-select', dialogContent).append('<option data-id="'+orgid+'" value="'+orgname+'">'+orgname+'</option>');
					
					if(index == 0) $('.company-set .value', dialogContent).html(orgname);
				});
				
				if(xml.find('org').length == 1) {
					$('.company-set', dialogContent).removeClass('hidden');
					updateAvailablePeriods($('.company-set .value', dialogContent).text(), dialogContent);
				} else {
					$('.company-select-container', dialogContent).removeClass('hidden');
				}
				
				$('.company-select', dialogContent).change(function(e) {
					var company = $(this).val();
					
					if(company != '') {
						updateAvailablePeriods(company);
					} else {
						$('.period-select-container', dialogContent).addClass('hidden');
						dataDialog.getButton('ok').disable();
					}
				});
				
				$('.period-select', dialogContent).change(function(e) {
					var period = $(this).val();
					
					if(period != '') {
						// a period has been selected, so enable the 'OK' button
						dataDialog.getButton('ok').enable();
					} else {
						dataDialog.getButton('ok').disable();
					}
				});
				
				var dialogButtons = [{
					id: 'ok',
					label: 'OK',
					cssClass: 'btn-primary',
					action: function(dialogRef){
						// disable the dialog elements so user can't do much until page changes
						$('.company-select, .period-select', dialogContent).attr('disabled', 'disabled');
						
						dialogRef.getButton('ok').disable();
						dialogRef.getButton('admin').disable();
							
						// redirect to main content with selected company & period
						var selectedCompany = ($('.company-select option', dialogContent).length > 2) ? $('.company-select', dialogContent).val() : $('.company-value', dialogContent).text();
						var selectedPeriod = $('.period-select', dialogContent).val();
						
						$('#formLogin input[name="company"]').val(selectedCompany);
						$('#formLogin input[name="period"]').val(selectedPeriod);
						
						$('#formLogin').submit();
						
						//window.location.href = "main.php?company="+selectedCompany+"&month="+selectedPeriod;
					}
				}];
				
				if(isAdmin) {
					dialogButtons.push({
						id: 'admin',
						label: 'Admin',
						cssClass: 'btn-link pull-left',
						action: function(dialogRef) {
							// disable the dialog elements so user can't do much until page changes
							$('.company-select, .period-select', dialogContent).attr('disabled', 'disabled');
							
							dialogRef.getButton('ok').disable();
							dialogRef.getButton('admin').disable();
							
							$('#formLogin input[name="company"]').remove()
							$('#formLogin input[name="period"]').remove();
							
							$('#formLogin').submit();
							
							// redirect to main content with no company & period
							//window.location.href = "main.php?userid4="+userid;
						}
					});
				}
				
				// show the dialog
				dataDialog = BootstrapDialog.show({
					title: 'Change Company / Period',
					message: dialogContent,
					cssClass: 'period-dialog',
					type: BootstrapDialog.TYPE_PRIMARY,
					closable: false,
					width: '150px',
					onshow: function(dialog) {
						dialog.getButton('ok').disable();
					},
					buttons: dialogButtons
				});
			} else {
				message = 'Unable to get company information.';	
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to get company information.';	
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	function updateAvailablePeriods(company, context) {
		if(!context) context = $('.modal-body');
		
		// clear any existing period options
		$('.period-select option', context).not('.period-select option[value=""]').remove();
		
		$.ajax({
		  method: "POST",
		  url: "../informer/reports.php",
		  data: { 
			  mode: 'monthyear',
			  company: company
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
                $('.period-select').find('option').remove().end();
                $('.period-select').append('<option selected="true" disabled="disabled">Select period</option>');

                var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
					
				xml.find('Value').each(function(index) {
					var month = parseInt($(this).find('month').text());
					var year = $(this).find('year').text();
					
					$('.period-select', context).append('<option value="'+month+'|'+year+'">'+monthNames[month-1]+' '+year+'</option>');
					
					if(index == 0) $('.period-set .value', context).html(monthNames[month-1]+' '+year);
				});
				
				if(xml.find('Value').length == 1) {
					$('.period-set', context).removeClass('hidden');
					
					// only a single period, so enable the 'OK' button
					dataDialog.getButton('ok').enable();
				} else {
					$('.period-select-container', context).removeClass('hidden');
				}
			} else {
				message = 'Unable to get period information.';	
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to get period information.';	
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	
	// reset password
	$('#btn-reset').click(function(e) {
		e.preventDefault();
		if($(this).hasClass('disabled')) return;
		 
		// clear any previous message
		var message = '';
		$('#forget-password .message').html('').addClass('hidden').removeClass('error').removeClass('success');
		
		var username = $('#forget-password input[name="userid"]').val();
		
		// check whether username has been entered
		if(username == '') {
			// show an error message
			message = 'You need to enter a username.';			
			$('#forget-password .message').html(message).addClass('error').removeClass('hidden');
			
			return;
		}
		
		// try to reset password
		$.ajax({
		  method: "POST",
		  url: "../user/user.php",
		  data: { 
			  mode: 'forgotpass',
			  userid: username
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
			var reason = xml.find( "message" ).text();
		  
			if(result == 'ok') {
				message = 'Your password has been reset and emailed to you.';			
				$('#forget-password .message').html(message).addClass('success').removeClass('hidden');
			} else {
				message = 'Unable to reset your password: '+reason;			
				$('#forget-password .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to reset passowrd.';			
			$('#forget-password .message').html(message).addClass('error').removeClass('hidden');
		});
	});
	
	
})();

(function() {
	function promptToChangeData(pageID) {
		// clear the selects in the dialog
        //alert(selectedMonth);
		$('.change-data-dialog select').val('');
		$('.change-data-dialog option').not('.change-data-dialog option[value=""]').remove();
		$('.period-select-container').addClass('hidden');
		
		if(typeof userid == 'undefined') alert('User ID undefined');
		
		$.ajax({
		  method: "POST",
		  url: "../user/org.php",
		  data: { 
			  mode: 'getorgassigned',
			  userid: userid
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
				var options = '';
                $('.company-select').find("option").remove().end();
                $('.company-select').append('<option selected="true" disabled="disabled">Select Organisation</option>');

                xml.find('org').each(function(index) {
					//alert($(this).find('orgname').text());
					var orgid = $(this).find('orgid').text();
					var orgname = $(this).find('orgname').text();
                    if( typeof selectedCompany != 'undefined' && (orgname == selectedCompany ) ){
                        options += '<option data-id="'+orgid+'" value="'+orgname+'" selected>'+orgname+'</option>';
                    }else{
                        options += '<option data-id="'+orgid+'" value="'+orgname+'">'+orgname+'</option>';
                    }
					//$('.company-select').append('<option data-id="'+orgid+'" value="'+orgname+'">'+orgname+'</option>');
                });

                if( typeof selectedCompany != 'undefined' ){
                    updateAvailablePeriods(selectedCompany);
                }


				// prepare the dialog content
				var dialogContent = $('.change-data-dialog').clone();
				$('.company-select', dialogContent).append(options);
				
				// set the change handlers
				$('.company-select', dialogContent).change(function(e) {
					var company = $(this).val();
					
					if(company != '') {
						updateAvailablePeriods(company);
					} else {
						$('.period-select-container').addClass('hidden');
						changeDataDialog.getButton('ok').disable();
					}
				});
				
				// show the dialog
				var changeDataDialog = BootstrapDialog.show({
					title: 'Change Company / Period',
					message: dialogContent,
					cssClass: 'period-dialog',
					type: BootstrapDialog.TYPE_PRIMARY,
					closable: false,
					width: '150px',
					onshow: function(dialog) {
						dialog.getButton('ok').disable();
					},
					onshown: function(dialog) {
						
					},
					buttons: [{
						id: 'cancel',
						label: 'Cancel',
						cssClass: 'btn-secondary',
						action: function(dialogRef){
							dialogRef.close();
						}
					},
					{
						id: 'ok',
						label: 'OK',
						cssClass: 'btn-primary',
						action: function(dialogRef){
							// disable the dialog elements so user can't do much until page changes
							$('.company-select, .period-select').attr('disabled', 'disabled');
							
							dialogRef.getButton('ok').disable();
							dialogRef.getButton('cancel').disable();
								
							// refresh with selected company & period
							var selectedCompany = $('.company-select', dialogContent).length > 0 ? $('.company-select', dialogContent).val() : $('.company-value').text();
							var selectedPeriod = $('.period-select', dialogContent).val();
							
							var path = "main.php?company="+selectedCompany+"&month="+selectedPeriod;

							if(pageID == 'menu'){
                                path += '&page=dashboard';
                               // console.log(pageID)
                            }else {
                                if(typeof pageID == 'undefined'){
                                    pageID = 'dashboard';
                                }
							    path += '&page='+pageID;
                             //   console.log(pageID)
                            }
							
							window.location.assign(path);
						}
					}]
				});
				
				// set the change handler for the period (needs dialog ref)
				$('.period-select', dialogContent).change(function(e) {
					var period = $(this).val();
					
					if(period != '') {
						// a period has been selected, so enable the 'OK' button
						changeDataDialog.getButton('ok').enable();
					} else {
						changeDataDialog.getButton('ok').disable();
					}
				});
				
				
			} else {
				message = 'Unable to get company information.';	
				$('#login .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			alert('fail');
			message = 'Failed to get company information.';	
			$('#login .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	function updateAvailablePeriods(company) {
		// clear any existing period options
		//$('.period-select option').not('.period-select option[value=""]').remove();
        $('.period-select').find("option").remove().end();
        $('.period-select').append('<option selected="true" disabled="disabled">Select Month</option>');
		
		$.ajax({
		  method: "POST",
		  url: "../informer/reports.php",
		  data: { 
			  mode: 'monthyear',
			  company: company
			}
		})
		.done(function(data) {
			var xmlData = $.parseXML( $.trim(data) );
			var xml = $(xmlData);
			var result = xml.find( "result" ).text();
		  
			if(result == 'ok') {
				xml.find('Value').each(function(index) {
					var month = parseInt($(this).find('month').text());
					var year = $(this).find('year').text();
					
					var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];


                    if( typeof selectedMonth != 'undefined' && (selectedMonth == (month+'|'+year) )){
                        $('.period-select').append('<option value="'+month+'|'+year+'" selected>'+monthNames[month-1]+' '+year+'</option>');
                    }else{
                        $('.period-select').append('<option value="'+month+'|'+year+'">'+monthNames[month-1]+' '+year+'</option>');
                    }
				});
				
				// show the period select
				$('.period-select-container').removeClass('hidden');
			} else {
				message = 'Unable to get period information.';	
				$('#error .message').html(message).addClass('error').removeClass('hidden');
			}
		})
		.fail(function() {
			message = 'Failed to get period information.';	
			$('#error .message').html(message).addClass('error').removeClass('hidden');
		});
	}
	
	$('.btn-test, [data-action="changeData"]').click(function(e) {
		var pageID = $(this).attr('data-page');
		
		promptToChangeData(pageID);
	});	
	
})();
/*
function checkform() {

	var msg="";
	
	var company=document.getElementById('company').value;
	
	if(company=="")
		msg+="Company name cannot be blank \r\n\r\n";
		
	msg+=checkpercent();
	
	if(msg!=""){
		alert(msg);
		return false;
	}
		
	return true;
}
	
function checkpercent() {

        var keys=document.getElementById('key').value;
	var msg="";
	
	for (i = 0; i < keys; i++) { 
		var cattot=0;
		var cat1val=Number(document.getElementById('cat1'+i).value);
		var cat2val=Number(document.getElementById('cat2'+i).value);
		var cat3val=Number(document.getElementById('cat3'+i).value);
		
		var cattot=cat1val+cat2val+cat3val;
		
		//alert(i+":::"+cattot);
		
		if(cattot!=100)
			msg+=document.getElementById('item'+i).value+"\r\n";
	}
	
	if(msg!="")
		msg="Total for the following items must add up to a 100 \r\n\r\n"+msg;
	
	return msg;
}

function populatefield(fld,key) {

	switch(fld) {
		case "cat1":
			if(Number(document.getElementById('cat2'+key).value)==100 || Number(document.getElementById('cat3'+key).value)==100){
				document.getElementById('cat1'+key).value='100';
				document.getElementById('cat2'+key).value='0';
				document.getElementById('cat3'+key).value='0';
			}
	        	break;
		case "cat2":
			if(Number(document.getElementById('cat1'+key).value)==100 || Number(document.getElementById('cat3'+key).value)==100){
				document.getElementById('cat1'+key).value='0';
				document.getElementById('cat2'+key).value='100';
				document.getElementById('cat3'+key).value='0';
			}
	        	break;
	        case "cat3":
	        	if(Number(document.getElementById('cat1'+key).value)==100 || Number(document.getElementById('cat2'+key).value)==100){
				document.getElementById('cat1'+key).value='0';
				document.getElementById('cat2'+key).value='0';
				document.getElementById('cat3'+key).value='100';
			}
	        	break;
	        default:
	        		document.getElementById('cat1'+key).value='100';
				document.getElementById('cat2'+key).value='0';
				document.getElementById('cat3'+key).value='0';
	} 

}

function checkblank(ele) {
	if(ele.value=="")
		ele.value='0';
}
*/

if($('.tablinks').length > 0) {
	// select the first tab link & content
	var firstTablink = $('.tablinks').eq(0);
	firstTablink.addClass('active').attr('disabled', 'disabled');

	var firstTabname = firstTablink.attr('data-value');
	firstTabname = firstTabname.split(' ').join('-');
	$('.tabcontent[data-period='+firstTabname+']').removeClass('hidden');
}


$('.tablinks').click(function(e) {
	// Get all elements with class="tabcontent" and hide them
	$('.tabcontent').addClass('hidden');
	
	// Get all elements with class="tablinks" and remove the class "active"
	$('.tablinks').removeClass('active').removeAttr('disabled');
	
	// Show the current tab, and add an "active" class to the link that opened the tab
	var tabname = $(this).attr('data-value');
	tabname = tabname.split(' ').join('-');
	$('.tabcontent[data-period='+tabname+']').removeClass('hidden');
	$(this).addClass('active').attr('disabled', 'disabled');
});

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

// file no longer used
function setComment(commentFrom, label){
    var commentBody = $(commentFrom).children("textarea");
    var commentBodytwo = tinyMCE.activeEditor.getContent();

    var company = getUrlParameter('company');
    var month = getUrlParameter('month');

    var userType = userregtype;

    //setTimeout(function(){
    //console.log(commentBody);
    //console.log(commentBodytwo);
    //}, 3000);

    var MyDate = new Date();
    var MyDateString;

    MyDateString = ('0' + MyDate.getDate()).slice(-2) + '-'
        + ('0' + (MyDate.getMonth()+1)).slice(-2) + '-'
        + MyDate.getFullYear();

    var datetime = "" + MyDateString + "  "
        + MyDate.getHours() + ":"
        + MyDate.getMinutes() + ":"
        + MyDate.getSeconds();

    var comentGoestoThisSection = $('#'+userType.toLowerCase()+'cmt-'+navTitle+' .generated-commentary');
    var greetingmessage  = $('.greeting').html();
    var userFullName = greetingmessage.split("Logged in as ").pop();


    $.ajax({
        method: "POST",
        url: "../informer/comments.php",
        data: {
            mode: 'insertcomment',
            comment : commentBodytwo,
            userid : userid,
            monthyear : selectedMonth,
            label : label,
            layer : navTitle,
            company: selectedCompany

        }
    })
    .done(function(data) {

        if($(comentGoestoThisSection).length == 0){
            $('#'+userType.toLowerCase()+'cmt-'+navTitle).prepend( '<div class="generated-commentary"><div><p>'+datetime + ' - (' + userFullName + ')</p>' + commentBodytwo + '</div></div>');
        }else {
            $(comentGoestoThisSection).prepend( '<div><p>'+datetime + ' - (' + userFullName + ')</p>' + commentBodytwo + '</div>');
        }

        bootbox.alert({message: 'Your commentary has been saved successfully.', size: 'small', className: 'success-alert alert-with-icon'});
        tinyMCE.activeEditor.setContent('');
        //console.log('send' + userType);
    })
    .fail(function() {
        bootbox.alert({message: 'Unable to save your commentary', size: 'small', className: 'danger-alert alert-with-icon'});

        console.log('fail' + userType);
    });
    

};