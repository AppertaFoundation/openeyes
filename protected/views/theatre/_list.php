<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

//$baseUrl = Yii::app()->baseUrl;
//$cs = Yii::app()->getClientScript();
//$cs->registerCoreScript('jquery');
//$cs->registerCoreScript('jquery.ui');
//$cs->registerCoreScript('jquery.printElement.min');
//$cs->registerCSSFile('/css/jqueryui/theme/jquery-ui.css', 'all');

if (empty($theatres)) {?>
	<p class="fullBox"><strong>No theatre schedules match your search criteria.</strong></p>
<?php } else {
	$panels = array();

	$firstTheatreShown = false;
	?>
	<span style="margin-left: 10px; color: #f00; display: none;" id="updated-flash">
		Session updated!
	</span>
	<?php
	$tbody = 0;
	foreach ($theatres as $name => $dates) { ?>
		<h3 class="theatre<?php if (!$firstTheatreShown) {?> firstTheatre<?php }?>"><strong><?php echo $name?></strong></h3>
		<?php
		$firstTheatreShown = true;
		foreach ($dates as $date => $sessions) {
			$timestamp = strtotime($date);?>
<?php
			$previousSequenceId = '';
			$timeAvailable = $sessions[0]['sessionDuration'];
			foreach ($sessions as $session) {
				if ($previousSequenceId != $session['sequenceId']) {
					if ($previousSequenceId != '') {
?>
						</tbody>
						<tfoot>
							<tr>
								<?php $status = ($timeAvailable > 0); ?>
								<th colspan="9" class="footer <?php echo ($status) ? 'available' : 'full'; ?> clearfix">
									<div class="timeLeft">
										<?php if($status) { ?>
										<?php echo $timeAvailable ?> minutes unallocated
										<?php } else { ?>
										<?php echo abs($timeAvailable) ?> minutes overbooked
										<?php } ?>
										<span<?php if (!$session['status']) {?> style="display: none;"<?php }?> class="session_unavailable" id="session_unavailable_<?php echo $previousSessionId?>"> - session unavailable</span>
									</div>
									<div class="metadata">
										<div<?php if(!$session_metadata['consultant']) {?> style="display: none;"<?php }?> id="consultant_icon_<?php echo $previousSessionId?>" class="consultant" title="Consultant Present">Consultant</div>
										<div<?php if(!$session_metadata['anaesthetist']) {?> style="display: none;"<?php }?> id="anaesthetist_icon_<?php echo $previousSessionId?>" class="anaesthetist" title="Anaesthetist Present">Anaesthetist</div>
										<div<?php if(!$session_metadata['paediatric']) {?> style="display: none;"<?php }?> id="paediatric_icon_<?php echo $previousSessionId?>" class="paediatric" title="Paediatric Session">Paediatric</div>
									</div>
								</th>
							</tr>
						</tfoot>
					</table>
					<div style="text-align:right; margin-right:10px; display: none;" id="buttons_<?php echo $previousSessionId?>">
						<img id="loader2_<?php echo $previousSessionId?>" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 2px; display: none" />
						<button type="submit" class="classy green tall" id="btn_save_<?php echo $previousSessionId?>"><span class="button-span button-span-green">Save</span></button>
						<button type="submit" class="classy red tall" id="btn_cancel_<?php echo $previousSessionId?>"><span class="button-span button-span-red">Cancel</span></button>
					</div>
				</div>
<?php
					}
?>
<h3 class="sessionDetails"><span class="date"><strong><?php echo date('d M',$timestamp)?></strong> <?php echo date('Y',$timestamp)?></span> - <strong><span class="day"><?php echo date('l',$timestamp)?></span>, <span class="time"><?php echo substr($session['startTime'], 0, 5)?> - <?php echo substr($session['endTime'], 0, 5)?></span></strong> for <?php echo !empty($session['firm_name']) ? $session['firm_name'] : 'Emergency List' ?> <?php echo !empty($session['specialty_name']) ? 'for (' . $session['specialty_name'] . ')' : '' ?> - <strong><?php echo $name?></strong></h3>
				<div class="action_options" id="action_options_<?php echo $session['sessionId']?>" style="float: right;">
					<img id="loader_<?php echo $session['sessionId']?>" src="/img/ajax-loader.gif" alt="loading..." style="margin-right: 5px; margin-bottom: 4px; display: none;" />
					<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_<?php echo $session['sessionId']?>" href="#">Edit</a></span>
				</div>
				<div class="theatre-sessions whiteBox clearfix">
					<div style="float: right;">
						<?php if (Yii::app()->user->checkAccess('purplerinse')) {?>
							<div class="purple_rinse" id="purple_rinse_<?php echo $session['sessionId']?>" style="display:none; width:207px;">
								<input type="checkbox" id="consultant_<?php echo $session['sessionId']?>" name="consultant_<?php echo $session['sessionId']?>" value="1"<?php if ($session['consultant']){?> checked="checked"<?php }?> /> Consultant present<br/>
								<input type="checkbox" id="paediatric_<?php echo $session['sessionId']?>" name="paediatric_<?php echo $session['sessionId']?>" value="1"<?php if ($session['paediatric']){?> checked="checked"<?php }?> /> Paediatric<br/>
								<input type="checkbox" id="anaesthetic_<?php echo $session['sessionId']?>" name="anaesthetic_<?php echo $session['sessionId']?>" value="1"<?php if ($session['anaesthetist']){?> checked="checked"<?php }?> /> Anaesthetist present<br/>
								<input type="checkbox" id="general_anaesthetic_<?php echo $session['sessionId']?>" name="general_anaesthetic_<?php echo $session['sessionId']?>" value="1"<?php if ($session['general_anaesthetic']){?> checked="checked"<?php }?> /> General anaesthetic available<br/>
								<input type="checkbox" id="available_<?php echo $session['sessionId']?>" name="available_<?php echo $session['sessionId']?>" value="1"<?php if ($session['status'] == 0){?> checked="checked"<?php }?> /> Session available<br/>
							</div>
						<?php }?>
						<div class="sessionComments" style="display:block; width:205px;">
							<form>
								<h4>Session Comments</h4>
								<textarea style="display: none;" rows="2" name="comments<?php echo $session['sessionId'] ?>" id="comments<?php echo $session['sessionId'] ?>"><?php echo $session['comments'] ?></textarea>
								<div id="comments_ro_<?php echo $session['sessionId']?>" title="Modified on <?php echo Helper::convertMySQL2NHS($session['last_modified_date'])?> at <?php echo $session['last_modified_time']?> by <?php echo $session['session_first_name']?> <?php echo $session['session_last_name']?>"><?php echo strip_tags($session['comments'])?></div>
							</form>
						</div>
					</div>
					<table id="theatre_list">
						<thead id="thead_<?php echo $session['sessionId']?>">
							<tr>
								<th>Admit time</th>
								<th class="th_sort" style="display: none;">Sort</th>
								<th>Hospital #</th>
								<th>Confirmed</th>
								<th>Patient (Age)</th>
								<th>[Eye] Operation</th>
								<th>Priority</th>
								<th>Anesth</th>
								<th>Ward</th>
								<th>Info</th>
							</tr>
						</thead>
						<tbody id="tbody_<?php echo $session['sessionId']?>">
<?php
					$previousSequenceId = $session['sequenceId'];
					$previousSessionId = $session['sessionId'];
					$previousSession = $session;
					$timeAvailable = $session['sessionDuration'];
				}

				if (!empty($session['patientId'])) {
					$timeAvailable -= $session['operationDuration'];
?>
							<tr id="oprow_<?php echo $session['operationId'] ?>">
								<td class="session">
									<input style="display: none;" type="text" name="admitTime_<?php echo $session['operationId']?>" id="admitTime_<?php echo $session['sessionId']?>_<?php echo $session['operationId'] ?>" value="<?php echo substr($session['admissionTime'], 0, 5)?>" size="4">
									<span id="admitTime_ro_<?php echo $session['sessionId']?>_<?php echo $session['operationId']?>"><?php echo substr($session['admissionTime'], 0, 5)?></span>
								</td>
								<td class="td_sort" style="display: none;">
									<img src="/img/_elements/icons/draggable_row.png" alt="draggable_row" width="25" height="28" />
								</td>
								<td class="hospital"><?php echo CHtml::link(
									$session['patientHosNum'],
									'/patient/episodes/' . $session['patientId'] . '/event/' . $session['eventId']
								);
								?></td>
								<td class="confirm"><input id="confirm_<?php echo $session['operationId']?>" type="checkbox" value="1" name="confirm_<?php echo $session['operationId']?>" disabled="disabled" <?php if ($session['confirmed']) {?>checked="checked" <?php }?>/></td>
								<td class="patient leftAlign"><?php echo $session['patientName'] . ' (' . $session['patientAge'] . ')'; ?></td>
								<td class="operation leftAlign"><?php echo !empty($session['procedures']) ? '['.$session['eye'].'] '.$session['procedures'] : 'No procedures'?></td>
								<td class=""><?php echo $session['priority']?></td>
								<td class="anesthetic"><?php echo $session['anaesthetic'] ?></td>
								<td class="ward"><?php echo $session['ward']; ?></td>
								<td class="alerts">
								<?php
					if ($session['patientGender'] == 'M') {
?>
<img src="/img/_elements/icons/alerts/male.png" alt="male" title="male" width="17" height="17" />
<?php
					} else {
?>
<img src="/img/_elements/icons/alerts/female.png" alt="female" title="female" width="17" height="17" />
<?php
					}

					if (!empty($session['operationComments']) && preg_match('/\w/', $session['operationComments'])) {
							?><img src="/img/_elements/icons/alerts/comment.png" alt="<?php echo htmlentities($session['operationComments']) ?>" title="<?php echo htmlentities($session['operationComments']) ?>" width="17" height="17" />
<?php
					}

					if (!empty($session['overnightStay'])) {
							?><img src="/img/_elements/icons/alerts/overnight.png" alt="Overnight stay required" title="Overnight stay required" width="17" height="17" />
<?php
					}

					if (!empty($session['consultantRequired'])) {
							?><img src="/img/_elements/icons/alerts/consultant.png" alt="Consultant required" title="Consultant required" width="17" height="17" />
<?php
					}
					?><img src="/img/_elements/icons/alerts/booked_user.png" alt="Created by: <?php echo $session['created_user']."\n"?>Last modified by: <?php echo $session['last_modified_user']?>" title="Created by: <?php echo $session['created_user']."\n"?>Last modified by: <?php echo $session['last_modified_user']?>" width="17" height="17" /><?php
				}
?>
								</td>
							</tr>
			<?php
				// Session data is replicated in every "session" record so we need to capture the last one of each group for display in the footer. Now wash your hands...
				$session_metadata = array_intersect_key($session, array('consultant'=>0,'anaesthetist'=>0,'paediatric'=>0));
			}
			?>
						</tbody>
						<tfoot>
							<tr>
								<?php $status = ($timeAvailable > 0); ?>
								<th colspan="9" class="footer <?php echo ($status) ? 'available' : 'full'; ?> clearfix">
									<div class="timeLeft">
										<?php if($status) { ?>
										<?php echo $timeAvailable ?> minutes unallocated
										<?php } else { ?>
										<?php echo abs($timeAvailable) ?> minutes overbooked
										<?php } ?>
										<span<?php if (!$previousSession['status']) {?> style="display: none;"<?php }?> class="session_unavailable" id="session_unavailable_<?php echo $previousSessionId?>"> - session unavailable</span>
									</div>
									<div class="metadata">
										<div<?php if(!$session_metadata['consultant']) {?> style="display: none;"<?php }?> id="consultant_icon_<?php echo $session['sessionId']?>" class="consultant" title="Consultant Present">Consultant</div>
										<div<?php if(!$session_metadata['anaesthetist']) {?> style="display: none;"<?php }?> id="anaesthetist_icon_<?php echo $session['sessionId']?>" class="anaesthetist" title="Anaesthetist Present">Anaesthetist</div>
										<div<?php if(!$session_metadata['paediatric']) {?> style="display: none;"<?php }?> id="paediatric_icon_<?php echo $session['sessionId']?>" class="paediatric" title="Paediatric Session">Paediatric</div>
									</div>
								</th>
							</tr>
						</tfoot>
					</table>
					<div style="text-align:right; margin-right:10px; display: none;" id="buttons_<?php echo $session['sessionId']?>">
						<button type="submit" class="classy green tall" id="btn_save_<?php echo $session['sessionId']?>"><span class="button-span button-span-green">Save</span></button>
						<button type="submit" class="classy red tall" id="btn_cancel_<?php echo $session['sessionId']?>"><span class="button-span button-span-red">Cancel</span></button>
					</div>
				</div>
<?php
		}
	}
}
?>

