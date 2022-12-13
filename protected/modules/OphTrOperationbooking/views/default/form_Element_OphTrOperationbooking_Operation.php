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

$event_errors = OphTrOperationbooking_BookingHelper::validateElementsForEvent($this->open_elements);
$procedure_readonly = $this->procedure_readonly;
?>

<div class="element-fields full-width">
    <div class="flex-layout">
        <div class="cols-3">
            <?php
            /**
             * Check if opbooking_disable_both_eyes is true.  Used to remove BOTH eyes option for Cataract operations. Default is true, but it can be overridden for different subspecialties.
             */
            $episode = $this->patient->getEpisodeForCurrentSubspecialty();

            if (SettingMetadata::model()->getSetting('opbooking_disable_both_eyes') == 1) {
                echo $form->radioButtons(
                    $element,
                    'eye_id',
                    CHtml::listData(Eye::model()->findAll(array(
                        'condition' => 'name != "Both"',
                        'order' => 'display_order asc',
                    )), 'id', 'name'),
                    $element->eye_id,
                    '',
                    '',
                    '',
                    '',
                    array(
                        'nowrapper' => true,
                        'extra_fieldset_attributes' => [
                            'data-test' => 'procedure-side',
                        ],
                        'label-class' => $event_errors ? 'error' : '',
                    ),
                    array()
                );
            } else {
                echo $form->radioButtons(
                    $element,
                    'eye_id',
                    CHtml::listData(Eye::model()->findAll(array('order' => 'display_order asc')), 'id', 'name'),
                    null,
                    '',
                    '',
                    '',
                    '',
                    array(
                        'extra_fieldset_attributes' => [
                            'data-test' => 'anaesthetic-type',
                        ],
                        'label-class' => $event_errors ? 'error' : ''
                    ),
                    array()
                );
            }
            ?>
        </div>
        <div class="cols-2">
            Procedure(s):
        </div>
        <div class="cols-7">
            <?php $form->widget('application.widgets.ProcedureSelection', array(
                'element' => $element,
                'durations' => true,
                'label' => '',
                'complexity' => $element->complexity
            )) ?>
        </div>
    </div>
    <hr class="divider">
    <div class="flex-layout flex-top col-gap data-group">
        <div class="cols-half">
            <table class="cols-full last-left">
                <tbody>
                    <tr>
                        <td>
                            Complexity:
                        </td>
                        <td>
                            <?php echo $form->radioButtons(
                                $element,
                                'complexity',
                                Element_OphTrOperationbooking_Operation::$complexity_captions,
                                null,
                                false,
                                false,
                                false,
                                false,
                                array(
                                    'nowrapper' => true,
                                )
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Named consultant list:
                        </td>
                        <td>
                            <?php echo $form->radioBoolean($element, 'consultant_required', array(
                                'nowrapper' => true
                            )) ?>
                            <?php echo $form->dropDownList(
                                $element,
                                'named_consultant_id',
                                CHtml::listData(
                                    User::model()->findAll(array(
                                        'condition' => 'is_consultant = 1 and is_surgeon=1',
                                        'order' => 'last_name, first_name',
                                    )),
                                    'id',
                                    'reversedFullName'
                                ),
                                array('empty' => 'Select'),
                                false,
                                array('field' => 5)
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Site:
                        </td>
                        <td>
                            <?php
                            $options = array(
                                $this->selectedSiteId => array('selected' => true),
                            );
                            echo $form->dropDownList(
                                $element,
                                'site_id',
                                CHtml::listData(OphTrOperationbooking_Operation_Theatre::getSiteList(), 'id', 'short_name'),
                                array('empty' => '- None -', 'options' => $options, 'nowrapper' => true),
                                false,
                                array('field' => 2)
                            );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Priority
                        </td>
                        <td>
                            <?php echo $form->radioButtons(
                                $element,
                                'priority_id',
                                CHtml::listData(
                                    OphTrOperationbooking_Operation_Priority::model()->notDeletedOrPk($element->priority_id)->findAll(array('order' => 'display_order asc')),
                                    'id',
                                    'name'
                                ),
                                null,
                                false,
                                false,
                                false,
                                false,
                                array('nowrapper' => true)
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Decision date
                        </td>
                        <td>
                            <?php echo $form->datePicker(
                                $element,
                                'decision_date',
                                array('maxDate' => 'today'),
                                array('nowrapper' => true),
                                array_merge($form->layoutColumns, array('field' => 2))
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Special equipment required
                        </td>
                        <td>
                            <?php $form->radioBoolean($element, 'special_equipment', array('nowrapper' => true)) ?>
                            <?php $form->textArea(
                                $element,
                                'special_equipment_details',
                                array('rows' => 1),
                                true,
                                array(),
                                array_merge($form->layoutColumns, array('label' => 6, 'field' => 12))
                            ) ?>
                        </td>
                    </tr>
                    <?php
                    if (Yii::app()->params['ophtroperationbooking_referral_link']) {
                        ?>
                        <tr>
                            <div class="data-groupw">
                                <?php if ($element->canChangeReferral()) { ?>
                                    <td>
                                        <label for="Element_OphTrOperationbooking_Operation_referral_id"><?= $element->getAttributeLabel('referral_id'); ?></label>
                                    </td>
                                    <td>
                                        <?php
                                        $html_options = array(
                                            'options' => array(),
                                            'empty' => '- No valid referral available -',
                                            'nowrapper' => true,
                                        );
                                        $choices = $this->getReferralChoices();
                                        foreach ($choices as $choice) {
                                            if ($active_rtt = $choice->getActiveRTT()) {
                                                if (count($active_rtt) == 1) {
                                                    $html_options['options'][(string)$choice->id] = array(
                                                        'data-clock-start' => Helper::convertDate2NHS($active_rtt[0]->clock_start),
                                                        'data-breach' => Helper::convertDate2NHS($active_rtt[0]->breach),
                                                    );
                                                }
                                            }
                                        }
                                        echo $form->dropDownList(
                                            $element,
                                            'referral_id',
                                            CHtml::listData($this->getReferralChoices(), 'id', 'description'),
                                            $html_options,
                                            false,
                                            array('field' => 2)
                                        );
                                        ?>
                                    </td>
                                    <td>
                                        <span id="rtt-info" class="rtt-info" style="display: none">Clock start - <span id="rtt-clock-start"></span> Breach - <span id="rtt-breach"></span></span>
                                    </td>
                                    <?php
                                } else { ?>
                                    <td><label>Referral:</label></td>
                                    <td>
                                        <?php if ($element->referral) {
                                            echo $element->referral->getDescription();
                                        } else {
                                            echo 'No Referral Set';
                                        } ?>
                                    </td>
                                <?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="cols-half">
            <table class="cols-full last-left">
                <tbody>
                    <tr>
                        <td>
                            Anaesthetic Type
                        </td>
                        <td>
                            <?php echo $form->checkBoxes(
                                $element,
                                'AnaestheticType',
                                'anaesthetic_type',
                                null,
                                false,
                                false,
                                false,
                                false,
                                array(
                                    'fieldset-class' => $element->getError('anaesthetic_type') ? 'highlighted-error error' : '',
                                    'field' => 'AnaestheticType',
                                )
                            ); ?>
                        </td>
                    </tr>
                    <?php if (!$this->module->isLACDisabled()) {?>
                        <tr>
                            <td>
                                Cover
                            </td>
                            <td>
                                <?= CHtml::openTag('label', ['class' => 'inline highlight']); ?>
                                    <?= CHTML::activeCheckBox($element, 'is_lac_required'); ?>
                                    <span class="in-txt">Anaesthetist cover required</span>
                                <?= CHtml::closeTag('label'); ?>
                            </td>
                        </tr>
                    <?php }?>
                    <tr>
                        <td>
                            Anaesthetic choice is
                        </td>
                        <td>
                            <?php $form->radioButtons(
                                $element,
                                'anaesthetic_choice_id',
                                'OphTrOperationbooking_Anaesthetic_Choice',
                                null,
                                false,
                                false,
                                false,
                                false,
                                array('nowrapper' => true)
                            ) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Patient needs to stop medication
                        </td>
                        <td>
                            <?php $form->radioBoolean($element, 'stop_medication', array('nowrapper' => true)) ?>
                            <?php $form->textArea(
                                $element,
                                'stop_medication_details',
                                array('rows' => 1),
                                true,
                                array(),
                                array_merge($form->layoutColumns, array('label' => 6, 'field' => 12))
                            ) ?>
                        </td>
                    </tr>
                    <tr id='tr_stop_medication_details' style="display:none">
                        <td>
                            <?php echo $element->getAttributeLabel('stop_medication_details') ?>
                        </td>
                        <td>
                            <?php $form->textArea(
                                $element,
                                'stop_medication_details',
                                array(
                                    'rows' => 1, 'label' => false,
                                    'nowrapper' => true
                                ),
                                true,
                                array(
                                    'class' => 'autosize',
                                    'class' => $element->getError('stop_medication_details') ? 'error' : ''
                                )
                            ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Overnight Stay required
                        </td>
                        <td>
                            <?php echo $form->radioButtons(
                                $element,
                                'overnight_stay_required_id',
                                'OphTrOperationbooking_Overnight_Stay_Required',
                                null,
                                false,
                                false,
                                false,
                                false,
                                array('nowrapper' => true)
                            );
?>
                        </td>
                    </tr>
                    <?php if (!$this->module->isTheatreDiaryDisabled() && !$this->module->isGoldenPatientDisabled()) : ?>
                        <tr>
                            <td><?= $element->getAttributeLabel('is_golden_patient'); ?> </td>
                            <td>
                                <?= CHtml::openTag('label', ['class' => 'inline highlight']); ?>
                                <?= CHTML::activeCheckBox($element, 'is_golden_patient'); ?>
                                <?= CHtml::closeTag('label'); ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <table class="cols-11">
        <tbody>
            <tr>
                <td>
                    Add Comments:
                </td>
                <td>
                    <?php echo $form->textArea($element, 'comments', array('rows' => 1, 'nowrapper' => true), '', array('placeholder' =>
                    'Scheduling guidance for admissions team', 'class' => 'cols-full autosize')) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Add RTT comments:
                </td>
                <td>
                    <?php echo $form->textArea(
                        $element,
                        'comments_rtt',
                        array('rows' => 1, 'nowrapper' => true),
                        '',
                        array('class' => 'autosize'),
                        array_merge($form->layoutColumns, array('field' => 4))
                    ) ?>
                </td>
            </tr>
            <tr>
                <td>
                    Doctor organising admission
                </td>
                <td>
                    <input type="hidden" name="<?= \CHtml::modelName($element) ?>[organising_admission_user_id]" id="<?= \CHtml::modelName($element) ?>_organising_admission_user_id" value="<?php echo $element->organising_admission_user_id ?>" />
                    <span class="organising_admission_user">
                        <?php echo $element->organising_admission_user ? $element->organising_admission_user->getReversedFullname() . ' <i href="#" class="remove_organising_admission_user oe-i remove-circle small pad-left"></i>' : 'None' ?>
                    </span>
                </td>
                <td>
                    <?php $this->widget('application.widgets.AutoCompleteSearch'); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>