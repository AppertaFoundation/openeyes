var analytics_init = (function () {
	var ajaxThrottleTime = analytics_toolbox.getAjaxThrottleTime() || 1000;
	var init = function () {
		function selectSpecialty(e) {
			e.preventDefault();

			// display spinner
			$('#js-analytics-spinner').show();

			analytics_toolbox.hideDrillDownShowChart();

			$(this).addClass('selected');

			$('.icon-btn a').not(this).removeClass('selected');

			var target = this.href;

			analytics_dataCenter.ajax.setAjaxURL(target);

			var specialty = analytics_toolbox.getCurrentSpecialty();

			$('.specialty').html(specialty);

			$.ajax({
				url: target,
				type: "POST",
				data: {
					"YII_CSRF_TOKEN": YII_CSRF_TOKEN,
					"specialty": specialty,
				},
				success: function (response) {
					var data = JSON.parse(response);
					analytics_dataCenter.specialtyData.setResponseData(data)
					$('#sidebar').html(data['dom']['sidebar']);
					$('#plot').html(data['dom']['plot']);
					$('#plot').html(data['dom']['drill'])
					if (specialty.toLowerCase() === 'cataract') {
						$('#js-analytics-spinner').hide();
						analytics_cataract(data['data']);
						return;
					}
					// load Sidebar
					analytics_sidebar();
					// defaultly load Service screen
					analytics_service();
					// initialize datePicker
					analytics_toolbox.initDatePicker();
					// load drill down
					analytics_drill_down();
				},
				complete: function () {
					$('#js-analytics-spinner').hide();
				},
				error: function (jqXHR, textStatus, errorThrown) {
					analytics_toolbox.ajaxErrorHandling(jqXHR.status, errorThrown)
				}
			});
		}
		// specialty options buttons: All CA GL MR
		$('.select-analytics .oescape-icon-btns a').on('click', _.throttle(selectSpecialty, ajaxThrottleTime));

		$('#js-all-specialty-tab').click();
	}
	return init;
})()