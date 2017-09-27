<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
if (!Yii::app()->user->checkAccess('Super schedule operation') && Yii::app()->params['future_scheduling_limit'] && $date > date('Y-m-d', strtotime('+'.Yii::app()->params['future_scheduling_limit']))) {?>
	<div class="alert-box alert with-icon" style="margin-top: 10px;">
		This date is outside the allowed booking window of <?php echo Yii::app()->params['future_scheduling_limit']?> and so cannot be booked into.
	</div>
<?php }?>

<h4>Select a session time:</h4>
<div id="theatre-times">

	<?php
        $i = 0;
        foreach ($theatres as $i => $theatre) {
            ?>

	<h5><?php echo $theatre->name ?>
		<?php if ($theatre->site) { 
			echo ' ('.$theatre->site->name.')'; 
		}?>
	</h5>
	<div id="theatre-times_tab_<?php echo $i ?>" class="sessionTimes">

		<?php foreach ($theatre->sessions as $j => $session) {
            if ($session->id != @$selectedSession->id) {?>
				<a href="<?php echo 
					Yii::app()->createUrl('/'.$operation->event->eventType->class_name.'/booking/'.($operation->booking ? 're' : '')
						.'schedule/'.$operation->event_id).'?'.
                        implode('&', array(
                            'firm_id='.($firm->id ? $firm->id : 'EMG'),
                            'date='.date('Ym', strtotime($date)),
                            'day='.CHtml::encode($_GET['day']),
                            'session_id='.$session->id,
                            'referral_id='.$operation->referral_id, )); ?>#book">
			<?php }?>
				<div class="timeBlock <?php echo $session->id == @$selectedSession->id ? 'selected_session' : $session->status ?><?php if (strtotime(date('Y-m-d')) > strtotime($session->date)) { echo ' inthepast'; } elseif ($session->operationBookable($operation)) { echo ' bookable';}?>" id="bookingSession<?php echo $session->id ?>">
					<div class="mainInfo">
						<div class="time"><?php echo substr($session->start_time, 0, 5) ?> - <?php echo substr($session->end_time, 0, 5) ?></div>
						<div class="timeLeft">
							(<?php echo abs($session->availableMinutes) ?> min
							<?php echo $session->minuteStatus?>)
						</div>
						<div class="session_id"><?php echo $session->id ?></div>
					</div>
					<?php if ($session->consultant || $session->anaesthetist || $session->paediatric) {?>
					<div class="metadata">
						<?php if ($session->consultant) {?>
							<div class="consultant" title="Consultant Present">Consultant</div>
						<?php }?>
						<?php if ($session->anaesthetist) {?>
							<div class="anaesthetist" title="Anaesthetist Present">Anaesthetist
							<?php if ($session->general_anaesthetic) {?> 
								(GA)
							<?php }?>
							</div>
						<?php }?>
						<?php if ($session->paediatric) {?>
							<div class="paediatric" title="Paediatric Session">Paediatric</div>
						<?php }?>
					</div>
					<?php }?>
				</div>
			<?php if ($session->id != @$selectedSession->id) {?>
				</a>
			<?php }?>
		<?php }?>
	</div>

	<?php if (isset($selectedSession) && !$selectedSession->operationBookable($operation)) {?>
		<div class="alert-box alert with-icon" style="margin-top: 10px;">
			<?php echo CHtml::encode($selectedSession->unbookableReason($operation))?>
		</div>
	<?php }?>

	<?php
        ++$i;
        }
    ?>

	<?php if ($i == 0) {?>
		<h5>Sorry, this firm has no sessions on the selected day.</h5>
	<?php }?>
</div>
