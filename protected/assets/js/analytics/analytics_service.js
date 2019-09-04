var analytics_service = (function () {
	var init = function () {
		// get ajax url
		var url = analytics_dataCenter.ajax.getAjaxURL();
		// get service_data
		var service_data = analytics_dataCenter.service.getServiceData();
		// force csv update the data inside
		analytics_csv_download();

		// get related data according to report type
		var typeData = analytics_toolbox.getTypeData();

		// report_type_links: followups, overdue, waiting
		var report_type_links = $('#js-charts-service .charts li a')

		// put total number of each report onto the dom
		report_type_links.each(function () {
			var type = $(this).data('report')
			$(this).text(typeData[type].htmlText + '(' + service_data['data_sum'][type] + ')')
		})

		// defaultly load overdue report
		var overdue_raw = analytics_toolbox.processPlotData('overdue', service_data['plot_data']);
		analytics_toolbox.loadPlot('init', overdue_raw.data, overdue_raw.title)

		// bind click event
		report_type_links.off('click').on('click', function (e) {
			e.stopPropagation();
			$('#js-analytics-spinner').show();

			// reset filter to All
			var filter_group = $('#js-charts-service .options-group');
			$(filter_group.find('ul.btn-list li')[0]).click();

			// hide drill down patient list
			if ($('.analytics-patient-list').css('display') === 'block') {
				$('.analytics-patient-list').hide();
			}

			$('#js-hs-chart-analytics-service').hide();

			$(this).addClass('selected');
			$('#js-charts-service .charts li a').not(this).removeClass('selected');
			$('#js-hs-app-new').removeClass('selected');

			// get current clicked report type
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
					// update service data
					analytics_dataCenter.service.setServiceData(data);

					// redo the plotting
					var plot_data = analytics_toolbox.processPlotData(type, data['plot_data']);
					analytics_toolbox.loadPlot('click', plot_data.data, plot_data.title);

					// force csv update the data inside
					analytics_csv_download();

					// bring back hidden plot
					if ($('.analytics-charts').css('display') === 'none') {
						$('.analytics-charts').show()
					}
					$('#js-hs-chart-analytics-service').show();

					$('#js-analytics-spinner').hide();
				}
			})
		});
	}
	return init;
})();