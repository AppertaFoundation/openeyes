/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$(document).ready(function () {
	$('#no_family_history').bind('change', function() {
		if ($(this)[0].checked) {
			$('.family_history_field').hide().find('select').attr('disabled', 'disabled');
		}
		else {
			$('.family_history_field').show().find('select').removeAttr('disabled');
		}
	});

	$('#btn-add_family_history').click(function() {
		$('#relative_id').val('');
		$('div.familyHistory #side_id').val('');
		$('#condition_id').val('');
		$('#add_family_history #comments').val('');
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',true);
		$('#btn-add_family_history').addClass('disabled');
	});
	$('button.btn_cancel_family_history').click(function() {
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',false);
		$('#btn-add_family_history').removeClass('disabled');
		return false;
	});

	$(document).on('change', '#relative_id', function() {
		if ($(this).find(':selected').data('other') == '1') {
			// show the rel other
			$('#family-history-o-rel-wrapper').show();
		}
		else {
			// hide the rel other
			$('#family-history-o-rel-wrapper').hide();
			// empty any text entered
			$('#family-history-o-rel-wrapper').find('input').each(function() {$(this).val('');});
		}
	});

	$(document).on('change', '#condition_id', function() {
		if ($(this).find(':selected').data('other') == '1') {
			// show the condition other
			$('#family-history-o-con-wrapper').show();
		}
		else {
			// hide the condition other
			$('#family-history-o-con-wrapper').hide();
			// empty any text entered
			$('#family-history-o-con-wrapper').find('input').each(function() {$(this).val('');});
		}
	});

	$('button.btn_save_family_history').click(function() {
		if (!$('#no_family_history').is(':checked')) {
			if ($('#relative_id').val() == '') {
				new OpenEyes.UI.Dialog.Alert({
					content: "Please select a relative."
				}).open();
				return false;
			}
			if ($('#side_id').val() == '') {
				new OpenEyes.UI.Dialog.Alert({
					content: "Please select a side."
				}).open();
				return false;
			}
			if ($('#condition_id').val() == '') {
				new OpenEyes.UI.Dialog.Alert({
					content: "Please select a condition."
				}).open();
				return false;
			}
		}
		$('img.add_family_history_loader').show();
		return true;
	});
	$('a.editFamilyHistory').click(function(e) {

		var tr = $(this).closest('tr');
		var history_id = $(this).attr('rel');

		$('#edit_family_history_id').val(history_id);
		var relative = tr.find('.relative').data('relativeid');
		$('#relative_id').val(relative);
		$('#relative_id').trigger('change');

		var side = tr.find('.side').text();
		$('#side_id').children('option').map(function() {
			if ($(this).text() == side) {
				$(this).attr('selected','selected');
			}
		});
		var condition = tr.find('.condition').data('conditionid');
		$('#condition_id').val(condition);
		$('#condition_id').trigger('change');

		$('#add_family_history #comments').val(tr.find('.comments').text());
		$('#add_family_history').slideToggle('fast');
		$('#btn-add_family_history').attr('disabled',true);
		$('#btn-add_family_history').addClass('disabled');

		e.preventDefault();
	});

	$('.removeFamilyHistory').live('click',function() {
		$('#family_history_id').val($(this).attr('rel'));

		$('#confirm_remove_family_history_dialog').dialog({
			resizable: false,
			modal: true,
			width: 560
		});

		return false;
	});

	$('button.btn_remove_family_history').click(function() {
		$("#confirm_remove_family_history_dialog").dialog("close");

		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/patient/removeFamilyHistory?patient_id=' + OE_patient_id + '&family_history_id='+$('#family_history_id').val(),
			'success': function(html) {
				if (html == 'success') {
					$('a.removeFamilyHistory[rel="'+$('#family_history_id').val()+'"]').parent().parent().remove();
					if($('.removeFamilyHistory').length == 0) {
						$('#currentFamilyHistory').hide();
						$('.family-history-status-unknown').show();
						$('.family-history-confirm-no').show();
					}
				} else {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, an internal error occurred and we were unable to remove the family_history.\n\nPlease contact support for assistance."
					}).open();
				}
			},
			'error': function() {
				new OpenEyes.UI.Dialog.Alert({
					content: "Sorry, an internal error occurred and we were unable to remove the family_history.\n\nPlease contact support for assistance."
				}).open();
			}
		});

		return false;
	});

	$('button.btn_cancel_remove_family_history').click(function() {
		$("#confirm_remove_family_history_dialog").dialog("close");
		return false;
	});
});
