<?php /**
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
<fieldset class="element-fields">
	<?php
	/**
	 * Check the Subspecialty is 'Cataract', and check opbooking_disable_both_eyes is True.  Used to remove BOTH eyes option for Cataract operations.
	 */
	if ($episode = $this->patient->getEpisodeForCurrentSubspecialty()) {
	}
	?>
	<?php
		if ($episode->getSubspecialtyText() == "Cataract" And Yii::app()->params['opbooking_disable_both_eyes'] == true) {
			?>
			<?php echo $form->radioButtons($element, 'eye_id', CHtml::listData(Eye::model()->findAll(array('condition' => 'name != "Both"', 'order' => 'display_order asc')), 'id', 'name'))?>
	<?php
		} else {
	?>
			<?php echo $form->radioButtons($element, 'eye_id', CHtml::listData(Eye::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'))?>
	<?php
	}
	?>
	<?php $form->widget('application.widgets.ProcedureSelection', array(
        'element' => $element,
        'durations' => true,
    ))?>
	<?php echo $form->radioBoolean($element, 'consultant_required')?>
	<?php echo $form->dropDownList($element, 'named_consultant_id', CHtml::listData(User::model()->findAll(array('condition' => 'is_consultant = 1 and is_surgeon=1', 'order' => 'last_name, first_name')), 'id', 'reversedFullName'), array('empty' => '- Please select -'), false, array('field' => 3));?>
	<?php echo $form->radioBoolean($element, 'senior_fellow_to_do')?>
	<?php echo $form->radioBoolean($element, 'any_grade_of_doctor')?>

    <?php echo $form->checkBoxes($element, 'AnaestheticType', 'anaesthetic_type', 'Anaesthetic Type',
        false, false, false, false,
        array(
                'fieldset-class' => $element->getError('anaesthetic_type') ? 'highlighted-error' : ''
        )
    ); ?>

	<?php $form->radioBoolean($element, 'anaesthetist_preop_assessment') ?>
	<?php $form->radioButtons($element, 'anaesthetic_choice_id', 'OphTrOperationbooking_Anaesthetic_Choice') ?>
	<?php $form->radioBoolean($element, 'stop_medication') ?>
	<?php $form->textArea($element, 'stop_medication_details', array('rows' => 4), true, array(), array_merge($form->layoutColumns, array('field' => 4))) ?>
	<?php echo $form->radioBoolean($element, 'overnight_stay')?>
    <?php

        $options = array(
            $this->selectedSiteId=>array('selected'=>true)
        );

        echo $form->dropDownList(
            $element,
            'site_id',
            CHtml::listData(OphTrOperationbooking_Operation_Theatre::getSiteList(), 'id', 'short_name'),
            array('empty' => '- None -', 'options' => $options),
            false,
            array('field' => 2));
    ?>
	<?php echo $form->radioButtons($element, 'priority_id', CHtml::listData(OphTrOperationbooking_Operation_Priority::model()->notDeletedOrPk($element->priority_id)->findAll(array('order' => 'display_order asc')), 'id', 'name'))?>
	<?php
        if (Yii::app()->params['ophtroperationbooking_referral_link']) {
            ?>
		<div class="row field-row">
	<?php
            if ($element->canChangeReferral()) {
                ?>

				<div class="large-2 column">
					<label for="Element_OphTrOperationbooking_Operation_referral_id"><?= $element->getAttributeLabel('referral_id');?></label>
				</div>
				<div class="large-4 column">
					<?php
                    $html_options = array('options' => array(), 'empty' => '- No valid referral available -', 'nowrapper' => true);
                $choices = $this->getReferralChoices();
                foreach ($choices as $choice) {
                    if ($active_rtt = $choice->getActiveRTT()) {
                        if (count($active_rtt) == 1) {
                            $html_options['options'][(string) $choice->id] = array(
                                        'data-clock-start' => Helper::convertDate2NHS($active_rtt[0]->clock_start),
                                        'data-breach' => Helper::convertDate2NHS($active_rtt[0]->breach),
                                );
                        }
                    }
                }
                echo $form->dropDownList($element, 'referral_id', CHtml::listData($this->getReferralChoices(), 'id', 'description'), $html_options, false, array('field' => 2));
                ?>
				</div>
				<div class="large-4 column end">
					<span id="rtt-info" class="rtt-info" style="display: none">Clock start - <span id="rtt-clock-start"></span> Breach - <span id="rtt-breach"></span></span>
				</div>
				<?php
            } else {?>
					<div class="large-2 column"><label>Referral:</label></div>
					<div class="large-4 column end">
						<?php if ($element->referral) {
                            echo $element->referral->getDescription();
                        } else {
                            echo 'No Referral Set';
                        }
                ?></div>
	<?php } ?>
		</div>
	<?php } ?>

	<?php echo $form->datePicker($element, 'decision_date', array('maxDate' => 'today'), array(), array_merge($form->layoutColumns, array('field' => 2)))?>
	<?php $form->radioBoolean($element, 'fast_track') ?>
	<?php $form->radioButtons($element, 'fast_track_discussed_with_patient', array(1 => 'Yes', 0 => 'No'), null, false)?>
	<?php $form->radioBoolean($element, 'special_equipment') ?>
	<?php $form->textArea($element, 'special_equipment_details', array(), true, array(), array_merge($form->layoutColumns, array('field' => 4))) ?>
	<?php echo $form->textArea($element, 'comments', array('rows' => 4), false, array(), array_merge($form->layoutColumns, array('field' => 4)))?>
	<?php echo $form->textArea($element, 'comments_rtt', array('rows' => 4), false, array(), array_merge($form->layoutColumns, array('field' => 4)))?>
	<div class="row field-row">
		<div class="large-2 column">
			<label for="<?= CHtml::modelName($element).'[organising_admission_user_id]' ?>"><?= CHtml::encode($element->getAttributeLabel('organising_admission_user_id')) ?>:</label>
		</div>
		<div class="large-4 column end">
			<input type="hidden" name="<?php echo CHtml::modelName($element)?>[organising_admission_user_id]" id="<?php echo CHtml::modelName($element)?>_organising_admission_user_id" value="<?php echo $element->organising_admission_user_id?>" />
			<span class="organising_admission_user"><?php echo $element->organising_admission_user ? $element->organising_admission_user->reversedFullname.' (<a href="#" class="remove_organising_admission_user">remove</a>)' : 'None'?></span>
		</div>
	</div>
	<div class="row field-row">
		<div class="large-2 column">
			<label>&nbsp;</label>
		</div>
		<div class="large-4 column end">
			<?php
                $this->widget(
                    'zii.widgets.jui.CJuiAutoComplete',
                    array(
                        'id' => 'organising_admission_user_autocomplete',
                        'name' => 'organising_admission_user_autocomplete',
                        'value' => '',
                        'source' => $this->createUrl('/user/autoComplete'),
                        'htmlOptions' => array('placeholder' => 'search for doctors'),
                        'options' => array(
                            'select' => "js:function(e, ui) {
								$('#Element_OphTrOperationbooking_Operation_organising_admission_user_id').val(ui.item.id);
								$('.organising_admission_user').html(ui.item.value + ' (<a href=\"#\" class=\"remove_organising_admission_user\">remove</a>)');
								$('#organising_admission_user_autocomplete').val('');
								return false;
							}",
                        ),
                    )
                );
            ?>
		</div>
	</div>
</fieldset>
