$(document).ready(function() {
	$(this).on('click', '#clear-diagnosis-widget', function(e) {
		$('#enteredDiagnosisText').hide();
		$('#savedDiagnosis').val('');
	});
});