var analytics_csv_cataract = (function () {
	function pcr_risk_to_csv() {
		// get a deep copy of x, y data from plot
		// in case the further modification will affect the original data
		var data_x = $('#PcrRiskReport')[0].data[0].x.slice();
		var data_y = $('#PcrRiskReport')[0].data[0].y.slice();
		var file_name = data.name;
		data_x = data_x.concat($('#PcrRiskReport')[0].data[1].x);
		data_y = data_y.concat($('#PcrRiskReport')[0].data[1].y);
		csv_export_cataract(file_name, {x: data_x, y: data_y});
	}

	function complication_profile_to_csv() {
		var data = $('#CataractComplicationsReport')[0].data[0];
		var file_name = data.name;
		csv_export_cataract(file_name, data);
	}

	function visual_acuity_to_csv() {
		var data = $('#OEModule_OphCiExamination_components_VisualOutcomeReport')[0].data[0];
		var file_name = 'VisualOutcomeReport';
		csv_export_cataract(file_name, data);
	}

	function refractive_outcome_to_csv() {
		var data = $('#OEModule_OphCiExamination_components_RefractiveOutcomeReport')[0].data[0];
		var file_name = data.name;
		csv_export_cataract(file_name, data);
	}

	function NOD_Audit_to_csv() {
		var data = $('#NodAuditReport')[0].data[0];
		var file_name = 'NODAuditReportCompletion';
		csv_export_cataract(file_name, data);
	}

	function csv_export_cataract(filename, data) {
		filename += '.csv';
		var processData = function (x, y) {
			var finalVal = x + ',' + y + '\n';
			return finalVal;
		};

		var csvFile = 'x,y\n';
		for (var i = 0; i < data.x.length; i++) {
			csvFile += processData(data.x[i], data.y[i]);
		}

		var blob = new Blob([csvFile], {
			type: 'text/csv;charset=utf-8;'
		});
		if (navigator.msSaveBlob) {
			navigator.msSaveBlob(blob, filename);
		} else {
			var link = document.getElementById('js-download-csv');
			var url = URL.createObjectURL(blob);
			link.setAttribute("href", url);
			link.setAttribute("download", filename);
		}
	}
	var init = function () {
		switch ($('.js-cataract-report-type.selected').data('report')) {
			case 'PCR':
				pcr_risk_to_csv();
				break;
			case 'CP':
				complication_profile_to_csv();
				break;
			case 'VA':
				visual_acuity_to_csv();
				break;
			case 'RO':
				refractive_outcome_to_csv();
				break;
			case 'NOD':
				NOD_Audit_to_csv();
				break;
		}
	}
	return init;
})()