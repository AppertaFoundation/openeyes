var analytics_toolbox = (function () {
	var typeData = {
		waiting: {
			name: "Followups waiting time",
			title: "Waiting time for new patients (weeks)",
			htmlText: 'Waiting time for new patients',
			type: "bar"
		},
		overdue: {
			name: "Overdue followups",
			title: "Overdue followups (weeks)",
			htmlText: 'Overdue followups',
			type: "bar"
		},
		coming: {
			name: "Followups coming due",
			title: "Followups coming due (weeks)",
			htmlText: 'Followups coming due',
			type: "bar"
		}
	}

	function ajaxErrorHandling(statusCode, errorMsg) {
		var main = document.querySelector('.oe-analytics');
		var div = document.createElement('div');

		main.innerHTML = '';

		div.setAttribute('class', 'oe-err-msg');

		var msg = 'HTTP status code ' + statusCode + ': ' + errorMsg + '. Please contact your admin or support and try again.';

		div.innerText = msg;

		main.appendChild(div);
	}

	function getThrottleTime() {
		return 100;
	}

	function getAjaxThrottleTime() {
		return 1000;
	}

	function processDate(date) {
		return date.getFullYear().toString() + '-' +
			(date.getMonth() + 1 < 10 ? '0' : '') + (date.getMonth() + 1) +
			'-' + (date.getDate() < 10 ? '0' : '') + date.getDate();
	}

	function initDatePicker(def) {
		var date_from = typeof (def) === 'undefined' ? false : new Date(def['date_from']);
		var date_to = typeof (def) === 'undefined' ? false : new Date(def['date_to']);
		if (date_from && date_to) {
			date_from = processDate(date_from);

			date_to = processDate(date_to);
		}

		pickmeup('#analytics_datepicker_from', {
			format: 'Y-m-d',
			date: date_from,
			hide_on_select: true,
			default_date: date_from,
		});

		pickmeup('#analytics_datepicker_to', {
			format: 'Y-m-d',
			date: date_to,
			hide_on_select: true,
			default_date: date_to,
		});
	}

	function getVATitle() {
		var va_mode = $('#js-chart-filter-plot');
		var va_title;
		if (va_mode.html().includes('change')) {
			va_title = "Visual acuity change from baseline (LogMAR)";
		} else {
			va_title = "Visual acuity (LogMAR)";
		}
		return va_title;
	}

	var drillDown = $('.analytics-patient-list table').html();

	function getCurrentSpecialty() {
		return $('.analytics-options li a.selected').data('specialty');
	}

	function loadPlot(flag, data, title) {
		var service_layout = JSON.parse(JSON.stringify(analytics_layout));
		title = title || 'Patient count'
		service_layout['xaxis']['rangemode'] = 'nonnegative';
		service_layout['yaxis']['title'] = 'Patient count';
		service_layout['yaxis']['tickformat'] = 'd';
		service_layout['xaxis']['title'] = title;
		service_layout['yaxis']['range'] = [0, data.max];
		$('#js-service-data-filter').show();
		if (flag === 'init') {
			Plotly.newPlot(
				'js-hs-chart-analytics-service', [data], service_layout, analytics_options
			)
		} else {
			Plotly.react(
				'js-hs-chart-analytics-service', [data], service_layout, analytics_options
			);
		}
	}

	function getTypeData() {
		return typeData;
	}

	function getCleanDrillDownList() {
		return drillDown;
	}

	function processPlotData(type, datasource) {
		var res = {
			name: typeData[type].name,
			x: Object.keys(datasource),
			y: Object.values(datasource).map(function (item, index) {
				return item.length;
			}),
			customdata: Object.values(datasource),
			type: typeData[type].type,
		};
		var max = Math.max(...res['y']) + 5 > 20 ? Math.max(...res['y']) + 5 : 20;
		var count = res['y'].reduce((a, b) => a + b, 0);
		return {
			data: res,
			count: count,
			max: max,
			title: typeData[type].title
		}
	}

	function getCurrentShownPlotId() {
		var plot_id;
		$('.js-plotly-plot').each(function () {
			if ($(this).is(':visible')) {
				plot_id = $(this)[0].id;
				return false;
			}
		});
		return plot_id;
	}

	function getDataFilters(specialty, side_bar_user_list, common_disorders, current_user) {
		var specialty = specialty;
		var service_common_disorders = common_disorders;
		var mr_custom_diagnosis = ['AMD(wet)', 'BRVO', 'CRVO', 'DMO'];
		var gl_custom_diagnosis = ['Glaucoma', 'Open Angle Glaucoma', 'Angle Closure Glaucoma', 'Low Tension Glaucoma', 'Ocular Hypertension'];
		var mr_custom_treatment = ['Lucentis', 'Elyea', 'Avastin', 'Triamcinolone', 'Ozurdex'];
		var gl_custom_procedure = ['Cataract Extraction', 'Trabeculectomy', 'Aqueous Shunt', 'Cypass', 'SLT', 'Cyclodiode'];
		var filters = '';
		$('.js-hs-filters').each(function () {
			if ($(this).is('span')) {
				if ($(this).html() !== 'All') {
					if ($(this).hasClass('js-hs-surgeon')) {
						if (side_bar_user_list !== null) {
							filters += '&' + $(this).data('name') + '=' + side_bar_user_list[$(this).html().trim()];
						} else {
							filters += '&' + $(this).data('name') + '=' + current_user;
						}
					} else if ($(this).data('name') == "service_diagnosis") {
						filters += '&' + $(this).data('name') + '=' + Object.keys(service_common_disorders).find(key => service_common_disorders[key] === $(this).html());
					} else if ($(this).hasClass('js-hs-custom-mr-diagnosis')) {
						var diagnosis_array = $(this).html().split(",");
						var diagnoses = "";
						diagnosis_array.forEach(
							function (item) {
								diagnoses += mr_custom_diagnosis.indexOf(item) + ',';
							}
						);
						diagnoses = diagnoses.slice(0, -1);
						filters += '&' + $(this).data('name') + '=' + diagnoses;
					} else if ($(this).hasClass('js-hs-custom-mr-treatment')) {
						var treatment = mr_custom_treatment.indexOf($(this).html());
						filters += '&' + $(this).data('name') + '=' + treatment;
					} else if ($(this).hasClass('js-hs-custom-gl-procedure')) {
						var procedure = gl_custom_procedure.indexOf($(this).html());
						filters += '&' + $(this).data('name') + '=' + procedure;
					} else if ($(this).hasClass('js-hs-custom-gl-diagnosis')) {
						var diagnosis_array = $(this).html().split(",");
						var diagnoses = "";
						diagnosis_array.forEach(
							function (item) {
								diagnoses += gl_custom_diagnosis.indexOf(item) + ',';
							}
						);
						diagnoses = diagnoses.slice(0, -1);
						filters += '&' + $(this).data('name') + '=' + diagnoses;
					} else if ($(this).hasClass('js-hs-custom-mr-plot-type')) {
						if ($(this).html().includes('change')) {
							filters += '&' + $(this).data('name') + '=change';
						}
					} else {
						filters += '&' + $(this).data('name') + '=' + $(this).html();

					}
				}
			} else if ($(this).is('select')) {
				filters += '&' + $(this).data('name') + '=' + $(this).val();
			}
		});
		return filters;
	}

	function plotUpdate(data, specialty, flag) {
		if (flag === 'service') {
			var service_type = $('#js-charts-service .charts li a.selected').data('report');
			var plot_data = processPlotData(service_type, data[1]['plot_data'])
			loadPlot('update', plot_data['data'], plot_data['title']);
		} else {
			if ($('#js-hs-clinical-diagnoses').hasClass('selected')) {
				var clinical_chart = $('#js-hs-chart-analytics-clinical')[0];
				var clinical_data = data[0];
				clinical_chart.data[0]['x'] = clinical_data.x;
				clinical_chart.data[0]['y'] = clinical_data.y;
				clinical_chart.data[0]['customdata'] = clinical_data.customdata;
				clinical_chart.data[0]['text'] = clinical_data.text;
				clinical_chart.layout['yaxis']['tickvals'] = clinical_data['y'];
				clinical_chart.layout['yaxis']['ticktext'] = clinical_data['text'];
				clinical_chart.layout['hoverinfo'] = 'x+y';
				Plotly.redraw(clinical_chart);
			}
			if (specialty !== 'All') {
				if ($('#js-hs-clinical-custom').hasClass('selected')) {
					var custom_charts = ['js-hs-chart-analytics-clinical-others-left', 'js-hs-chart-analytics-clinical-others-right'];
					var custom_data = data[2];
					for (var i = 0; i < custom_charts.length; i++) {
						var chart = $('#' + custom_charts[i])[0];
						chart.layout['title'] = (i) ? 'Clinical Section (Right Eye)' : 'Clinical Section (Left Eye)';
						chart.layout['yaxis']['title'] = {
							font: {
								family: 'sans-serif',
								size: 12,
								color: '#fff',
							},
							text: getVATitle(),
						};
						//Set VA unit tick labels
						var va_mode = $('#js-chart-filter-plot');
						if (va_mode.html().includes('change')) {
							chart.layout['yaxis']['tickmode'] = 'auto';
						} else {
							chart.layout['yaxis']['tickmode'] = 'array';
							chart.layout['yaxis']['tickvals'] = JSON.parse($va_final_ticks['tick_position']);
							chart.layout['yaxis']['ticktext'] = JSON.parse($va_final_ticks['tick_labels']);
						}
						chart.data[0]['x'] = custom_data[i][0]['x'];
						chart.data[0]['y'] = custom_data[i][0]['y'];
						chart.data[0]['customdata'] = custom_data[i][0]['customdata'];
						chart.data[0]['error_y'] = custom_data[i][0]['error_y'];
						chart.data[0]['hoverinfo'] = custom_data[i][0]['hoverinfo'];
						chart.data[0]['hovertext'] = custom_data[i][0]['hovertext'];
						chart.data[1]['x'] = custom_data[i][1]['x'];
						chart.data[1]['y'] = custom_data[i][1]['y'];
						chart.data[1]['customdata'] = custom_data[i][1]['customdata'];
						chart.data[1]['error_y'] = custom_data[i][1]['error_y'];
						chart.data[1]['hoverinfo'] = custom_data[i][1]['hoverinfo'];
						chart.data[1]['hovertext'] = custom_data[i][1]['hovertext'];
						Plotly.redraw(chart);
					}
				}
			}
		}
	}


	// update UI to show how Filter works
	// this is pretty basic but only to demo on IDG
	function updateUI($optionGroup) {
		// get the ID of the IDG demo text element
		var textID = $optionGroup.data('filter-ui-id');
		var $allListElements = $('.btn-list li', $optionGroup);

		$allListElements.click(function () {
			if ($(this).parent().hasClass('js-multi-list') && $(this).text() !== "All") {
				if ($(this).hasClass('selected')) {
					if ($('#' + textID).text().includes(',')) {
						$(this).removeClass('selected');
						$('#' + textID).text($('#' + textID).text().replace($(this).text() + ",", ""));
						$('#' + textID).text($('#' + textID).text().replace("," + $(this).text(), ""));
					}
				} else {
					$(this).addClass('selected');
					$allListElements.filter(function () {
						return $(this).text() == "All";
					}).removeClass('selected');
					if ($('#' + textID).text() == "All") {
						$('#' + textID).text($(this).text());
					} else {
						$('#' + textID).text($('#' + textID).text() + ',' + $(this).text());
					}
				}
			} else {
				$('#' + textID).text($(this).text());
				$allListElements.removeClass('selected');
				$(this).addClass('selected');
			}
		});
	}
	return {
		getCurrentShownPlotId: getCurrentShownPlotId,
		getDataFilters: getDataFilters,
		plotUpdate: plotUpdate,
		updateUI: updateUI,
		getTypeData: getTypeData,
		loadPlot: loadPlot,
		processPlotData: processPlotData,
		getCurrentSpecialty: getCurrentSpecialty,
		getVATitle: getVATitle,
		getCleanDrillDownList: getCleanDrillDownList,
		initDatePicker: initDatePicker,
		processDate: processDate,
		getThrottleTime: getThrottleTime,
		getAjaxThrottleTime: getAjaxThrottleTime,
		ajaxErrorHandling: ajaxErrorHandling,
	}
})()