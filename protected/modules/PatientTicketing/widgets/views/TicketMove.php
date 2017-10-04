<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
    $t_svc = Yii::app()->service->getService('PatientTicketing_Ticket');
    $q_svc = Yii::app()->service->getService('PatientTicketing_QueueSet');
    if ($flash_message = Yii::app()->user->getFlash('patient-ticketing-'.$q_svc->getQueueSetForTicket($this->ticket->id)->getId())) {
        ?>
		<div class="alert-box with-icon success">
			<?php echo $flash_message; ?>
		</div>
	<?php

    }
?>


<form id="PatientTicketing-moveForm-<?= $this->ticket->id ?>" class="PatientTicketing-moveTicket" data-patient-id="<?= $this->ticket->patient_id ?>">
	<input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
	<input type="hidden" name="from_queue_id" value="<?= $this->ticket->current_queue->id ?>" />
	<input type="hidden" name="ticket_id" value="<?= $this->ticket->id ?>" />
	<div>
		<h3><?= $t_svc->getTicketActionLabel($this->ticket) ?></h3>
		<?php
            if (count($this->outcome_options) > 1) { ?>
				<fieldset class="field-row row">
					<div class="large-2 column">
						<label for="to_queue_id">To:</label>
					</div>
					<div class="large-3 column">
						<?php
                            echo CHtml::dropDownList('to_queue_id', $this->outcome_queue_id, $this->outcome_options, array(
                                    'id' => 'to_queue_id-'.$this->ticket->id,
                                    'empty' => ' - Please Select -', ));
                ?>
					</div>
					<div class="large-1 column end">
						<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;">
					</div>
				</fieldset>
		<?php
            } else {
                ?>
				<input type="hidden" name="to_queue_id" value="<?=$this->outcome_queue_id?>" />
		<?php

            }
        ?>
	</div>
	<div id="PatientTicketing-queue-assignment" data-queue="<?=$this->ticket->current_queue->id?>">
		<?php

            $buttons = '<div class="buttons text-right">
                            <button class="secondary small ok" type="button" data-queue="'.$this->ticket->current_queue->id.'">OK</button>
                            <button class="warning small cancel" type="button" data-queue="'.$this->ticket->current_queue->id.'" data-category="'.$this->ticket->current_queue->queueset->category_id.'">Cancel</button>
                        </div>';

            $buttons_drawn = false;

            if ($this->outcome_queue_id) {
                $this->widget('OEModule\PatientTicketing\widgets\QueueAssign', array(
                        'queue_id' => $this->outcome_queue_id,
                        'patient_id' => $this->ticket->patient_id,
                        'current_queue_id' => $this->ticket->current_queue->id,
                        'ticket' => $this->ticket,
                        'extra_view_data' => array('buttons'=>$buttons)
                    ));

                $buttons_drawn = true;
            }
        ?>

	</div>
	<div class="alert-box alert hidden"></div>
    <?php if(!$buttons_drawn) echo $buttons; ?>
</form>