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
if ($queue) {?>
	<div class="row">
	<div class="large-8 column">
		<?php
        foreach ($form_fields as $fld) {
            if (@$fld['type'] == 'widget') {
                $this->widget('OEModule\PatientTicketing\widgets\\'.$fld['widget_name'], array(
                        'ticket' => $this->ticket,
                        'label_width' => $this->label_width,
                        'data_width' => $this->data_width,
                        'form_name' => $fld['form_name'],
                        'form_data' => $form_data,
                    ));
            } else { ?>
			<fieldset class="field-row row">
				<div class="large-<?= $this->label_width ?> column">
					<label for="<?= $fld['form_name']?>"><?= $fld['label'] ?>:</label>
				</div>
				<div class="large-<?= $this->data_width ?> column end">
					<?php if (@$fld['choices']) {
    echo CHtml::dropDownList(
                                $fld['form_name'],
                                @$form_data[$fld['form_name']],
                                $fld['choices'],
                                array('empty' => ($fld['required']) ? ' - Please Select - ' : 'None'));
} else {
    //may need to expand this beyond textarea and select in the future.
                        $notes = @$form_data[$fld['form_name']];
    ?>
						<textarea id="<?= $fld['form_name']?>" name="<?= $fld['form_name']?>"><?=$notes?></textarea>
					<?php }?>
				</div>
			</fieldset>
		<?php }
        }
    if ($auto_save) {
        ?>
			<script>
				$(document).ready(function(){
					window.patientTicketChanged = true;
					window.changedTickets[<?=$this->current_queue_id?>]=true;
				});
			</script>
		<?php

    }
    ?>
	</div>
	<div class="large-4 column end">
		<?php	if ($this->patient_id) { ?>
			<div class="text-right">
                <ul>
                    <?php	foreach ($queue->event_types as $et) {?>
                        <li><a href="<?= Yii::app()->baseURL?>/<?=$et->class_name?>/default/create?patient_id=<?= $this->patient_id ?>" class="button small event-type-link auto-save" data-queue="<?= $this->current_queue_id?>"><?= $et->name ?></a></li>
                    <?php }
                    if ($print_letter_event) {?>
                        <li><a href="<?= Yii::app()->baseURL?>/<?=$print_letter_event->eventType->class_name?>/default/doPrintAndView/<?=$print_letter_event->id?>?all=1" class="button small event-type-link auto-save" data-queue="<?= $this->current_queue_id?>">Print Letter</a></li>
                    <?php } ?>
                </ul>
            </div>
            <?php echo @$extra_view_data['buttons']; ?>
		<?php }    ?>
	</div>
	</div>
<?php } ?>