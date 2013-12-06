
/* Module-specific javascript can be placed here */

$(document).ready(function() {
	<?php if (isset($elements) && !empty($elements)) {?>
		handleButton($('#et_save'),function() {
			<?php foreach ($elements as $element) {
				foreach ($element['fields'] as $field) {
					if ($field['type'] == 'EyeDraw' && @$field['extra_report']) {?>
			if ($('#<?php echo $element['class_name']?>_<?php echo $field['name']?>2').length >0) {
				$('#<?php echo $element['class_name']?>_<?php echo $field['name']?>2').val(ed_drawing_edit_<?php echo $field['eyedraw_class']?>.report());
			}
					<?php }
				}
			}?>
		});
	<?php }else{?>
		handleButton($('#et_save'));
	<?php }?>

	handleButton($('#et_cancel'),function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+et_patient_id;
		}
		e.preventDefault();
	});

	handleButton($('#et_deleteevent'));

	handleButton($('#et_canceldelete'),function(e) {
		if (m = window.location.href.match(/\/delete\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/delete/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+et_patient_id;
		}
		e.preventDefault();
	});

	handleButton($('#et_print'),function(e) {
		printIFrameUrl(OE_print_url, null);
		enableButtons();
		e.preventDefault();
	});

	$('select.populate_textarea').unbind('change').change(function() {
		if ($(this).val() != '') {
			var cLass = $(this).parent().parent().parent().attr('class').match(/Element.*/);
			var el = $('#'+cLass+'_'+$(this).attr('id'));
			var currentText = el.text();
			var newText = $(this).children('option:selected').text();

			if (currentText.length == 0) {
				el.text(ucfirst(newText));
			} else {
				el.text(currentText+', '+newText);
			}
		}
	});
});

function ucfirst(str) { str += ''; var f = str.charAt(0).toUpperCase(); return f + str.substr(1); }

function eDparameterListener(_drawing) {
	if (_drawing.selectedDoodle != null) {
		// handle event
	}
}
