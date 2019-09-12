var analytics_drill_down = (function () {
	var ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;
	// for pagination
	var start = 0;
	var limit = 300;

	// flag to indicate if reach max
	var reachedMax = false;

	// for deep copy of data
	var g_data = [];

	function patientDetails() {
		$('#js-analytics-spinner').show();
		var link = $(this).data('link');
		window.location.href = link;
	}

	function getPatient() {
		// if reach max, exit immediately
		if (reachedMax && g_data.length <= 0) {
			$('#js-analytics-spinner').hide();
			return;
		}
		$.ajax({
			url: '/analytics/getdrilldown',
			type: "POST",
			data: {
				drill: true,
				YII_CSRF_TOKEN: YII_CSRF_TOKEN,
				ids: JSON.stringify(g_data.splice(start, limit)),
				specialty: analytics_toolbox.getCurrentSpecialty(),
			},
			dataType: 'json',
			success: function (response) {
				if (response == "reachedMax") {
					reachedMax = true;
					$('#js-analytics-spinner').hide();
				} else {
					$("#p_list").append(response);
				}
				$('#p_list tr.analytics-patient-list-row.clickable').on("click", _.throttle(patientDetails, ajaxThrottleTime))
			},
			complete: function () {
				$('#js-analytics-spinner').hide();
			},
			error: function (jqXHR, textStatus, errorThrown) {
				analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown)
			}
		});
	}

	function scrollPatientList() {
		// get screen hight (header not included)
		var scroll_h = document.querySelector("main.oe-analytics").scrollHeight;

		// if scroll bar reach 2/3 height of the screen, send out request
		if ($(this).scrollTop() > scroll_h - scroll_h / 3) {
			if ($('#js-analytics-spinner').css('display') === 'block') {
				return;
			}
			$('#js-analytics-spinner').show();
			getPatient();
		}
	}

	var init = function (ele) {
		// if no parameter ele passed in, use service screen. only for initialization
		var ele = typeof (ele) === 'undefined' ? $('.analytics-section.selected').data('section') : ele;
		var plot_patient = typeof (ele) === 'object' ? ele : document.getElementById(ele.replace('#', ''));

		var custom_data = null;
		$(plot_patient).off('plotly_click').on('plotly_click', function (e, data) {
			// data structure: {event, points}
			/*
			    points[0] = {
			        curveNumber,
			        customdata, <- id list for clicked bar or point
			        data,       <- plot info/settings
			        fullData,   <- all plot data/settings
			        pointIndex,
			        pointNumber,
			        x,
			        xaxis,
			        y,
			        yaxis,
			    }
			*/
			custom_data = data.points[0].customdata

			var specialty = analytics_toolbox.getCurrentSpecialty();

			// get patient list dom
			var patient_list_container = $('.analytics-patient-list');
			// reset patient list dom layout
			$(patient_list_container).find('table').html(analytics_toolbox.getCleanDrillDownList());
			var colGroup = $('.analytics-patient-list table colgroup')

			if (Array.isArray(custom_data)) {
				$('#js-analytics-spinner').show();
				$('.analytics-charts').hide();
				patient_list_container.show();

				// set up patient list dom layout for cataract
				if (specialty === 'Cataract') {
					patient_list_container.addClass('analytics-event-list');
					$('<th class="text-left" style="vertical-align: center;">Eye</th>').insertBefore('.analytics-patient-list .patient_procedures');
					$('<th style="vertical-align: center;">Date</th>').insertAfter('.analytics-patient-list .patient_procedures');
					colGroup.append('<col style="width: 350px;"><col style="width: 50px;"><col style="width: 400px;"><col style="width: 100px;">')
				} else {
					// set up patient list dom layout for non cataract
					colGroup.append('<col style="width: 450px;"><col style="width: 450px;">')
				}
				// deep copy from passed in data
				g_data = custom_data.slice();
				getPatient();
			} else {
				// clicked item has further plot
				analytics_clinical('update', custom_data);
			}
		});
		$('main.oe-analytics').off('scroll').on('scroll', _.throttle(scrollPatientList, ajaxThrottleTime))

		$('#js-back-to-chart').off('click').on('click', function () {
			// reset
			reachedMax = false;
			start = 0;
			
			analytics_toolbox.hideDrillDownShowChart();
		})

	}
	return init;
})();