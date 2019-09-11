var analytics_custom = (function () {
	var init = function (data) {
		var custom_layout = JSON.parse(JSON.stringify(analytics_layout));
		var custom_data = data['custom_data'];
		console.log(custom_data)
		var va = data['va_final_ticks'];
		var specialty = analytics_toolbox.getCurrentSpecialty();
		// window.csv_data_for_report['custom_data'] = custom_data['csv_data'];
		custom_layout['xaxis']['title'] = "Time post procedure (weeks)";
		custom_layout['xaxis']['rangeslider'] = {};
		custom_layout['yaxis']['title'] = analytics_toolbox.getVATitle();

		//Set VA unit tick labels
		var va_mode = $('#js-chart-filter-plot');
		if (va_mode.html().includes('absolute')) {
			custom_layout['yaxis']['tickmode'] = 'array';
			custom_layout['yaxis']['tickvals'] = va['tick_position'];
			custom_layout['yaxis']['ticktext'] = va['tick_labels'];
		} else {
			custom_layout['yaxis']['tickmode'] = 'auto';
		}

		custom_layout['yaxis2'] = {
			title: specialty === 'Glaucoma' ? "IOP (mm Hg)" : "CRT &mu;m",
			titlefont: {
				family: 'sans-serif',
				size: 12,
				color: '#fff',
			},
			side: 'right',
			overlaying: 'y',
			linecolor: '#fff',
			tickcolor: '#fff',
			tickfont: {
				color: '#fff',
			},
		};
		plot(true, custom_layout, custom_data[1]);
		plot(false, custom_layout, custom_data[0])
		$('#js-btn-selected-eye').click(function (e) {
			$('#js-chart-filter-eye-side').trigger("changeEyeSide");
		});
		$('#js-chart-filter-eye-side').bind("changeEyeSide", function () {
			var side = $('#js-chart-filter-eye-side').text().toLowerCase();
			var opposite_side = side == 'left' ? 'right' : 'left';
			$('#js-hs-chart-analytics-clinical-others-' + side).show();
			$('#js-hs-chart-analytics-clinical-others-' + opposite_side).hide();
		});

		$('#js-chart-filter-age').on('DOMSubtreeModified', function () {
			if ($('#js-chart-filter-age').html() == "Range") {
				$('#js-chart-filter-age-all').hide();
				$('#js-chart-filter-age-min').addClass('js-hs-filters');
				$('#js-chart-filter-age-max').addClass('js-hs-filters');
				$('#js-chart-filter-age-range').show();
			} else {
				$('#js-chart-filter-age-range').hide();
				$('#js-chart-filter-age-min').removeClass('js-hs-filters');
				$('#js-chart-filter-age-max').removeClass('js-hs-filters');
				$('#js-chart-filter-age-all').show();
			}
		});

		function plot(right, custom_layout, custom_data) {
			var id;
			if (right) {
				id = 'js-hs-chart-analytics-clinical-others-right';
				custom_layout['title'] = "Clinical Section (Right Eye)";
			} else {
				id = 'js-hs-chart-analytics-clinical-others-left';
				custom_layout['title'] = "Clinical Section (Left Eye)";
			}

			var custom_plot = document.getElementById(id);
			Plotly.newPlot(
				id, custom_data, custom_layout, analytics_options
			);

			analytics_drill_down(custom_plot, custom_data);
			// custom_plot.on('plotly_click', function (data) {
			//     for (var i = 0; i < data.points.length; i++) {
			//         $('.analytics-charts').hide();
			//         $('.analytics-patient-list').show();
			//         $('.analytics-patient-list-row').hide();
			//         var patient_show_list = data.points[i].customdata;
			//         for (var j = 0; j < patient_show_list.length; j++) {
			//             $('#' + patient_show_list[j]).show();
			//         }
			//     }
			// });
		}
	}

	return init;
})()