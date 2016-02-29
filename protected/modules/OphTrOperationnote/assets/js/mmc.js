$(document).ready(function () {
	function updateApplicationType() {
		$('.ophtroperationnote-mmc-application').hide();
		$('#ophtroperationnote-mmc-' + $(this).find(':selected').text().toLowerCase()).show();
	}

	function updateDose() {
		var conc = $('#Element_OphTrOperationnote_Mmc_concentration_id').find(':selected').text();
		var vol = $('#Element_OphTrOperationnote_Mmc_volume_id').find(':selected').text();

		$('#ophtroperationnote-mmc-dose').text((conc * vol).toFixed(2));
	}

	var app_type = $('#Element_OphTrOperationnote_Mmc_application_type_id');
	app_type.change(updateApplicationType);
	updateApplicationType.call(app_type);

	$('#Element_OphTrOperationnote_Mmc_concentration_id').change(updateDose);
	$('#Element_OphTrOperationnote_Mmc_volume_id').change(updateDose);
	updateDose();
});
