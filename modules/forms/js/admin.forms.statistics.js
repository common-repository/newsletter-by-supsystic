var g_nbsChart = {};
jQuery(document).ready(function(){
	if(typeof(google) === 'undefined') {
		alert('Please check your Internet connection - we need it to load Google Charts Library from Google Server');
		return false;
	}
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback(nbsFormInitCharts);
});
function nbsFormInitCharts() {
	// Main charts
	if(typeof(nbsFormBaseStats) !== 'undefined') {
		nbsFormDrawBaseChart( [
			[ toeLangNbs('Label'), toeLangNbs('Actions') ]
		,	[ toeLangNbs('Views'), parseInt(nbsFormBaseStats.views) ]
		,	[ toeLangNbs('Unique Views'), parseInt(nbsFormBaseStats.unique_views) ]
		,	[ toeLangNbs('Actions'), parseInt(nbsFormBaseStats.actions) ]
		], 'nbsFormTotalStats', {
			type: 'bar'
		});
	} else {
		_nbsSwitchToNoStats('nbsFormTotalStats');
	}
}
function _nbsSwitchToNoStats(chartId) {
	jQuery('.nbsChartShell[data-chart="'+ chartId+ '"]').hide();
	jQuery('.nbsNoStatsMsg[data-chart="'+ chartId+ '"]').show();
}
function nbsFormDrawBaseChart( data, elmId, params ) {
	var chartType = params.type ? params.type : 'line';

	var baseInitData = {
		tbl: null, chart: null, allData: data
	};
	if(g_nbsChart[ elmId ] && g_nbsChart[ elmId ].viewportRefreshed) {
		baseInitData.viewportRefreshed = g_nbsChart[ elmId ].viewportRefreshed;
	}
	g_nbsChart[ elmId ] = baseInitData;
	g_nbsChart[ elmId ].tbl = google.visualization.arrayToDataTable( data );
	var options = {
		legend: { position: 'right' }
	,	height: 350
	,	explorer: {
			actions: ['dragToZoom', 'rightClickToReset']
		,	axis: 'horizotal'
		}
	,	animation:{
			duration: 1000
		,	easing: 'out'
		}
	,	isStacked: true
	};
	switch(chartType) {
		case 'bar':
			g_nbsChart[ elmId ].chart = new google.visualization.BarChart(document.getElementById( elmId ));
			break;
		case 'line': default:
			g_nbsChart[ elmId ].chart = new google.visualization.LineChart(document.getElementById( elmId ));
			break;
	}
	g_nbsChart[ elmId ].options = options;
	g_nbsChart[ elmId ].chart.draw(g_nbsChart[ elmId ].tbl, options);
}
function nbsRefreshCharts() {
	for(var elmId in g_nbsChart) {
		if(g_nbsChart[ elmId ] 
			&& g_nbsChart[ elmId ].chart 
			&& g_nbsChart[ elmId ].tbl
			&& !g_nbsChart[ elmId ].viewportRefreshed
		) {
			g_nbsChart[ elmId ].chart.draw(g_nbsChart[ elmId ].tbl, g_nbsChart[ elmId ].options);
			g_nbsChart[ elmId ].viewportRefreshed = true;	// To refresh it only once - when first time open statistics tab
		}
	}
}


