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

if (!$reschedule) {
	echo CHtml::form(array('booking/create'), 'post', array('id' => 'bookingForm'));
} else {
	echo CHtml::form(array('booking/update'), 'post', array('id' => 'bookingForm'));
}

?>
	<h4>Other operations in this session: <?php echo abs($session['time_available']) . " min {$minutesStatus}"; ?></h4>

	<div class="theatre-sessions">
	<table id="appointment_list">
		<thead>
			<tr>
				<th>Operation list overview</th>
				<th>Date: <?php echo Helper::convertDate2NHS($session['date']); ?></th>
				<th>Anaesthetic type</th>
				<th>Session time: <?php echo substr($session['start_time'], 0, 5) . ' - '
				. substr($session['end_time'], 0, 5); ?></th>
				<th>Admission time</th>
				<th>Comments</th>
			</tr>
		</thead>
		<tbody>

<?php
	$counter = 1;
	foreach ($bookings as $booking) {
		$thisOperation = $booking->elementOperation;
		if(!$thisOperation->event) {
			// Event has been marked as deleted. This is a conflicted state, but for now the best thing we can do is skip it
			continue;
		}
		// Use nopas flag as temporary work around for merged patients
		$patient_id = $thisOperation->event->episode->patient_id;
		$patient = Patient::model()->noPas()->findByPk($patient_id);
		$procedures = $thisOperation->procedures;
		$procedureNames = array();
		foreach ($procedures as $procedure) {
			$procedureNames[] = $procedure->term;
		}
		$procedureList = implode(', ', $procedureNames);
		if (empty($procedureList)) {
			$procedureList = 'No procedures';
		} ?>

			<tr>
				<td><?php echo $counter?>. <?php echo $patient->getDisplayName() ?></td>
				<td><?php echo $procedureList; ?></td>
				<td><?php echo $thisOperation->anaesthetic_type->name?></td>
				<td><?php echo "{$thisOperation->total_duration} minutes"; ?></td>
				<td><?php echo $booking->admission_time ?></td>
				<td><?php echo $thisOperation->comments?></td>
			</tr>
<?php
		$counter++;
	} ?>
	</tbody>
		<tfoot>
			<tr>
				<th colspan="6"><?php echo ($counter - 1) . ' booking';
	if (($counter - 1) != 1) {
		echo 's';
	}
	if ($_POST['bookable']) {
		echo ' currently scheduled';
	} else {
		echo ' were scheduled';
	}
		?></th>
			</tr>
		</tfoot>
</table>
</div>

