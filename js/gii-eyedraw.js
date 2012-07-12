$(document).ready(function() {
	$('.eyeDrawClassSelect').live('change',function() {
		var selected = $(this).children('option:selected').val();

		if (selected != '') {
			$(this).ajaxCall('getEyedrawSize',{"class":selected},function(size, element, field) {
				$('input[name="eyedrawSize'+element+'Field'+field+'"]').val(size).select().focus();

				switch(selected) {
					case 'Buckle':
					case 'Cataract':
					case 'Vitrectomy':
						if (!$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html().match(/eyedrawExtraReport/)) {
							$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html('<input type="checkbox" name="eyedrawExtraReport'+element+'Field'+field+'" value="1" /> Store eyedraw report data in hidden input<br/>');
						}
						break;
					default:
						$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html('');
						break;
				}
			});
		} else {
			$('input[name="eyedrawSize'+$(this).getElement()+'Field'+$(this).getField()+'"]').val('');
			$('#eyeDrawExtraReportFieldDiv'+$(this).getElement()+'Field'+$(this).getField()).html('');
		}
	});
});
