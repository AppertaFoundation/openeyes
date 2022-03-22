$(document).ready(function() {
	$('#type').change(function() {
		setTypeFilter($(this).val());
		updateMacroList(0);
	});

	$('#site_id, #institution_id, #subspecialty_id, #firm_id').change(function() {
		updateMacroList(0);
	});

	$('#name, #episode_status_id').change(function() {
		updateMacroList(1);
	});

	$('.addLetterMacro').click(function(e) {
		e.preventDefault();

		window.location.href = baseUrl + '/OphCoCorrespondence/admin/addMacro';
	});

	$('.addEmailAddress').click(function(e) {
		e.preventDefault();

		window.location.href = baseUrl + '/OphCoCorrespondence/admin/addEmailAddress';
	});

	$('.addEmailTemplate').click(function(e) {
		e.preventDefault();

		window.location.href = baseUrl + '/OphCoCorrespondence/admin/addEmailTemplate';
	});

	$(this).on('click','.cancelEditMacro',function(e) {
		e.preventDefault();

		window.location.href = baseUrl = '/OphCoCorrespondence/admin/letterMacros';
	});

	$('#LetterMacro_type').change(function() {
        setTypeFilter($(this).val());
	});

    $('#LetterMacro_type').trigger('change');

	$('#LetterMacro_body').select(function() {
		if ($(this)[0].selectionStart != undefined) {
			var text = $(this).val().substring($(this)[0].selectionStart-1,$(this)[0].selectionEnd+1);

			var m = text.match(/^\[([a-z]{3})\]$/);

			if (m) {
				$.ajax({
					'type': 'GET',
					'url': baseUrl + '/patient/shortCodeDescription?shortcode=' + m[1],
					'success': function(description) {
						$('.shortCodeDescription').html(description);
					}
				});
			} else {
				$('.shortCodeDescription').html('&nbsp;');
			}
		} else {
			$('.shortCodeDescription').html('&nbsp;');
		}
	});

	$('#LetterMacro_body').unbind('blur').bind('blur',function() {
		macro_cursor_position = $(this).prop('selectionEnd');
	});

	//Add a shortcode to the current editor at the cursor position
	$('#shortcode').change(function() {
		if ($(this).val() !== '') {
			if(tinyMCE.activeEditor) {
				tinyMCE.activeEditor.execCommand('mceInsertContent', false, '[' + $(this).val() + ']');
				$(this).val('');
			}
		}
	});

	$('#selectall').click(function() {
		if ($(this).is(':checked')) {
			$(this).closest('thead').next('tbody').find('input[type="checkbox"]').attr('checked','checked');
		} else {
			$(this).closest('thead').next('tbody').find('input[type="checkbox"]').attr('checked',false);
		}
	});

	$('.deleteMacros').click(function() {
		if ($('#admin_letter_macros tbody').find('input[type="checkbox"]:checked').length == 0) {
			alert("Please select one or more macros to delete.");
		} else {
			var list = {"id": []};

			$('#admin_letter_macros tbody').find('input[type="checkbox"]:checked').map(function() {
				list["id"].push($(this).val());
			});

			$.ajax({
				'type': 'POST',
				'url': baseUrl + '/OphCoCorrespondence/admin/deleteLetterMacros',
				'data': $.param(list) + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp != "1") {
						alert("Something went wrong trying to delete the macro(s). Please try again or contact support for assistance.");
					} else {
						window.location.reload();
					}
				}
			});
		}
	});

	$('.deleteEmailTemplates').click(function() {
		if ($('#admin_email_templates tbody').find('input[type="checkbox"]:checked').length == 0) {
			alert("Please select one or more email templates to delete.");
		} else {
			var list = {
				id: []
			};

			$('#admin_email_templates tbody').find('input[type="checkbox"]:checked').map(function() {
				list.id.push($(this).val());
			});

			$.ajax({
				'type': 'POST',
				'url': baseUrl + '/OphCoCorrespondence/admin/deleteEmailTemplates',
				'data': $.param(list) + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp != "1") {
						alert("Something went wrong trying to delete the email template(s). Please try again or contact support for assistance.");
					} else {
						window.location.reload();
					}
				}
			});
		}
	});

	$('.deleteEmailAddresses').click(function() {
		if ($('#sender_email_addresses tbody').find('input[type="checkbox"]:checked').length == 0) {
			alert("Please select one or more email addresses to delete.");
		} else {
			var list = {
				id: []
			};

			$('#sender_email_addresses tbody').find('input[type="checkbox"]:checked').map(function() {
				list.id.push($(this).val());
			});

			$.ajax({
				'type': 'POST',
				'url': baseUrl + '/OphCoCorrespondence/admin/deleteEmailAddresses',
				'data': $.param(list) + '&YII_CSRF_TOKEN=' + YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp != "1") {
						alert("Something went wrong trying to delete the email address(es). Please try again or contact support for assistance.");
					} else {
						window.location.reload();
					}
				}
			});
		}
	});

	if ($('#LetterMacro_body').length >0) {
		macro_cursor_position = $('#LetterMacro_body').val().length;
	}

    $('#internal_referral_settings tr.clickable').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/OphCoCorrespondence/oeadmin/internalReferralSettings/editSetting?key=' + $(this).data('key');
    });

    $('#letter_settings tr.clickable').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/OphCoCorrespondence/admin/editSetting?key=' + $(this).data('key');
    });

	/** Internal Referral Settings **/
	function saveSiteList(){
		data = $('#to_location_sites_grid input').serializeArray();

		data.push({name: 'YII_CSRF_TOKEN', value: YII_CSRF_TOKEN});

        $.ajax({
            'type': 'POST',
            'url': '/OphCoCorrespondence/oeadmin/internalReferralSettings/updateToLocationList',
            'data': data,
			'beforeSend':function (){
            	$('#internal_referral_to_location .loader').show();
                $('#internal_referral_to_location span.error').fadeOut(500);
			},
            'success': function (data) {
			    data = JSON.parse(data);

                if(data.success === true){
                    $('#internal_referral_to_location span.saved').show();
                    $('#internal_referral_to_location span.saved').fadeOut(3000);
                } else {
                    $('#internal_referral_to_location span.error').show();
                    if(data.message){
                        $('#internal_referral_to_location span.error').text(data.message);
                    }
                }
            },
			'error': function(){
                $('#internal_referral_to_location span.error').show();
			},
            'complete': function(){
                $('#internal_referral_to_location .loader').hide();
			}
        });

	}

    $('#save_to_location_table').on('click', function(){
        saveSiteList();
    });

	/** End of Internal Referral Settings **/


    /** EditMacro Page **/

    $('#LetterMacro_letter_type_id').on('change', function(){
        var radios = $('#LetterMacro_recipient_id').find('input[type=radio]'),
        	letter_type = $(this).find('option:selected').text();

        $.each(radios, function(index, option){
        	var $label = $(option).closest('label'),
				$span = $label.find('span'),
                txt = $span.text().trim();

			if(letter_type === 'Internal Referral'){

				if(txt === 'None'){
                    $span.text("Internal Referral");
                    $(option).prop('checked', true);
                    $label.show();
				} else {
                    $(option).prop('disabled', true);
                    $(option).prop('checked', false);
                    $label.hide();
				}

			} else {
                if(txt === 'Internal Referral'){
                    $span.text("None");
				}
                $(option).prop('disabled', false);
                $label.show();
			}
		});
    });

	var pathname = window.location.pathname;
    if(pathname.indexOf('/snippet/') > -1){

		$('#search_institution_relations\\.institution_id option')
		.filter(function() {
			return !this.value || $.trim(this.value).length == 0 || $.trim(this.text).length == 0;
		})
		.remove();

		$("#et_add").prop('value', 'Add to ' + $('#search_institution_relations\\.institution_id option:selected').text());
		
		$('#search_institution_relations\\.institution_id').on('change', function () {
			getInstitutionSites($(this).val(), $('#search_sites\\.id'));
		});

		if($('#LetterString_institutions').val() === "") {
			$("#LetterString_sites").prop("disabled", true);
		} else {
			$("#LetterString_sites").prop("disabled", false);
		}

		$('#LetterString_institutions').on('change', function () {
			getInstitutionSites($(this).val(), $('#LetterString_sites'));

			$(".multi-select-remove").each(function(i) {
				var btn = $(this);
				setTimeout(btn.trigger.bind(btn, "click"), i * 1);
			});

			if($('#LetterString_institutions').val() === "") {
				$("#LetterString_sites").prop("disabled", true);
			} else {
				$("#LetterString_sites").prop("disabled", false);
			}
		});

		$("#et_save").click(function(event){
			$('#LetterString_institutions').prop("disabled", false); // Element(s) are now enabled.
		});

	}



    /** End of EditMacro Page **/

});

