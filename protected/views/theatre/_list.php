<?php
/*
_____________________________________________________________________________
(C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
(C) OpenEyes Foundation, 2011
This file is part of OpenEyes.
OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
_____________________________________________________________________________
http://www.openeyes.org.uk	 info@openeyes.org.uk
--
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
		Sessions updated!
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
									</div>
									<?php if(array_sum($session_metadata)) { ?>
									<div class="metadata">
										<?php if($session_metadata['consultant']) { ?><div class="consultant" title="Consultant Present">Consultant</div><?php } ?>
										<?php if($session_metadata['anaesthetist']) { ?><div class="anaesthetist" title="Anaesthetist Present">Anaesthetist</div><?php } ?>
										<?php if($session_metadata['paediatric']) { ?><div class="paediatric" title="Paediatric Session">Paediatric</div><?php } ?>
									</div>
									<?php } ?>
								</th>
							</tr>
						</tfoot>
					</table>
					<div style="text-align:right; margin-right:10px; display: none;" id="buttons_<?php echo $previousSessionId?>">
						<button type="submit" class="classy green tall" id="btn_save_<?php echo $previousSessionId?>"><span class="button-span button-span-green">Save</span></button>
						<button type="submit" class="classy red tall" id="btn_cancel_<?php echo $previousSessionId?>"><span class="button-span button-span-red">Cancel</span></button>
					</div>
				</div>
<?php
					}
?>
<h3 class="sessionDetails"><span class="date"><strong><?php echo date('d M',$timestamp)?></strong> <?php echo date('Y',$timestamp)?></span> - <strong><span class="day"><?php echo date('l',$timestamp)?></span>, <span class="time"><?php echo substr($session['startTime'], 0, 5)?> - <?php echo substr($session['endTime'], 0, 5)?></span></strong> for <?php echo !empty($session['firm_name']) ? $session['firm_name'] : 'Emergency List' ?> <?php echo !empty($session['specialty_name']) ? 'for (' . $session['specialty_name'] . ')' : '' ?> </h3>
				<div class="action_options" id="action_options_<?php echo $session['sessionId']?>" style="float: right;">
					<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" id="edit-sessions_<?php echo $session['sessionId']?>" href="#">Edit</a></span>
				</div>
				<div class="theatre-sessions whiteBox clearfix">
					<div class="sessionComments" style="display:block; float:right; width:205px; ">
						<form>
							<h4>Comments</h4>
							<textarea style="display: none;" rows="2" name="comments<?php echo $session['sessionId'] ?>" id="comments<?php echo $session['sessionId'] ?>"><?php echo $session['comments'] ?></textarea>
							<div id="comments_ro_<?php echo $session['sessionId']?>"><?php echo strip_tags($session['comments'])?></div>
						</form>
					</div>
					<table id="theatre_list">
						<thead>
							<tr>
								<th>Admit time</th>
								<th class="th_sort" style="display: none;">Sort</th>
								<th>Hospital #</th>
								<th>Confirm</th>
								<th>Patient (Age)</th>
								<th>[Eye] Operation</th>
								<th>Anesth</th>
								<th>Ward</th>
								<th>Info</th>
							</tr>
						</thead>
						<tbody id="tbody_<?php echo $session['sessionId']?>">
<?php
					$previousSequenceId = $session['sequenceId'];
					$previousSessionId = $session['sessionId'];
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
									</div>
									<?php if(array_sum($session_metadata)) { ?>
									<div class="metadata">
										<?php if($session_metadata['consultant']) { ?><div class="consultant" title="Consultant Present">Consultant</div><?php } ?>
										<?php if($session_metadata['anaesthetist']) { ?><div class="anaesthetist" title="Anaesthetist Present">Anaesthetist</div><?php } ?>
										<?php if($session_metadata['paediatric']) { ?><div class="paediatric" title="Paediatric Session">Paediatric</div><?php } ?>
									</div>
									<?php } ?>
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

	$(document).ready(function() {
		load_table_states();
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
		$('td.td_sort').show();
		$('th.th_sort').show();
		$('#btn_print').hide();
		$('input[name^="confirm_"]').attr('disabled',false);
		$('#buttons_'+selected_tbody_id).show();
		return false;
	});

	$('a.view-sessions').die('click').live('click',function() {
		view_mode();
		return false;
	});

	$('button[id^="btn_cancel_"]').live('click',function() {
		cancel_edit();
		return false;
	});

	function cancel_edit() {
		if (selected_tbody_id !== null) {
			var tbody_id = "tbody_"+selected_tbody_id;

			if (table_states[tbody_id] !== undefined) {
				for (x in table_states[tbody_id]) {
					$('#'+table_states[tbody_id][x]).appendTo('#'+tbody_id);
				}
			}

			view_mode();

			$('div[id^="buttons_"]').hide();
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

		$('#btn_print').show();
		$('input[name^="confirm_"]').attr('disabled',true);
	}
</script>
