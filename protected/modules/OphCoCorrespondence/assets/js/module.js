/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

var correspondence_markprinted_url, correspondence_print_url;
$(document).ready(function() {
	$(this).delegate('#ElementLetter_site_id', 'change', function() {
		if (correspondence_directlines) {
			$('#ElementLetter_direct_line').val(correspondence_directlines[$('#ElementLetter_site_id').val()]);
		}
	});

	handleButton($('#et_save_draft'),function() {
		$('#ElementLetter_draft').val(1);
	});

	handleButton($('#et_save_print'),function() {
		$('#ElementLetter_draft').val(0);
	});

	handleButton($('#et_cancel'),function() {
		$('#dialog-confirm-cancel').dialog({
			resizable: false,
			//height: 140,
			modal: true,
			buttons: {
				"Yes, cancel": function() {
					$(this).dialog('close');

					disableButtons();

					if (m = window.location.href.match(/\/update\/[0-9]+/)) {
						window.location.href = window.location.href.replace('/update/','/view/');
					} else {
						window.location.href = baseUrl+'/patient/episodes/'+OE_patient_id;
					}
				},
				"No, go back": function() {
					$(this).dialog('close');
					return false;
				}
			}
		});
	});

	handleButton($('#et_deleteevent'));
	handleButton($('#et_canceldelete'));

	$('#address_target').change(function() {
		var nickname = $('input[id="ElementLetter_use_nickname"][type="checkbox"]').is(':checked') ? '1' : '0';

		if ($(this).children('option:selected').val() != '') {
			if ($(this).children('option:selected').text().match(/NO ADDRESS/)) {

				new OpenEyes.UI.Dialog.Alert({
					content: "Sorry, this contact has no address so you can't send a letter to them."
				}).open();

				$(this).val(selected_recipient);
				return false;
			}

			var val = $(this).children('option:selected').val();

			if (re_field == null) {
				if ($('#re_default').length >0) {
					re_field = $('#re_default').val();
				} else {
					re_field = $('#ElementLetter_re').val();
				}
			}

			var target = $(this);

			$.ajax({
				'type': 'GET',
				'dataType': 'json',
				'url': baseUrl+'/OphCoCorrespondence/Default/getAddress?patient_id='+OE_patient_id+'&contact='+val+'&nickname='+nickname,
				'success': function(data) {
					if (data['error'] == 'DECEASED') {

						new OpenEyes.UI.Dialog.Alert({
							content: "This patient is deceased and cannot be written to."
						}).open();

						target.val(selected_recipient);
						return false;
					}

					if (val.match(/^Patient/)) {
						$('#ElementLetter_re').val('');
						$('#ElementLetter_re').parent().parent().hide();
					} else {
						if (re_field != null) {
							$('#ElementLetter_re').val(re_field);
							$('#ElementLetter_re').parent().parent().show();
						}
					}

					correspondence_load_data(data);
					selected_recipient = val;

					// try to remove the selected recipient's address from the cc field
					if ($('#ElementLetter_cc').val().length >0) {
						$.ajax({
							'type': 'GET',
							'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact='+val,
							'success': function(text) {
								if (text.match(/DECEASED/)) {
									new OpenEyes.UI.Dialog.Alert({
										content: "This patient is deceased and cannot be cc'd."
									}).open();
									target.val(selected_recipient);
									return false;
								} else if (!text.match(/NO ADDRESS/)) {
									if ($('#ElementLetter_cc').val().length >0) {
										var cur = $('#ElementLetter_cc').val();

										if (cur.indexOf(text) != -1) {
											var strings = cur.split("\n");
											var replace = '';

											for (var i in strings) {
												if (strings[i].length >0 && strings[i].indexOf(text) == -1) {
													if (replace.length >0) {
														replace += "\n";
													}
													replace += $.trim(strings[i]);
												}
											}

											$('#ElementLetter_cc').val(replace);
										}
									}

									var targets = '';

									$('#cc_targets').children().map(function() {
										if ($(this).val() != val) {
											targets += '<input type="hidden" name="CC_Targets[]" value="'+$(this).val()+'" />';
										}
									});
									$('#cc_targets').html(targets);
								}
							}
						});
					}

					// if the letter is to anyone but the GP we need to cc the GP
					if (!val.match(/^Gp|^Practice/)) {
						var contact;
						if (OE_gp_id) {
							contact = 'Gp' + OE_gp_id;
						}
						else if (OE_practice_id) {
							contact = 'Practice' + OE_practice_id;
						}
						if (contact) {
							$.ajax({
								'type': 'GET',
								'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact='+contact,
								'success': function(text) {
									if (!text.match(/NO ADDRESS/)) {
										if ($('#ElementLetter_cc').val().length >0) {
											var cur = $('#ElementLetter_cc').val();

											if (cur.indexOf(text) == -1) {
												if (!$('#ElementLetter_cc').val().match(/[\n\r]$/)) {
													cur += "\n";
												}

												$('#ElementLetter_cc').val(cur+text);
												$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="gp" />');
											}

										} else {
											$('#ElementLetter_cc').val(text);
											$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="gp" />');
										}
									} else {
										new OpenEyes.UI.Dialog.Alert({
											content: "Warning: letters should be cc'd to the patient's GP, but the current patient's GP has no valid address."
										}).open();
									}
								}
							});
						}
					} else {
						// if the letter is to the GP we need to cc the patient
						$.ajax({
							'type': 'GET',
							'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact=Patient'+OE_patient_id,
							'success': function(text) {
								if (text.match(/DECEASED/)) {
									new OpenEyes.UI.Dialog.Alert({
										content: "The patient is deceased so cannot be cc'd."
									}).open();
									target.val(selected_recipient);
									return false;
								} else if (!text.match(/NO ADDRESS/)) {
									if ($('#ElementLetter_cc').val().length >0) {
										var cur = $('#ElementLetter_cc').val();

										if (cur.indexOf(text) == -1) {
											if (!$('#ElementLetter_cc').val().match(/[\n\r]$/)) {
												cur += "\n";
											}

											$('#ElementLetter_cc').val(cur+text);
											$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="patient" />');
										}

									} else {
										$('#ElementLetter_cc').val(text);
										$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="patient" />');
									}
								} else {
									new OpenEyes.UI.Dialog.Alert({
										content: "Warning: letters to the GP should be cc'd to the patient's, but the patient has no valid address."
									}).open();
								}
							}
						});
					}
				}
			});
		}
	});

	$('#macro').change(function() {
		var nickname = $('input[id="ElementLetter_use_nickname"][type="checkbox"]').is(':checked') ? '1' : '0';
		var obj = $(this);

		if ($(this).val() != '') {
			var macro_id = $(this).val();

			$.ajax({
				'type': 'GET',
				'dataType': 'json',
				'url': baseUrl+'/OphCoCorrespondence/Default/getMacroData?patient_id=' + OE_patient_id + '&macro_id=' + macro_id + '&nickname=' + nickname,
				'success': function(data) {
					if (data['error'] == 'DECEASED') {
						new OpenEyes.UI.Dialog.Alert({
							content: "The patient is deceased so this macro cannot be used."
						}).open();
						obj.val('');
						return false;
					}
					$('#ElementLetter_cc').val('');
					$('#cc_targets').html('');
					correspondence_load_data(data);
					et_oph_correspondence_body_cursor_position = $('#ElementLetter_body').val().length;
					obj.val('');
				}
			});
		}
	});

	$('input[id="ElementLetter_use_nickname"][type="checkbox"]').click(function() {
		$('#address_target').change();
	});

	$('select.stringgroup').change(function() {
		var obj = $(this);
		var selected_val = $(this).children('option:selected').val();

		if (selected_val != '') {
			var m = selected_val.match(/^([a-z]+)([0-9]+)$/);

			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/Default/getString?patient_id='+OE_patient_id+'&string_type='+m[1]+'&string_id='+m[2],
				'success': function(text) {
					if (ophcocorrespondence_previous_dropdown(obj.attr('id'))) {
						text = "\n\n"+ucfirst(text);
					}

					correspondence_append_body(text);
					obj.val('');

					et_oph_correspondence_last_stringgroup = obj.attr('id');
				}
			});
		}
	});

	$('#cc').change(function() {
		var contact_id = $(this).children('option:selected').val();
		var obj = $(this);

		if (contact_id != '') {
			var ok = true;

			$('#cc_targets').children('input').map(function() {
				if ($(this).val() == contact_id) {
					ok = false;
				}
			});

			if (!ok) {
				if (obj.val().match(/^Patient/)) {
					var found = false;
					$.each($('#ElementLetter_cc').val().split("\n"),function(key, value) {
						if (value.match(/^Patient: /)) {
							found = true;
						}
					});
					if (found) {
						obj.val('');
						return true;
					}
				} else if (obj.val().match(/^Gp/)) {
					var found = false;
					$.each($('#ElementLetter_cc').val().split("\n"),function(key, value) {
						if (value.match(/^GP: /)) {
							found = true;
						}
					});
					if (found) {
						obj.val('');
						return true;
					}
				} else {
					obj.val('');
					return true;
				}
			}

			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/Default/getCc?patient_id='+OE_patient_id+'&contact='+contact_id,
				'success': function(text) {
					if (text.match(/DECEASED/)) {
						new OpenEyes.UI.Dialog.Alert({
							content: "The patient is deceased so cannot be cc'd."
						}).open();
						obj.val('');
						return false;
					} else if (!text.match(/NO ADDRESS/)) {
						if ($('#ElementLetter_cc').val().length >0) {
							var cur = $('#ElementLetter_cc').val();

							if (!$('#ElementLetter_cc').val().match(/[\n\r]$/)) {
								cur += "\n";
							}

							$('#ElementLetter_cc').val(cur+text);
						} else {
							$('#ElementLetter_cc').val(text);
						}

						$('#cc_targets').append('<input type="hidden" name="CC_Targets[]" value="'+contact_id+'" />');
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Sorry, this contact has no address and so cannot be cc'd."
						}).open();
					}

					obj.val('');
				}
			});
		}
	});

	$('#ElementLetter_body').unbind('keyup').bind('keyup',function() {
		et_oph_correspondence_body_cursor_position = $(this).prop('selectionEnd');
		et_oph_correspondence_last_stringgroup_do = false;

		// turn on the last_stringgroup feature if the user has removed all of the text in the body field
		if ($(this).val().length == 0) {
			et_oph_correspondence_last_stringgroup_do = true;
		}

		if (m = $(this).val().match(/\[([a-z]{3})\]/i)) {

			var text = $(this).val();

			$.ajax({
				'type': 'POST',
				'url': baseUrl+'/OphCoCorrespondence/Default/expandStrings',
				'data': 'patient_id='+OE_patient_id+'&text='+text+"&YII_CSRF_TOKEN="+YII_CSRF_TOKEN,
				'success': function(resp) {
					if (resp) {
						$('#ElementLetter_body').val(resp);
					}
				}
			});
		}
	});

	$('#ElementLetter_body').unbind('click').click(function() {
		et_oph_correspondence_body_cursor_position = $(this).prop('selectionEnd');
	});

	if ($('#OphCoCorrespondence_printLetter').val() == 1) {
		if ($('#OphCoCorrespondence_printLetter_all').val() == 1) {
			setTimeout("OphCoCorrespondence_do_print(true);",1000);
		} else {
			setTimeout("OphCoCorrespondence_do_print(false);",1000);
		}
	}

	handleButton($('#et_print'),function(e) {
		if ($('#correspondence_out').hasClass('draft')) {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/default/doPrint/'+OE_event_id,
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to print the letter, please try again or contact support for assistance."
						}).open();
					}
				}
			});
		} else {
			OphCoCorrespondence_do_print(false);
			e.preventDefault();
		}
	});

	handleButton($('#et_print_all'),function(e) {
		if ($('#correspondence_out').hasClass('draft')) {
			$.ajax({
				'type': 'GET',
				'url': baseUrl+'/OphCoCorrespondence/default/doPrint/'+OE_event_id+'?all=1',
				'success': function(html) {
					if (html == "1") {
						window.location.reload();
					} else {
						new OpenEyes.UI.Dialog.Alert({
							content: "Something went wrong trying to print the letter, please try again or contact support for assistance."
						}).open();
					}
				}
			});
		} else {
			OphCoCorrespondence_do_print(true);
			e.preventDefault();
		}
	});

	handleButton($('#et_confirm_printed'),function() {
		$.ajax({
			'type': 'GET',
			'url': baseUrl+'/OphCoCorrespondence/Default/confirmPrinted/'+OE_event_id,
			'success': function(html) {
				if (html != "1") {
					new OpenEyes.UI.Dialog.Alert({
						content: "Sorry, something went wrong. Please try again or contact support for assistance."
					}).open();
					enableButtons();
				} else {
					location.reload(true);
				}
			}
		});
	});

	$('button.addEnclosure').die('click').live('click',function() {
		var id = -1;
		$('#enclosureItems').find('.enclosureItem input').each(function() {
			var m = $(this).attr('name').match(/[0-9]+/);
			if (parseInt(m[0]) > id) {
				id = parseInt(m[0]);
			}
		});

		id += 1;

		var html = [
			'<div class="field-row row collapse enclosureItem">',
			'		<div class="large-8 column">',
			'			<input type="text" value="" autocomplete="' + window.OE_html_complete + '" name="EnclosureItems[enclosure'+id+']">',
			'		</div>',
			'		<div class="large-4 column end">',
			'			<div class="postfix align"><a href="#" class="field-info removeEnclosure">Remove</a></div>',
			'		</div>',
			'	</div>'
		].join('');

		$('#enclosureItems').append(html).show();
		$('input[name="EnclosureItems[enclosure'+id+']"]').select().focus();
	});

	$('a.removeEnclosure').die('click').live('click',function(e) {
		$(this).closest('.enclosureItem').remove();
		if (!$('#enclosureItems').children().length) {
			$('#enclosureItems').hide();
		}
		e.preventDefault();
	});

	$('div.enclosureItem input').die('keypress').live('keypress',function(e) {
		if (e.keyCode == 13) {
			$('button.addEnclosure').click();
			return false;
		}
		return true;
	});

	var selected_recipient = $('#address_target').val();

	$('#ElementLetter_body').tabby();
});

