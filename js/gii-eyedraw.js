$(document).ready(function() {
	$('input[name^="eyedrawSize"]').die('keypress').live('keypress',function(e) {
		return (e.keyCode != 13);
	});

	$('.eyeDrawClassSelect').live('change',function() {
		var m = $(this).attr('name').match(/^eyedrawClass([0-9]+)Field([0-9]+)$/);
		var element = m[1];
		var field = m[2];
		var selected = $(this).children('option:selected').val();

		if (selected != '') {
			$.ajax({
				'url': '/gii/EventTypeModule?ajax=getEyedrawSize&class='+selected,
				'type': 'GET',
				'success': function(size) {
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
				}
			});
		} else {
			$('input[name="eyedrawSize'+element+'Field'+field+'"]').val('');
			$('#eyeDrawExtraReportFieldDiv'+element+'Field'+field).html('');
		}
	});
});
