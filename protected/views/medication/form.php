<?php

/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

$form = $this->beginWidget('FormLayout', array('layoutColumns' => array('label' => 3, 'field' => 9)));

?>
    <fieldset class="data-group">

        <legend><strong><?= $medication->isNewRecord ? 'Add' : 'Edit' ?> medication</strong></legend>

        <input type="hidden" name="medication_id" id="medication_id" value="<?= $medication->id ?>"/>
        <input type="hidden" name="patient_id" id="medication_id" value="<?= $patient->id ?>"/>
        <input type="hidden" name="prescription_item_id" id="prescription_item_id"
               value="<?= $medication->prescription_item_id ?>"/>

        <div class="data-group">
            <div class="<?= $form->columns('label'); ?>">
                <label for="drug_id">Medication:</label>
            </div>
            <div class="<?= $form->columns('field'); ?>">

                <input type="hidden" name="drug_id" value="<?= $medication->drug_id ?>"/>
                <input type="hidden" name="medication_drug_id" value="<?= $medication->medication_drug_id ?>"/>

                <div class="data-value"
                     id="medication_drug_name"><?= CHtml::encode($medication->getDrugLabel()) ?></div>

                <div class="data-group">
                    <?= CHtml::dropDownList(
                        'drug_select',
                        '',
                        Drug::model()->listBySubspecialtyWithCommonMedications($firm->getSubspecialtyID()),
                        array('empty' => '- Select -')
                    ) ?>
                </div>

                <div class="data-group">
                    <div class="label"></div>
                    <?php
                    $this->widget(
                        'zii.widgets.jui.CJuiAutoComplete',
                        array(
                            'name' => 'drug_autocomplete',
                            'source' => new CJavaScriptExpression(
                                'function (req, res) { $.getJSON(' . json_encode($this->createUrl('medication/finddrug')) . ', req, res); }'
                            ),
                            'options' => array(
                                'minLength' => 3,
                                'focus' => "js:function(e,ui) {
                                $('#drug_autocomplete').val(ui.item.label);
                                e.preventDefault();
                            }",
                            ),
                            'htmlOptions' => array('placeholder' => 'or search'),
                        )
                    );
                    ?>
                </div>
            </div>
        </div>

        <?php $form->widget(
            'application.widgets.TextField',
            array('element' => $medication, 'field' => 'dose', 'name' => 'dose')
        ); ?>

        <?php $form->widget(
            'application.widgets.DropDownList',
            array(
                'element' => $medication,
                'field' => 'route_id',
                'data' => 'DrugRoute',
                'htmlOptions' => array('name' => 'route_id', 'empty' => '- Select -'),
            )
        ); ?>

        <div id="medication_route_option">
            <?php if ($medication->route) {
                $this->renderPartial('route_option', array('medication' => $medication, 'route' => $medication->route));
            } ?>
        </div>

        <?php $form->widget(
            'application.widgets.DropDownList',
            array(
                'element' => $medication,
                'field' => 'frequency_id',
                'data' => 'DrugFrequency',
                'htmlOptions' => array('name' => 'frequency_id', 'empty' => '- Select -'),
            )
        ); ?>

        <input type="hidden" name="start_date">
        <?php
        $medication_start_date = null;
        if ($medication->start_date) {
            $medication_start_date = $medication->start_date;
        } elseif (( null !== SettingMetadata::model()->getSetting('enable_concise_med_history')) && SettingMetadata::model()->getSetting('enable_concise_med_history')) {
            $medication_start_date = date('Y-m-d');
        }
        $this->renderPartial(
            '/patient/_fuzzy_date',
            array(
                'form' => $form,
                'date' => $medication_start_date,
                'class' => 'medication_start_date',
                'label' => 'Date from',
            )
        ); ?>

        <div class="data-group">
            <div class="<?= $form->columns('label') ?>"><label for="current">Current:</label></div>
            <div class="<?= $form->columns('field') ?>">
                <label
                    class="inline"><?= CHtml::radioButton('current', !$medication->end_date, array('value' => true)) ?>
                    Yes</label>
                <label
                    class="inline"><?= CHtml::radioButton('current', $medication->end_date, array('value' => false)) ?>
                    No</label>
                <?php if ((null === SettingMetadata::model()->getSetting('enable_concise_med_history')) || !SettingMetadata::model()->getSetting('enable_concise_med_history')) { ?>
                    <button id="medication_from_today" type="button" class="tiny right">From today</button>
                <?php } ?>
            </div>
        </div>

        <div id="medication_end" class="<?= $medication->end_date ? '' : 'hidden' ?>">
            <input type="hidden" name="end_date">
            <?php
            $medication_end_date = null;
            if ($medication->end_date) {
                $medication_end_date = $medication->end_date;
            } elseif (( null !== SettingMetadata::model()->getSetting('enable_concise_med_history')) && SettingMetadata::model()->getSetting('enable_concise_med_history')) {
                $medication_end_date = date('Y-m-d');
            }
            $this->renderPartial(
                '/patient/_fuzzy_date',
                array(
                    'form' => $form,
                    'date' => $medication_end_date,
                    'class' => 'medication_end_date',
                    'label' => 'Date to',
                )
            );
            $this->renderPartial('stop_reason', array('form' => $form, 'medication' => $medication));

            ?>
        </div>

        <div id="medication_form_errors" class="alert-box alert hide"></div>

        <div class="buttons">
            <button type="button" class="medication_save secondary small">Save</button>
            <button type="button" class="medication_cancel warning small">Cancel</button>
        </div>
    </fieldset>
<?php

$this->endWidget();