var et_oph_correspondence_body_cursor_position = 0;
var re_field = null;
var et_oph_correspondence_last_stringgroup_do = true;
var et_oph_correspondence_last_stringgroup = null;

function correspondence_load_data(data) {
	for (var i in data) {
		if (m = i.match(/^text_(.*)$/)) {
			$('#'+m[1]).val(data[i]);
		} else if (m = i.match(/^sel_(.*)$/)) {
			if (m[1] == 'address_target') {
				if (data[i].match(/^Patient/)) {
					$('#ElementLetter_re').val('');
					$('#ElementLetter_re').parent().parent().hide();
				} else {
					if (re_field != null) {
						$('#ElementLetter_re').val(re_field);
						$('#ElementLetter_re').parent().parent().show();
					}
				}
			}
			$('#'+m[1]).val(data[i]);
		} else if (m = i.match(/^check_(.*)$/)) {
			$('input[id="'+m[1]+'"][type="checkbox"]').attr('checked',(parseInt(data[i]) == 1 ? true : false));
		} else if (m = i.match(/^textappend_(.*)$/)) {
			$('#'+m[1]).val($('#'+m[1]).val()+data[i]);
		} else if (m = i.match(/^hidden_(.*)$/)) {
			$('#'+m[1]).val(data[i]);
		} else if (m = i.match(/^elementappend_(.*)$/)) {
			$('#'+m[1]).append(data[i]);
		} else if (i == 'alert') {
			new OpenEyes.UI.Dialog.Alert({
				content: data[i]
			}).open();
		}
	}
}

