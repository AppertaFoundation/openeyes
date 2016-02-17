
$(document).ready(function() {
	$('#summary').click(function() {
		if ($(this).is(':checked')) {
			$('.examinationInformation').slideUp('fast');
		} else {
			$('.examinationInformation').slideDown('fast');
		}
	});
});
