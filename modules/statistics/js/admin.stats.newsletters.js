var g_nbsChart = {};
jQuery(document).ready(function(){
	_nbsPrepareIntVals();
	google.charts.load('current', {'packages':['corechart']});
	google.charts.setOnLoadCallback( nbsInitCharts );
});
function nbsInitCharts() {
	nbsDrawSimplePie([
		[ toeLangNbs('Waiting'), toeLangNbs('Sent') ]
	,	[ toeLangNbs('Waiting'), (nbsNewsletter.queued - nbsNewsletter.sent_cnt) ]
	,	[ toeLangNbs('Sent'), nbsNewsletter.sent_cnt ]
	], 'nbsSentPieStats');
	
	if(nbsNewsletter.sent_cnt) {
		nbsDrawSimpleChart([
			[ toeLangNbs('Label'), toeLangNbs('Actions') ]
		,	[ toeLangNbs('Sent'), nbsNewsletter.sent_cnt ]
		,	[ toeLangNbs('Opened'), nbsNewsletter.open ]
		,	[ toeLangNbs('Unique Opened'), nbsNewsletter.uniq_open	 ]
		,	[ toeLangNbs('Clicked'), nbsNewsletter.click ]
		], 'nbsTotalStats', {
			type: 'bar'
		});
	} else {
		jQuery('#nbsTotalStatsShell').hide();
		jQuery('#nbsTotalStatsNoDataShell').show();
	}
}
function nbsDrawSimplePie( data, elmId, params ) {
	var dataTbl = google.visualization.arrayToDataTable( data )
	,	options = {
			legend: { position: 'right' }
		,	height: 250
		,	chartArea: {top: 10, left: 30}
	};

	var chart = new google.visualization.PieChart(document.getElementById( elmId ));

	chart.draw(dataTbl, options);
}
function nbsDrawSimpleChart( data, elmId, params ) {
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
function _nbsPrepareIntVals() {
	var intKeys = ['open', 'uniq_open', 'click', 'queued', 'sent_cnt', 'subscribers_cnt'];
	for(var i = 0; i < intKeys.length; i++) {
		if(nbsNewsletter[ intKeys[i] ]) {
			nbsNewsletter[ intKeys[i] ] = parseInt( nbsNewsletter[ intKeys[i] ] );
		}
	}
}