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
	<div class="action_options" style="float: right;">
		<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" href="#">Edit</a></span>
	</div>
	<span style="margin-left: 10px; color: #f00; display: none;" id="updated-flash">
		Sessions updated!
	</span>
	<?php
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
								<th colspan="8" class="footer">Time unallocated: <span><?php echo $timeAvailable ?> min</span></th>
							</tr>
						</tfoot>
					</table>
				</div>
<?php
					}
?>
<h3 class="sessionDetails"><span class="date"><strong><?php echo date('d M',$timestamp)?></strong> <?php echo date('Y',$timestamp)?></span> - <strong><span class="day"><?php echo date('l',$timestamp)?></span>, <span class="time"><?php echo substr($session['startTime'], 0, 5)?> - <?php echo substr($session['endTime'], 0, 5)?></span></strong> for <?php echo !empty($session['firm_name']) ? $session['firm_name'] : 'Emergency List' ?> <?php echo !empty($session['specialty_name']) ? 'for (' . $session['specialty_name'] . ')' : '' ?> </h3>
				<div class="theatre-sessions whiteBox clearfix">

						<div class="sessionComments" style="display:block; float:right; width:250px; ">
							<form>
								Comments<br/>
								<div style="height: 0.4em;"></div>
								<textarea style="display: none;" rows="2" style="width:245px;" name="comments<?php echo $session['sessionId'] ?>" id="comments<?php echo $session['sessionId'] ?>"><?php echo $session['comments'] ?></textarea>
								<div id="comments_ro_<?php echo $session['sessionId']?>"><?php echo strip_tags($session['comments'])?></div>
							</form>
							<?/*<div class="modifyComments"><span class="edit"><a href="#" id="editComments<?php echo $session['sessionId'] ?>" name="<?php echo $session['sessionId'] ?>">Edit comment</a></span></div>*/?>
						</div>

					<table id="theatre_list">
						<thead>
							<tr>
								<th>Admit time</th>
								<th>Hospital #</th>
								<th>Patient (Age)</th>
								<th>[Eye] Operation</th>
								<th>Anesth</th>
								<th>Ward</th>
								<th>Info</th>
							</tr>
						</thead>
						<tbody>
<?php
					$previousSequenceId = $session['sequenceId'];
					$timeAvailable = $session['sessionDuration'];
				}

				if (!empty($session['patientId'])) {
					$timeAvailable -= $session['operationDuration'];
?>
							<tr id="oprow_<?php echo $session['operationId'] ?>">
								<td class="session">
									<input style="display: none;" type="text" name="admitTime<?php echo $session['operationId']?>" id="admitTime<?php echo $session['operationId'] ?>" value="<?php echo substr($session['admissionTime'], 0, 5)?>" size="4">
									<span id="admitTime_ro_<?php echo $session['operationId']?>"><?php echo substr($session['admissionTime'], 0, 5)?></span>
								</td>
								<td class="hospital"><?php echo CHtml::link(
									$session['patientHosNum'],
									'/patient/episodes/' . $session['patientId'] . '/event/' . $session['eventId']
											);
								?></td>
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
			}
?>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="8" class="footer">Time unallocated: <span><?php echo $timeAvailable ?> min</span></th>
							</tr>
						</tfoot>
					</table>
				</div>
<?php
		}
	}
}
?>

<script type="text/javascript">
	$('a[id^="editAdmitTime"]').click(function() {
		id = this.id.replace(/editAdmitTime/i, "");
		value = $('#admitTime' + id).val();

		$.ajax({
					'url': '<?php echo Yii::app()->createUrl('theatre/updateAdmitTime'); ?>',
					'type': 'POST',
					'data': 'id=' + id + '&admission_time=' + value,
					'success': function(data) {
				return false;
					}
		});

		return false;
	});

				$('a[id^="editComments"]').click(function() {
								id = this.name;
								value = $('#comments' + this.name).val();

								$.ajax({
												'url': '<?php echo Yii::app()->createUrl('theatre/updateSessionComments'); ?>',
												'type': 'POST',
												'data': 'id=' + id + '&comments=' + value,
												'success': function(data) {
																return false;
												}
								});

								return false;
				});

	$('a[id^="u_"]').click(function() {
		id = this.id.replace(/u_/i, "");

					$.ajax({
								'url': '<?php echo Yii::app()->createUrl('theatre/moveOperation'); ?>',
								'type': 'POST',
								'data': 'id=' + id + '&up=1',
								'success': function(data) {
				if (data == 1) {
					$('#oprow_' + id).prev().before($('#oprow_' + id));
				}
								},
					});

		return false;
	});

	$('a[id^="d_"]').click(function() {
		id = this.id.replace(/d_/i, "");

		$.ajax({
			'url': '<?php echo Yii::app()->createUrl('theatre/moveOperation'); ?>',
			'type': 'POST',
			'data': 'id=' + id + '&up=0',
			'success': function(data) {
				if (data == 1) {
					$('#oprow_' + id).next().after($('#oprow_' + id));
				}
			},
		});

		return false;
	});

	function enable_sort() {
		$("#theatre_list tbody").sortable({
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
			 placeholder: 'theatre-list-sort-placeholder',
			 stop: function(e, tr) {
				 n = 0;

				 $('tbody').children().map(function() {
					 if ($(this).attr('id') !== undefined) {
						 if (m = $(this).attr('id').match(/^oprow_([0-9]+)$/)) {
							 $('#admitTime'+m[1]).val(times[n++]);
						 }
					 }
				 });
			 }
		}).disableSelection();
		$("#theatre_list tbody").sortable('enable');
	}

	function disable_sort() {
		$("#theatre_list tbody").sortable('disable');
	}

	$('a.edit-sessions').live('click',function() {
		$('#updated-flash').hide();
		$('div[id^="comments_ro_"]').hide();
		$('textarea[name^="comments"]').show();
		$('span[id^="admitTime_ro_"]').hide();
		$('input[name^="admitTime"]').show();
		enable_sort();
		$('#btn_save').show();
		$('#btn_cancel').show();
		$('div.action_options').html('<span class="aBtn"><a class="view-sessions" href="#">View</a></span><span class="aBtn_inactive edit-event">Edit</span>');
	});

	$('a.view-sessions').live('click',function() {
		view_mode();
	});

	$('#btn_cancel').live('click',function() {
		view_mode();
	});

	function view_mode() {
		$('div[id^="comments_ro_"]').show();
		$('textarea[name^="comments"]').hide();
		$('span[id^="admitTime_ro_"]').show();
		$('input[name^="admitTime"]').hide();
		disable_sort();
		$('#btn_save').hide();
		$('#btn_cancel').hide();
		$('div.action_options').html('<span class="aBtn_inactive">View</span><span class="aBtn edit-event"><a class="edit-sessions" href="#">Edit</a></span>');

		// revert text changes
		$('span[id^="admitTime_ro_"]').map(function() {
			var id = $(this).attr('id').match(/[0-9]+/);
			$('#admitTime'+id).val($(this).html());
		});
		$('div[id^="comments_ro_"]').map(function() {
			var id = $(this).attr('id').match(/[0-9]+/);
			$('#comments'+id).val($(this).html());
		});
	}
</script>
