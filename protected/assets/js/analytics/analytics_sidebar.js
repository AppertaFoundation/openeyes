var analytics_sidebar = (function () {
	// get throttle time
	var throttleTime = analytics_toolbox.getThrottleTime() || 100;
	var ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;

	var init = function () {
		// diagnosis filter in Service section, for update chart function
		var common_disorders_dom = $('.btn-list li')
		var common_disorders = common_disorders_dom.map(function (i, e) {
			return $(e).html()
		})

		// get current Specialty
		var specialty = analytics_toolbox.getCurrentSpecialty();

		function selectSpecialtyOpt() {
			// data section is used to save plot dom element id
			// Service data section: #js-hs-chart-analytics-service
			// Clinical data section: #js-hs-chart-analytics-clinical-main
			var selected_section = $(this).data('section');

			// data tab is used to save the menu dom element id under the tab
			// Service data tab: #js-charts-service
			// Clinical data tab: #js-charts-clinical
			var selected_tab = $(this).data('tab');

			// for IF statement below
			// Service data options: service
			// Clinical data options: Clinical
			var selected_option = $(this).data('options');

			// display selected tab and the things related to it
			$(this).addClass('selected');
			$('.analytics-section').not(this).removeClass('selected');
			$(selected_section).show();
			$($('.analytics-section').not(this).data('section')).hide();
			$(selected_tab).show();
			$($('.analytics-section').not(this).data('tab')).hide();

			// force display when come back from other screen, like drill down
			// and hide drill down
			$('.analytics-charts').show();
			$('.analytics-patient-list').hide();
			$('.analytics-patient-list-row').hide();

			// execute related function according to selected tab
			if (selected_option === 'clinical') {
				analytics_clinical();
			} else {
				analytics_service();
			}

			// drill down only cares plot, as the required data is with the plot
			analytics_drill_down(selected_section);
		}

		// options in clinical tab
		// All: Diagnoses
		// GL/MR: Diagnoses, Change in vision
		// ----------------------------------
		// Diagnoses: bring up Diagnoses plot
		// Change in vision: send ajax request for the plot
		function selectClinicalOpt(e) {
			// for Diagnoses
			e.preventDefault();
			e.stopPropagation();
			$(this).addClass('selected');
			$('.clinical-plot-button').not(this).removeClass('selected');
			$('.js-hs-chart-analytics-clinical').hide();
			$('.js-hs-filter-analytics-clinical').hide();
			$($(this).data('filterid')).show();
			$($(this).data('plotid')).show();

			// Change in vision selection
			if ($(this).text().trim().toLowerCase() === 'change in vision') {
				$('#js-analytics-spinner').show();
				$.ajax({
					url: '/analytics/getCustomPlot',
					data: {
						"YII_CSRF_TOKEN": YII_CSRF_TOKEN,
						specialty: specialty,
					},
					dataType: 'json',
					success: function (data) {
						// update custom data
						analytics_dataCenter.custom.setCustomData(data);

						// custom plot
						analytics_custom()

						// enable csv download for custom data
						// the parameter indicate if the csv download is for 
						// custom data or not
						analytics_csv_download(true)
                    },
                    complete: function(){
                        $('#js-analytics-spinner').hide();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown)
                    }
				});
			}
		}

		// update chart button
		function updateChart(e) {
			e.preventDefault();
			e.stopPropagation();

			// get user info
			var side_bar_user_list = analytics_dataCenter.user.getSidebarUser();
			var current_user = analytics_dataCenter.user.getCurrentUser();

			// Service data options: service
			// Clinical data options: Clinical
			var selected_options = $('.analytics-section.selected').data('options')

			// get current plot for hiding and displaying
			var current_plot = $("#" + analytics_toolbox.getCurrentShownPlotId());

			current_plot.hide();

			$('#js-analytics-spinner').show();
			$.ajax({
				url: '/analytics/updateData',
				data: $('#search-form').serialize() +
					analytics_toolbox.getDataFilters(specialty, side_bar_user_list, common_disorders, current_user) +
					'&report=' + $('#js-charts-service .charts li a.selected').data('report'),
				dataType: 'json',
				success: function (data) {
					// TODO: only update current plot

					// data structure
					// data[0]: clinical data
					// data[1]: service data
					// data[2]: custom data

					// for updating service and clinical data
					analytics_dataCenter.clinical.setClinicalData(data[0]);
					analytics_dataCenter.service.setServiceData(data[1]);

					current_plot.show();
                    
					// update plot and refresh csv download
					if (selected_options === 'clinical') {
                        analytics_toolbox.plotUpdate(data, specialty, 'clinical');
						analytics_csv_download();
					} else {
                        analytics_toolbox.plotUpdate(data, specialty, 'service');
						analytics_csv_download();
					}
                },
                complete: function(){
                    $('#js-analytics-spinner').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown)
				}
			});
		}
		//Service, Clinical tab click
		$('.analytics-section').off('click').on('click', _.throttle(selectSpecialtyOpt, throttleTime));

		// according to filter (green button, hover over will display filter options) selection
		// update filters text (below filter button)
		$('.oe-filter-options').each(function () {
			var id = $(this).data('filter-id');
			/*
                @param $wrap
                @param $btn
                @param $popup
            */
			enhancedPopupFixed(
				$('#oe-filter-options-' + id),
				$('#oe-filter-btn-' + id),
				$('#filter-options-popup-' + id)
			);

			// workout fixed poition

			var $allOptionGroups = $('#filter-options-popup-' + id).find('.options-group');
			$allOptionGroups.each(function () {
				// listen to filter changes in the groups
				analytics_toolbox.updateUI($(this));
			});

		});

		// from original code, don't where is it...
		$('#js-chart-filter-global-anonymise').off('click').on('click', function () {
			if (this.checked) {
				$('.drill_down_patient_list').hide();
			} else {
				$('.drill_down_patient_list').show();
			}
		});

		// from original code, don't where is it...
		var clinical_custom = $('#js-hs-clinical-custom')[0];
		if (clinical_custom) {
			$(clinical_custom).off('click').on('click', function () {
				$(this).addClass('selected');
				$('.clinical-plot-button').not(this).removeClass('selected');
			});
		}

		// bind click event on options in clinical tab
		// All: Diagnoses
		// GL/MR: Diagnoses, Change in vision
		$('.clinical-plot-button').off('click').on('click', _.throttle(selectClinicalOpt, ajaxThrottleTime));

		// bind submit event on search form wich is triggered bt Update Chart button
		$('#search-form').off('submit').on('submit', _.throttle(updateChart, ajaxThrottleTime));
	}
	return init;
})();