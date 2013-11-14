$(document).ready(function() {

	(function patientWarningTooltip() {

		var warning = $('.panel.patient .warning');
		if (!warning.length) {
			return;
		}
		var messages = warning.find('.messages');
		var box = $('<div class="quicklook warning"></div>');

		box.hide();
		box.html(messages.html());
		box.appendTo('body');

		warning.hover(function() {

			var offsetPos = $(this).offset();
			var top = offsetPos.top + $(this).height() + 6;
			var middle = offsetPos.left + $(this).width()/2;
			var left = middle - box.width()/2 - 8;

			box.css({
					position: 'absolute',
					top: top,
					left: left
			});
			box.fadeIn('fast');
		}, function(e){
			box.hide();
		});
	}());


	$('.removeDiagnosis').live('click',function(e) {
		e.preventDefault();

		$('#diagnosis_id').val($(this).attr('rel'));

		$('#confirm_remove_diagnosis_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});
	});

	$('button.btn_remove_diagnosis').click(function(e) {
		e.preventDefault();

		$("#confirm_remove_diagnosis_dialog").dialog("close");

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removediagnosis?patient_id='+OE_patient_id+'&diagnosis_id='+$('#diagnosis_id').val(),
			'success': function(html) {
				if (html == 'success') {
					$('a.removeDiagnosis[rel="'+$('#diagnosis_id').val()+'"]').parent().parent().remove();
				} else {
					new OpenEyes.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the diagnosis.\n\nPlease contact support for assistance."
					}).open();
				}
			},
			'error': function() {
				new OpenEyes.Dialog.Alert({
					content: "Sorry, an internal error occurred and we were unable to remove the diagnosis.\n\nPlease contact support for assistance."
				}).open();
			}
		});
	});

	$('button.btn_cancel_remove_diagnosis').click(function(e) {
		e.preventDefault();
		$("#confirm_remove_diagnosis_dialog").dialog("close");
	});

	$('tr.all-episode').unbind('click').click(function(e) {
		e.preventDefault();
		window.location.href = baseUrl+'/patient/episode/'+$(this).attr('id');
	});

	$('a.removeContact').die('click').live('click',function(e) {
		e.preventDefault();

		var row = $(this).parent().parent();
		var pca_id = row.attr('data-attr-pca-id');

		// If we're currently editing this contact, hide the edit form
		var edit_contact = $("#edit_contact:visible");
		if (edit_contact.find("[name='pca_id']").val() == pca_id) {
		    edit_contact.slideToggle('fast');
		}

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/unassociateContact?pca_id='+pca_id,
			'success': function(resp) {
				if (resp == "1") {
					if (row.attr('data-attr-location-id')) {
						currentContacts['locations'].splice(currentContacts['locations'].indexOf(row.attr('data-attr-location-id')),1);
					} else {
						currentContacts['contacts'].splice(currentContacts['contacts'].indexOf(row.attr('data-attr-contact-id')),1);
					}
					row.remove();
				} else {
					new OpenEyes.Dialog.Alert({
						content: "There was an error removing the contact association, please try again or contact support for assistance."
					}).open();
				}
			}
		});
	});

	$('#contactfilter').change(function() {
		$('#contactname').focus();
	});
});

var contactCache = {};