<?php if ($_POST['bookable']) {?>
	<div class="eventDetail clearfix">
		<div class="label"><strong>Admission Time:</strong></div>
		<div class="data"> 
			<input type="text" id="Booking_admission_time" name="Booking[admission_time]" value="<?php echo ($session['start_time'] == '13:30:00') ? '12:00' : date('H:i', strtotime('-1 hour', strtotime($session['start_time']))) ?>" size="6">
			<span id="Booking_admission_time_error"></span>
		</div>
	</div>

	<div class="eventDetail clearfix" style="position:relative;">
		<div class="label"><strong>Session Comments:</strong>
			<img src="<?php echo Yii::app()->createUrl('img/_elements/icons/alerts/comment.png')?>" alt="comment" width="17" height="17" style="position:absolute; bottom:10px; left:10px;" />
		</div>
		<div class="data">
			<div class="sessionComments" style="width:400px; display:inline-block; margin-bottom:0; ">
				<textarea id="Session_comments" name="Session[comments]" rows="2" style="width:395px;"><?php echo htmlspecialchars($session['comments']) ?></textarea>
			</div>
		</div>	
	</div>

	<?php
	if ($reschedule) {
		echo CHtml::hiddenField('booking_id', $operation->booking->id);
	}
	echo CHtml::hiddenField('Booking[element_operation_id]', $operation->id);
	echo CHtml::hiddenField('Booking[session_id]', $session['id']);
	?>

	<?php if ($reschedule) { ?>
	<h3>Reason for Reschedule</h3>
	<div class="eventDetail clearfix" style="position:relative;">
		<div class="label"><strong><?php echo CHtml::label('Reschedule Reason: ', 'cancellation_reason'); ?></strong></div>
		<?php if (date('Y-m-d') == date('Y-m-d', strtotime($operation->booking->session->date))) {
			$listIndex = 3;
		} else {
			$listIndex = 2;
		} ?>
		<div class="data">
		<?php echo CHtml::dropDownList('cancellation_reason', '',
			CancellationReason::getReasonsByListNumber($listIndex),
			array('empty' => 'Select a reason')
		); ?>
		</div>
	</div>
	<div class="eventDetail clearfix" style="position:relative;">
		<div class="label"><strong><?php echo CHtml::label('Reschedule Comments: ', 'cancellation_comment'); ?></strong></div>
		<div class="data">
			<textarea name="cancellation_comment" rows=3 cols=50></textarea>
		</div>
	</div>
	<?php } ?>

	<div class="eventDetail clearfix" style="position:relative;">
		<div class="label"><strong><?php echo CHtml::label('Operation Comments: ', 'operation_comments'); ?></strong></div>
		<div class="data">
			<textarea id="operation_comments" name="Operation[comments]" rows=3 cols=50><?php echo $operation->comments ?></textarea>
		</div>
	</div>

	<div style="margin: 0.5em 0;">
		<span id="dateSelected">Date/Time currently selected: <span class="highlighted"><?php echo Helper::convertDate2NHS($session['date']); ?>, <?php echo substr($session['start_time'], 0, 5) . ' - ' . substr($session['end_time'], 0, 5); ?></span></span>
	</div>
	<div style="margin-top:10px;">
	<button type="submit" class="classy green venti" id="confirm_slot"><span class="button-span button-span-green">Confirm slot</span></button>
	<button type="button" class="classy red venti" id="cancel_scheduling"><span class="button-span button-span-red">Cancel <?php if($reschedule) { ?>re-<?php } ?>scheduling</span></button>
	</div>

	<?php
	echo CHtml::endForm();
	?>

	<div class="alertBox" style="margin-top: 10px; display:none"><p>Please fix the following input errors:</p>
	<ul><li>&nbsp;</li></ul></div>

	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/jquery.validate.min.js'))?>
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->createUrl('js/additional-validators.js'))?>
	<script type="text/javascript">
		$('button#cancel_scheduling').click(function() {
			if (!$(this).hasClass('inactive')) {
				disableButtons();
				document.location.href = '<?php echo Yii::app()->createUrl('patient/episodes/'.$operation->event->episode->patient->id)?>';
			}
			return false;
		});
		
		$('#bookingForm').validate({
			rules : {
				"Booking[admission_time]" : {
					required: true,
					time: true
				},
				"cancellation_reason" : {
					required: true
				}
			},
			submitHandler: function(form){
				if (!$('#bookingForm button#confirm_slot').hasClass('inactive')) {
					disableButtons();

					$.ajax({
						'type': 'POST',
						'url': <?php if ($reschedule) {?>'<?php echo Yii::app()->createUrl('booking/update')?>',<?php }else{?>'<?php echo Yii::app()->createUrl('booking/create')?>',<?php }?>
						'data': $('#bookingForm').serialize(),
						'dataType': 'json',
						'success': function(data) {
							var n=0;
							var html = '';
							$.each(data, function(key, value) {
								html += '<ul><li>'+value+'</li></ul>';
								n += 1;
							});

							if (n == 0) {
								window.location.href = '<?php echo Yii::app()->createUrl('patient/event/'.$operation->event->id)?>';
							} else {
								$('div.alertBox').show();
								$('div.alertBox').html(html);
							}

							enableButtons();
							return false;
						}
					});

					return false;
				} else {
					return false;
				}
			}
		});
	</script>
<?php }?>