<script type="text/javascript">
	var table_states = {};
	var purple_states = {};

	$(document).ready(function() {
		load_table_states();
		<?php if (Yii::app()->user->checkAccess('purplerinse')) {?>
			load_purple_states();
		<?php }?>
	});

	function load_table_states() {
		table_states = {};

		$('tbody').map(function() {
			if ($(this).attr('id') !== undefined) {
				var tbody_id = $(this).attr('id');

				table_states[tbody_id] = [];

				$(this).children('tr[id^="oprow_"]').map(function() {
					table_states[tbody_id].push($(this).attr('id'));
				});
			}
		});
	}

	function load_purple_states() {
		purple_states = {};

		$('tbody').map(function() {
			if ($(this).attr('id') !== undefined) {
				var tbody_id = $(this).attr('id').match(/[0-9]+/);

				purple_states[tbody_id] = {};

				purple_states[tbody_id]["consultant"] = $('#consultant_'+tbody_id).is(':checked');
				purple_states[tbody_id]["paediatric"] = $('#paediatric_'+tbody_id).is(':checked');
				purple_states[tbody_id]["anaesthetic"] = $('#anaesthetic_'+tbody_id).is(':checked');
				purple_states[tbody_id]["available"] = $('#available_'+tbody_id).is(':checked');
				purple_states[tbody_id]["general_anaesthetic"] = $('#general_anaesthetic_'+tbody_id).is(':checked');

				if ($('#consultant_'+tbody_id).is(':checked')) {
					$('#consultant_icon_'+tbody_id).show();
				} else {
					$('#consultant_icon_'+tbody_id).hide();
				}

				if ($('#paediatric_'+tbody_id).is(':checked')) {
					$('#paediatric_icon_'+tbody_id).show();
				} else {
					$('#paediatric_icon_'+tbody_id).hide();
				}

				if ($('#anaesthetic_'+tbody_id).is(':checked')) {
					$('#anaesthetist_icon_'+tbody_id).show();
				} else {
					$('#anaesthetist_icon_'+tbody_id).hide();
				}

				if ($('#available_'+tbody_id).is(':checked')) {
					$('#session_unavailable_'+tbody_id).hide();
				} else {
					$('#session_unavailable_'+tbody_id).show();
				}
			}
		});
	}

	function enable_sort(session_id) {
		$("#tbody_"+session_id).sortable({
			 helper: function(e, tr)
			 {
				 var $originals = tr.children();
				 var $helper = tr.clone();
				 $helper.children().each(function(index)
				 {
					 // Set helper cell sizes to match the original sizes
					 $(this).width($originals.eq(index).width())
				 });
				 return $helper;
			 },
			 placeholder: 'theatre-list-sort-placeholder'
		}).disableSelection();
		$("#theatre_list tbody").sortable('enable');
	}

	function disable_sort() {
		$("#theatre_list tbody").sortable('disable');
	}

	var selected_tbody_id = null;

	$('a.edit-sessions').die('click').live('click',function() {
		$('#loader_'+selected_tbody_id).show();

		cancel_edit();

		selected_tbody_id = $(this).attr('id').replace(/^edit-sessions_/,'');

		$('#updated-flash').hide();
		$('div[id="comments_ro_'+selected_tbody_id+'"]').hide();
		$('textarea[name="comments'+selected_tbody_id+'"]').show();
		$('span[id^="admitTime_ro_'+selected_tbody_id+'_"]').hide();
		$('input[id^="admitTime_'+selected_tbody_id+'_"]').show();
		enable_sort(selected_tbody_id);
		$('div.action_options').map(function() {
			var html = $(this).html();
			if (m = html.match(/edit-sessions_([0-9]+)/)) {
				$(this).html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_'+m[1]+'" href="#">Edit</a></span>');
			}
			if (m = html.match(/view-sessions_([0-9]+)/)) {
				$(this).html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_'+m[1]+'" href="#">Edit</a></span>');
			}
		});
		$('div.action_options').hide();
		$('#action_options_'+selected_tbody_id).show();
		$('#action_options_'+selected_tbody_id).html('<span class="aBtn"><a class="view-sessions" id="view-sessions_'+selected_tbody_id+'" href="#">View</a></span><span class="aBtn_inactive edit-event">Edit</span>');
		$('#btn_print').hide();
		$('tbody[id="tbody_'+selected_tbody_id+'"] td.confirm input[name^="confirm_"]').attr('disabled',false);
		$('tbody[id="tbody_'+selected_tbody_id+'"] td.td_sort').show();
		$('thead[id="thead_'+selected_tbody_id+'"] th.th_sort').show();
		$('#buttons_'+selected_tbody_id).show();
		$('div[id="purple_rinse_'+selected_tbody_id+'"]').show();
		$('th.footer').attr('colspan','10');
		return false;
	});

	$('a.view-sessions').die('click').live('click',function() {
		cancel_edit();
		return false;
	});

	$('button[id^="btn_cancel_"]').live('click',function() {
		if (!$(this).hasClass('inactive')) {
			$('#loader2_'+$(this).attr('id').match(/[0-9]+/)).show();
			disableButtons();
			setTimeout('edit_session_cancel_button('+$(this).attr('id').match(/[0-9]+/)+');',300);
		}
		return false;
	});

	function edit_session_cancel_button(id) {
		cancel_edit();
		enableButtons();
		$('#loader2_'+id).hide();
	}

	function cancel_edit() {
		if (selected_tbody_id !== null) {
			var tbody_id = "tbody_"+selected_tbody_id;

			if (table_states[tbody_id] !== undefined) {
				for (x in table_states[tbody_id]) {
					$('#'+table_states[tbody_id][x]).appendTo('#'+tbody_id);
				}
			}

			if (purple_states[selected_tbody_id] !== undefined) {
				$('#consultant_'+selected_tbody_id).attr('checked',purple_states[selected_tbody_id]["consultant"]);
				$('#paediatric_'+selected_tbody_id).attr('checked',purple_states[selected_tbody_id]["paediatric"]);
				$('#anaesthetic_'+selected_tbody_id).attr('checked',purple_states[selected_tbody_id]["anaesthetic"]);
				$('#available_'+selected_tbody_id).attr('checked',purple_states[selected_tbody_id]["available"]);
				$('#general_anaesthetic_'+selected_tbody_id).attr('checked',purple_states[selected_tbody_id]["general_anaesthetic"]);
			}

			view_mode();

			$('div[id^="buttons_"]').hide();
			$('th.footer').attr('colspan','9');
		}
	}

	function view_mode() {
		$('div[id^="comments_ro_"]').show();
		$('textarea[name^="comments"]').hide();
		$('span[id^="admitTime_ro_"]').show();
		$('input[id^="admitTime_"]').hide();
		disable_sort();
		$('div.action_options').map(function() {
			var html = $(this).html();
			if (m = html.match(/edit-sessions_([0-9]+)/)) {
				$(this).html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_'+m[1]+'" href="#">Edit</a></span>');
			}
			if (m = html.match(/view-sessions_([0-9]+)/)) {
				$(this).html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_'+m[1]+'" href="#">Edit</a></span>');
			}
		});
		$('div.action_options').show();
		$('td.td_sort').hide();
		$('th.th_sort').hide();

		// revert text changes
		$('span[id^="admitTime_ro_"]').map(function() {
			var m = $(this).attr('id').match(/^admitTime_ro_([0-9]+)_([0-9]+)$/);
			$('#admitTime_'+m[1]+'_'+m[2]).val($(this).html());
		});
		$('div[id^="comments_ro_"]').map(function() {
			var id = $(this).attr('id').match(/[0-9]+/);
			$('#comments'+id).val($(this).html());
		});

		$('div.purple_rinse').hide();
		$('#btn_print').show();
		$('input[name^="confirm_"]').attr('disabled',true);
	}

	$('input[id^="consultant_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			operations = [];

			$('#tbody_'+id).children('tr').map(function() {
				if ($(this).attr('id').match(/oprow/)) {
					operations.push($(this).attr('id').match(/[0-9]+/));
				}
			});

			$.ajax({
				type: "POST",
				data: "operations[]=" + operations.join("&operations[]="),
				url: "/theatre/requiresconsultant",
				success: function(html) {
					if (html == "1") {
						$('#consultant_'+id).attr('checked',true);
						alert("Sorry, you cannot remove the 'Consultant required' flag from this session because there are one or more patients booked into it who require a consultant.");
						return false;
					}
				}
			});
		}
	});

	$('input[id^="paediatric_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			patients = [];

			$('#tbody_'+id).children('tr').map(function() {
				$(this).children('td.hospital').map(function() {
					$(this).children('a').map(function() {
						patients.push($(this).html());
					});
				});
			});

			$.ajax({
				type: "POST",
				data: "patients[]=" + patients.join("&patients[]="),
				url: "/theatre/ischild",
				success: function(html) {
					if (html == "1") {
						$('#paediatric_'+id).attr('checked',true);
						alert("Sorry, you cannot remove the 'Paediatric' flag from this session because there are one or more patients booked into it who are paediatric.");
						return false;
					}
				}
			});
		}
	});

	$('input[id^="anaesthetic_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			operations = [];

			$('#tbody_'+id).children('tr').map(function() {
				if ($(this).attr('id').match(/oprow/)) {
					operations.push($(this).attr('id').match(/[0-9]+/));
				}
			});

			if (operations.length >0) {
				$.ajax({
					type: "POST",
					data: "operations[]=" + operations.join("&operations[]="),
					url: "/theatre/requiresanaesthetist",
					success: function(html) {
						if (html == "1") {
							$('#anaesthetic_'+id).attr('checked',true);
							alert("Sorry, you cannot remove the 'Anaesthetist required' flag from this session because there are one or more patients booked into it who require an anaesthetist.");
							return false;
						} else {
							$('#general_anaesthetic_'+id).attr('checked','false');
						}
					}
				});
			} else {
				$('#general_anaesthetic_'+id).attr('checked',false);
			}
		}
	});

	$('input[id^="general_anaesthetic_"]').click(function() {
		var id = $(this).attr('id').match(/[0-9]+/);

		if (!$(this).is(':checked')) {
			operations = [];

			$('#tbody_'+id).children('tr').map(function() {
				if ($(this).attr('id').match(/oprow/)) {
					operations.push($(this).attr('id').match(/[0-9]+/));
				}
			});

			if (operations.length >0) {
				$.ajax({
					type: "POST",
					data: "operations[]=" + operations.join("&operations[]="),
					url: "/theatre/requiresgeneralanaesthetic",
					success: function(html) {
						if (html == "1") {
							$('#general_anaesthetic_'+id).attr('checked',true);
							alert("Sorry, you cannot remove the 'General anaesthetic available' flag from this session because there are one or more patients booked into it who require a general anaesthetic.");
							return false;
						}
					}
				});
			}
		} else {
			$('#anaesthetic_'+id).attr('checked',true);
		}
	});
</script>
