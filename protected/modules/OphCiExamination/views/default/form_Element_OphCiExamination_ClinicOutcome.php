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
?>
<?php
$queues = array();
if ($ticket_api = Yii::app()->moduleAPI->get('PatientTicketing')) {
    $queues = $element->getPatientTicketQueues($this->firm, $this->patient);
}
?>

<div class="element-fields">
    <div id="div_<?php echo CHtml::modelName($element)?>_status">
		<div class="field-row row">
			<div class="large-3 column">
				<label for="<?php echo CHtml::modelName($element).'_status_id';?>">
					<?php echo $element->getAttributeLabel('status_id')?>:
				</label>
			</div>
			<div class="large-3 column end">
				<?php
                $outcomes = \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status::model()->activeOrPk($element->status_id)->bySubspecialty($this->firm->getSubspecialty())->findAll();
                $html_options = array('empty' => '- Please select -', 'nowrapper' => true, 'options' => array());
                $authRoles = Yii::app()->authManager->getRoles(Yii::app()->user->id);

                foreach ($outcomes as $opt) {
                    $options = array('data-followup' => $opt->followup, 'data-ticket' => $opt->patientticket);
                    if ($opt->patientticket && (!count($queues) || !isset($authRoles['Patient Tickets']))) {
                        $options['disabled'] = true;
                    }
                    $html_options['options'][(string) $opt->id] = $options;
                }
                echo $form->dropDownList($element, 'status_id', \CHtml::listData($outcomes, 'id', 'name'), $html_options)?>
			</div>
		</div>
	</div>

	<?php if ($ticket_api) {
    ?>
		<div id="div_<?= CHtml::modelName($element)?>_patientticket"
				<?php if (!($element->status && $element->status->patientticket)) {
    ?> style="display: none;"<?php 
}
    ?>
				data-queue-ass-form-uri="<?= $ticket_api->getQueueAssignmentFormURI()?>">
			<!-- TODO, this should be pulled from the ticketing module somehow -->
			<?php
            $ticket = $element->getPatientTicket();
    if ($ticket) {
        ?>

				<span class="field-info">Already Referred to Virtual Clinic:</span><br />
				<?php $this->widget($ticket_api::$TICKET_SUMMARY_WIDGET, array('ticket' => $ticket));
        ?>
			<?php 
    } else {
        ?>
				<fieldset class="field-row row">
					<legend class="large-3 column">
						Virtual Clinic:
					</legend>
					<div class="large-3 column">
						<?php
                            if (count($queues) == 0) {
                                ?>
								<span>No valid Virtual Clinics available</span>
							<?php

                            } elseif (count($queues) == 1) {
                                echo reset($queues);
                                $qid = key($queues);
                                $_POST['patientticket_queue'] = $qid;
                                ?>
								<input type="hidden" name="patientticket_queue" value="<?= $qid ?>" />

							<?php

                            } else {
                                echo CHtml::dropDownList('patientticket_queue', @$_POST['patientticket_queue'], $queues,
                                    array('empty' => '- Please select -', 'nowrapper' => true, 'options' => array()));
                            }
        ?>
					</div>
					<div class="large-1 column end">
						<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;">
					</div>
				</fieldset>
				<div id="queue-assignment-placeholder">
					<?php if (@$_POST['patientticket_queue']) {
    $this->widget($ticket_api::$QUEUE_ASSIGNMENT_WIDGET, array('queue_id' => $_POST['patientticket_queue'], 'label_width' => 3, 'data_width' => 5));
}
        ?>
				</div>
			<?php 
    }
    ?>
		</div>
	<?php 
} ?>

	<div id="div_<?php echo CHtml::modelName($element)?>_followup"<?php if (!($element->status && $element->status->followup)) {
    ?> style="display: none;"<?php 
}?>>
		<fieldset class="field-row row">
			<legend class="large-3 column">
					<?php echo $element->getAttributeLabel('followup_quantity')?>:
			</legend>
			<div class="large-9 column end">
				<?php
                $html_options = array('empty' => '- Please select -', 'options' => array());
                echo CHtml::activeDropDownList($element, 'followup_quantity', $element->getFollowUpQuantityOptions(), array_merge($html_options, array('class' => 'inline')))?>
				<?php
                $html_options = array('empty' => '- Please select -', 'options' => array());
                echo CHtml::activeDropDownList($element, 'followup_period_id', CHtml::listData(\Period::model()->findAll(array('order' => 'display_order')), 'id', 'name'), array_merge($html_options, array('class' => 'inline')))?>
				<label class="inline">
					<?php echo CHtml::activeCheckBox($element, 'community_patient')?>
					<?php echo $element->getAttributeLabel('community_patient')?>
				</label>
			</div>
		</fieldset>
	</div>

	<div id="div_<?php echo CHtml::modelName($element)?>_role"<?php if (!($element->status && $element->status->followup)) {
    ?> style="display: none;"<?php 
}?>>
		<fieldset class="field-row row">
			<legend class="large-3 column">
				<?php echo $element->getAttributeLabel('role')?>:
			</legend>
			<div class="large-9 column end">
				<div class="row">
					<div class="large-3 column">
						<?php
                        $html_options = array('empty' => '- Please select -', 'nowrapper' => true, 'options' => array());
                        echo $form->dropDownList($element, 'role_id', '\OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Role', $html_options) ?>
					</div>
					<div class="large-3 column end">
						<?php echo CHtml::activeTextField($element, 'role_comments', array('autocomplete' => Yii::app()->params['html_autocomplete']))?>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
    <div class="field-row textMacros">
        <?php $this->renderPartial('_attributes', array('element' => $element, 'field' => 'description', 'form' => $form))?>
    </div>
    <div class="field-row">
        <?php echo $form->textArea($element, 'description', array('rows' => '1', 'class' => 'autosize', 'nowrapper' => true), false, array('placeholder' => $element->getAttributeLabel('description')))?>
    </div>

	<script type="text/javascript">
			var Element_OphCiExamination_ClinicOutcome_templates = {
			<?php foreach (\OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Template::model()->findAll() as $template) {
    ?>
			"<?php echo $template->id?>": {
				"clinic_outcome_status_id": <?php echo $template->clinic_outcome_status_id ?>,
				"followup_quantity": "<?php echo $template->followup_quantity ?>",
				"followup_period_id": "<?php echo $template->followup_period_id ?>"
			},
			<?php 
} ?>
			};
	</script>
</div>
