$(document).ready(function() {
	$('#type').change(function() {
		setTypeFilter($(this).val());
		updateMacroList(0);
	});

	$('#site_id').change(function() {
		updateMacroList(0);
	});

	$('#subspecialty_id').change(function() {
		updateMacroList(0);
	});

	$('#firm_id').change(function() {
		updateMacroList(0);
	});

	$('#name').change(function() {
		updateMacroList(1);
	});

	$('#episode_status_id').change(function() {
		updateMacroList(1);
	});

	$('.addLetterMacro').click(function(e) {
		e.preventDefault();

		window.location.href = baseUrl + '/OphCoCorrespondence/admin/addMacro';
	});

	handleButton($('.cancelEditMacro'),function(e) {
		e.preventDefault();

		window.location.href = baseUrl = '/OphCoCorrespondence/admin/letterMacros';
	});

	$('#LetterMacro_type').change(function() {
        setTypeFilter($(this).val());
	});

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

	$('#shortcode').change(function() {
		if ($(this).val() !== '') {
			var current = $('#LetterMacro_body').val();

			$('#LetterMacro_body').val(current.substring(0,macro_cursor_position) + '[' + $(this).val() + ']' + current.substring(macro_cursor_position,current.length));
			$(this).val('');
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

	if ($('#LetterMacro_body').length >0) {
		macro_cursor_position = $('#LetterMacro_body').val().length;
	}

    $('#internal_referral_settings tr.clickable').click(function (e) {
        e.preventDefault();
        window.location.href = baseUrl + '/OphCoCorrespondence/oeadmin/internalReferralSettings/editSetting?key=' + $(this).data('key');
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

});

var macro_cursor_position = 0;

/**
 * Shows/Hides the correct dropdowns in the letter macro form
 *
 * @param type
 */
function setTypeFilter(type)
{
    $('#div_LetterMacro_site_id, #div_LetterMacro_subspecialty_id, #div_LetterMacro_firm_id').hide();
    $('#div_LetterMacro_'+type+'_id').show();
}

function updateMacroList(preserve)
{
	$('#admin_letter_macros tbody').html('<tr><td colspan="10">Searching...</td></tr>');

	var name = $('#name').val();
	var episode_status_id = $('#episode_status_id').val();

	$.ajax({
		'type': 'GET',
		'url': baseUrl + '/OphCoCorrespondence/admin/filterMacros?type=' + $('#type').val() + '&site_id=' + $('#site_id').val() + '&subspecialty_id=' + $('#subspecialty_id').val() + '&firm_id=' + $('#firm_id').val() + '&name=' + name + '&episode_status_id=' + episode_status_id,
		'success': function(html) {
			$('#admin_letter_macros tbody').html(html);
		}
	});

	$.ajax({
		'type': 'GET',
		'url': baseUrl + '/OphCoCorrespondence/admin/filterMacroNames?type=' + $('#type').val() + '&site_id=' + $('#site_id').val() + '&subspecialty_id=' + $('#subspecialty_id').val() + '&firm_id=' + $('#firm_id').val(),
		'success': function(html) {
			$('#name').html(html);

			if (preserve) {
				$('#name').val(name);
			}
		}
	});

	$.ajax({
		'type': 'GET',
		'url': baseUrl + '/OphCoCorrespondence/admin/filterEpisodeStatuses?type=' + $('#type').val() + '&site_id=' + $('#site_id').val() + '&subspecialty_id=' + $('#subspecialty_id').val() + '&firm_id=' + $('#firm_id').val(),
		'success': function(html) {
			$('#episode_status_id').html(html);

			if (preserve) {
				$('#episode_status_id').val(episode_status_id);
			}
		}
	});
}