function correspondence_append_body(text) {
	var cpos = et_oph_correspondence_body_cursor_position;
	var insert_prefix = '';

	var current = $('#ElementLetter_body').val();

	text = ucfirst(text);

	if (!text.match(/\.$/) && !text.match(/\n$/)) {
		text += '. ';
	}

	if (current == '') {
		$('#ElementLetter_body').val(text);
	} else {
		// attempt to intelligently drop the text in based on what it follows
		var preceeding_blob = current.substring(0,cpos);

		if (preceeding_blob.match(/\.$/)) {
			insert_prefix = ' ';
		} else if (preceeding_blob.match(/[a-zA-Z]+$/)) {
			insert_prefix = '. ';
		}

		$('#ElementLetter_body').val(current.substring(0,cpos) + insert_prefix + text + current.substring(cpos,current.length));
	}

	et_oph_correspondence_body_cursor_position += insert_prefix.length;
	et_oph_correspondence_body_cursor_position += text.length;
}

function ucfirst(str) {
	str += '';
	var f = str.charAt(0).toUpperCase();
	return f + str.substr(1);
}

function uclower(str) {
	str += '';
	var f = str.charAt(0).toLowerCase();
	return f + str.substr(1);
}

function ophcocorrespondence_previous_dropdown(dropdown) {
	if (!et_oph_correspondence_last_stringgroup_do) {
		return false;
	}

	switch (dropdown) {
		case 'findings':
			return (et_oph_correspondence_last_stringgroup == "introduction");
		case 'diagnosis':
			return inArray(et_oph_correspondence_last_stringgroup, ["introduction","findings"]);
		case 'management':
			return inArray(et_oph_correspondence_last_stringgroup, ["introduction","findings","diagnosis"]);
		case 'drugs':
			return inArray(et_oph_correspondence_last_stringgroup, ["introduction","findings","diagnosis","management"]);
		case 'outcome':
			return inArray(et_oph_correspondence_last_stringgroup, ["introduction","findings","diagnosis","management","drugs"]);
	}

	return false;
}

function inArray(needle, haystack) {
	var length = haystack.length;
	for (var i = 0; i < length; i++) {
		if (haystack[i] == needle) return true;
	}
	return false;
}

function OphCoCorrespondence_do_print(all) {
	$.ajax({
		'type': 'GET',
		'url': correspondence_markprinted_url,
		'success': function(html) {
			if (all) {
				printEvent({"all":1});
			} else {
				printEvent(null);
			}
			enableButtons();
		}
	});
}