var macro_cursor_position = 0;

/**
 * Shows/Hides the correct dropdowns in the letter macro form
 *
 * @param type
 */
function setTypeFilter(type)
{
    $('#LetterMacro_institutions, #LetterMacro_sites, #LetterMacro_subspecialties, #LetterMacro_firms').hide();
    $('#LetterMacro_'+type).show();
	$('#LetterMacro_type_dropdown').text(type.charAt(0).toUpperCase() + type.slice(1));
}

function updateMacroList(preserve)
{
	$('#admin_letter_macros tbody').html('<tr><td colspan="10">Searching...</td></tr>');

	var name = $('#name').val();
	var episode_status_id = $('#episode_status_id').val();

	$.ajax({
		'type': 'GET',
		'url': baseUrl + '/OphCoCorrespondence/admin/filterMacros',
		data: {
			type: $('#type').val(),
			institution_id: document.getElementById('institution_id').value,
			site_id: $('#site_id').val(),
			subspecialty_id: $('#subspecialty_id').val(),
			firm_id: $('#firm_id').val(),
			name: name,
			episode_status_id: episode_status_id
		},
		'success': function(html) {
			$('#admin_letter_macros tbody').html(html);
		}
	});

	$.ajax({
		'type': 'GET',
		'url': baseUrl + '/OphCoCorrespondence/admin/filterMacroNames',
		data: {
			type: $('#type').val(),
			institution_id: document.getElementById('institution_id').value,
			site_id: $('#site_id').val(),
			subspecialty_id: $('#subspecialty_id').val(),
			firm_id: $('#firm_id').val()
		},
		'success': function(html) {
			$('#name').html(html);

			if (preserve) {
				$('#name').val(name);
			}
		}
	});

	$.ajax({
		'type': 'GET',
		'url': baseUrl + '/OphCoCorrespondence/admin/filterEpisodeStatuses',
		data: {
			type: $('#type').val(),
			institution_id: document.getElementById('institution_id').value,
			site_id: $('#site_id').val(),
			subspecialty_id: $('#subspecialty_id').val(),
			firm_id: $('#firm_id').val()
		},
		'success': function(html) {
			$('#episode_status_id').html(html);

			if (preserve) {
				$('#episode_status_id').val(episode_status_id);
			}
		}
	});
}
