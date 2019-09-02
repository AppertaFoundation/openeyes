var analytics_sidebar = (function () {
	var init = function (data, target, side_bar_user_list, current_user) {
		console.log(data);
		var common_disorders_dom = $('.btn-list li')
		var common_disorders = common_disorders_dom.map(function (i, e) {
			return $(e).html()
		})
		// console.log(common_disorders)
		analytics_service(data['service_data'], target);
		// console.log(data);

		analytics_toolbox.initDatePicker();

		var specialty = analytics_toolbox.getCurrentSpecialty();
		// in case 
		$('.analytics-section').off('click')
		//select tag between clinic, custom and service
		$('.analytics-section').on('click', function () {
			// $('.analytics-section').each(function () {
			//     if ($(this).hasClass('selected')){
			//         $(this).removeClass('selected');
			//         $($(this).data('section')).hide();
			//         $($(this).data('tab')).hide();
			//     }
			// });
			var selected_section = $(this).data('section');
			var selected_tab = $(this).data('tab');
			$(this).addClass('selected');
			$('.analytics-section').not(this).removeClass('selected');
			$(selected_section).show();
			$($('.analytics-section').not(this).data('section')).hide();
			$(selected_tab).show();
			$($('.analytics-section').not(this).data('tab')).hide();
			$('.analytics-charts').show();
			$('.analytics-patient-list').hide();
			$('.analytics-patient-list-row').hide();
			// console.log(selected_section)
			if (selected_section.includes('clinical')) {
				analytics_clinical('', data['clinical_data']);
			} else {
				analytics_service(data['service_data'], target);
			}
			analytics_drill_down(selected_section, data['clinical_data']);
		});

		$('.oe-filter-options').each(function () {
			var id = $(this).data('filter-id');
			// console.log(id);
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
				// console.log($(this))
				analytics_toolbox.updateUI($(this));
			});

		});

		$('#js-chart-filter-global-anonymise').on('click', function () {
			if (this.checked) {
				$('.drill_down_patient_list').hide();
			} else {
				$('.drill_down_patient_list').show();
			}
		});

		var clinical_custom = $('#js-hs-clinical-custom')[0];
		if (clinical_custom) {
			$(clinical_custom).off('click');
			$(clinical_custom).on('click', function () {
				$(this).addClass('selected');
				$('.clinical-plot-button').not(this).removeClass('selected');

			})
		}
		$('.clinical-plot-button').off('click');
		$('.clinical-plot-button').on('click', function () {
			$(this).addClass('selected');
			$('.clinical-plot-button').not(this).removeClass('selected');
			$('.js-hs-chart-analytics-clinical').hide();
			$('.js-hs-filter-analytics-clinical').hide();
			$($(this).data('filterid')).show();
			$($(this).data('plotid')).show();
			if ($(this).text().trim().toLowerCase() === 'change in vision') {
				$('#js-analytics-spinner').show();
				$.ajax({
					url: '/analytics/getCustomPlot',
					// specialty, side_bar_user_list, common_disorders
					data: {
						"YII_CSRF_TOKEN": YII_CSRF_TOKEN,
						specialty: specialty,
					},
					dataType: 'json',
					success: function (data, textStatus, jqXHR) {
						console.log(data)
						analytics_custom(data)
						analytics_csv_download(data['custom_data']['csv_data'], true)
					},
					complete: function () {
						$('#js-analytics-spinner').hide();
					}
				});
			}
		});
		$('#search-form').off('submit')
		$('#search-form').on('submit', function (e) {
			// console.log($('#search-form').serialize());
			// console.log($('#search-form').serialize() + analytics_toolbox.getDataFilters(specialty, side_bar_user_list, common_disorders));
			// console.log(specialty, side_bar_user_list, common_disorders)
			// console.log(e);
			// console.log('fired');
			e.preventDefault();
			var selected_section = $('.analytics-section.selected').data('section')
			// console.log(selected_section)
			let current_plot = $("#" + analytics_toolbox.getCurrentShownPlotId());
			current_plot.hide();
			// console.log(current_plot)
			$('#js-analytics-spinner').show();
			$.ajax({
				url: '/analytics/updateData',
				data: $('#search-form').serialize() + analytics_toolbox.getDataFilters(specialty, side_bar_user_list, common_disorders, current_user),
				dataType: 'json',
				success: function (data, textStatus, jqXHR) {
					console.log(data[2])
					$('#js-analytics-spinner').hide();
					current_plot.show();
					console.log(data)
					if (selected_section.includes('clinical')) {
						analytics_toolbox.plotUpdate(data, specialty, 'clinical');
						analytics_csv_download(data[0]['csv_data']);
					} else {
						analytics_toolbox.plotUpdate(data, specialty, 'service');
						analytics_csv_download(data[1]['csv_data']);
					}
				}
			});
		});
	}
	return init;
})();