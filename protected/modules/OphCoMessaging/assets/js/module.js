/* Module-specific javascript can be placed here */

$(document).ready(function() {
	$(this).on('click','#et_cancel',function(e) {
		if (m = window.location.href.match(/\/update\/[0-9]+/)) {
			window.location.href = window.location.href.replace('/update/','/view/');
		} else {
			window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
		}
		e.preventDefault();
	});

	$(this).on('click','#et_print',function(e) {
		printIFrameUrl(OE_print_url, null);
		enableButtons();
		e.preventDefault();
	});

    $(this).on('click', '#add-message-comment', function() {
        $('#new-comment-form').toggle();
        $('#add-comment-button-container').toggle();
    });

    $(this).on('click', '#new-comment-cancel', function(e) {
        e.preventDefault();
        $('#new-comment-form').toggle();
        $('#add-comment-button-container').toggle();
    });

	if(document.getElementById('oe-autocompletesearch')) {
		OpenEyes.UI.AutoCompleteSearch.init({
			input: $('#oe-autocompletesearch'),
			url: '/user/autocomplete',
			onSelect: function () {
				let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
				$('#fao_user_display').html(AutoCompleteResponse.label);
				$('#OEModule_OphCoMessaging_models_Element_OphCoMessaging_Message_for_the_attention_of_user_id').val(AutoCompleteResponse.id);
				return false;
			}
		});
	}
});

