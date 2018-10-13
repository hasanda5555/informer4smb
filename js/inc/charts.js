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
	
	if($(graphName)[0]) {
		// TEST - example graph with hard-coded data (the data would be passed in as a parameter)
		$.plot($(graphName),[ 
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
		});
	}
}
