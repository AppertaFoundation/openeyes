var analytics_service = (function () {
	// var typeData = {
	// 	waiting: {
	// 		name: "Followups waiting time",
	// 		title: "Waiting time for new patients (weeks)",
	// 		htmlText: 'Waiting time for new patients',
	// 		type: "bar"
	// 	},
	// 	overdue: {
	// 		name: "Overdue followups",
	// 		title: "Overdue followups (weeks)",
	// 		htmlText: 'Overdue followups',
	// 		type: "bar"
	// 	},
	// 	coming: {
	// 		name: "Followups coming due",
	// 		title: "Followups coming due (weeks)",
	// 		htmlText: 'Followups coming due',
	// 		type: "bar"
	// 	}
  // }

	// function loadPlot(flag, data, title) {
	// 	console.log(data);
	// 	var service_layout = JSON.parse(JSON.stringify(analytics_layout));
	// 	title = title || 'Patient count'
	// 	service_layout['xaxis']['rangemode'] = 'nonnegative';
	// 	service_layout['yaxis']['title'] = 'Patient count';
	// 	service_layout['yaxis']['tickformat'] = 'd';
	// 	service_layout['xaxis']['title'] = title;
	// 	service_layout['yaxis']['range'] = [0, data.max];
	// 	$('#js-service-data-filter').show();
	// 	if (flag === 'init') {
	// 		Plotly.newPlot(
	// 			'js-hs-chart-analytics-service', [data], service_layout, analytics_options
	// 		)
	// 	} else {
	// 		Plotly.react(
	// 			'js-hs-chart-analytics-service', [data], service_layout, analytics_options
	// 		);
	// 	}
	// }

	// function processPlotData(template, type, datasource) {
	// 	// console.log(datasource)
	// 	var res = {
	// 		name: template[type].name,
	// 		x: Object.keys(datasource),
	// 		y: Object.values(datasource).map(function (item, index) {
	// 			return item.length;
	// 		}),
	// 		customdata: Object.values(datasource),
	// 		type: template[type].type,
	// 	};
	// 	var max = Math.max(...res['y']) + 5 > 20 ? Math.max(...res['y']) + 5 : 20;
	// 	var count = res['y'].reduce((a, b) => a + b, 0);
	// 	return {
	// 		data: res,
	// 		count: count,
	// 		max: max,
	// 		title: template[type].title
	// 	}
  // }
  
	var init = function (service_data, url) {
		var service_data = service_data;
		analytics_csv_download(service_data['csv_data']);
		var typeData = analytics_toolbox.getTypeData();
		var report_type_links = $('#js-charts-service .charts li a')
		report_type_links.each(function () {
			var type = $(this).data('report')
			$(this).text(typeData[type].htmlText + '(' + service_data['data_sum'][type] + ')')
		})
		
		var overdue_raw = analytics_toolbox.processPlotData('overdue', service_data['plot_data']);
    analytics_toolbox.loadPlot('init', overdue_raw.data, overdue_raw.title)
    report_type_links.off('click')
		report_type_links.on('click', function (e) {
			var filter_group = $('.options-group');
			$(filter_group.find('ul.btn-list li')[0]).click();
			// console.log('about to execute updateUI')
			// analytics_toolbox.updateUI($(filter_group));
      $('#js-analytics-spinner').show();
      if($('.analytics-patient-list').css('display') === 'block'){
        $('.analytics-patient-list').hide();
      }
      $('#js-hs-chart-analytics-service').hide();
      // $('#js-hs-chart-analytics-service').show();
      
			$(this).addClass('selected');
			$('#js-charts-service .charts li a').not(this).removeClass('selected');
			$('#js-hs-app-new').removeClass('selected');
			e.stopPropagation();
			var type = $(this).data('report');
			$.ajax({
				url: url,
				type: "POST",
				data: {
					YII_CSRF_TOKEN: YII_CSRF_TOKEN,
					report: type,
					specialty: analytics_toolbox.getCurrentSpecialty(),
				},
				dataType: 'json',
				success: function (data) {
					// window.csv_data_for_report['service_data'] = data['csv_data']
					// console.log(data['plot_data']);
					var plot_data = analytics_toolbox.processPlotData(type, data['plot_data']);
          analytics_toolbox.loadPlot('click', plot_data.data, plot_data.title);
          // console.log($('#js-hs-chart-analytics-service').css('display'))
          // console.log(data);
          analytics_csv_download(data['csv_data']);
          if($('.analytics-charts').css('display') === 'none'){
            $('.analytics-charts').show()
          }
          $('#js-hs-chart-analytics-service').show();
          // $('#js-hs-chart-analytics-service').css('display:block;');
          $('#js-analytics-spinner').hide();
				}
			})
		});
	}
	return init;
})();