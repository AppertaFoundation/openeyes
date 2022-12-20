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
<div class="cols-full">
    <table class="cols-full last-left">
        <colgroup>
            <col class="cols-6">
        </colgroup>
        <tbody>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('incision_site_id'); ?>
            </td>
            <td>
                <div class="cols-6">
                    <?php echo $form->dropDownList(
                        $element,
                        'incision_site_id',
                        'OphTrOperationnote_IncisionSite',
                        array(
                            'empty' => 'Select',
                            'textAttribute' => 'data-value',
                            'nolabel' => true,
                            'style' => 'width: 100%;',
                            'data-prefilled-value' => $template_data['incision_site_id'] ?? '',
                            'data-test' => 'incision-site'
                        ),
                        false,
                        array('field' => 12)
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('length'); ?>
            </td>
            <td>
                <div class="cols-6">
                    <?php echo $form->textField(
                        $element,
                        'length',
                        [
                            'nowrapper' => true,
                            'style' => 'width: 100%;',
                            'data-prefilled-value' => $template_data['length'] ?? '',
                            'data-test' => 'length'
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('meridian'); ?>
            </td>
            <td>
                <div class="cols-6">
                    <?php echo $form->textField(
                        $element,
                        'meridian',
                        [
                            'nowrapper' => true,
                            'style' => 'width:100%;',
                            'data-prefilled-value' => $template_data['meridian'] ?? '',
                            'data-test' => 'meridian'
                        ]
                    ) ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('incision_type_id'); ?>
            </td>
            <td>
                <div class="cols-6">
                    <?php echo $form->dropDownList(
                        $element,
                        'incision_type_id',
                        'OphTrOperationnote_IncisionType',
                        array(
                            'empty' => 'Select',
                            'textAttribute' => 'data-value',
                            'nolabel' => true,
                            'style' => 'width: 100%;',
                            'data-prefilled-value' => $template_data['incision_type_id'] ?? '',
                            'data-test' => 'incision-type'
                        ),
                        false,
                        array('field' => 12)
                    ) ?>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <?php echo $form->textArea(
                    $element,
                    'report',
                    [],
                    false,
                    [
                        'rows' => 6,
                        'readonly' => true,
                        'data-prefilled-value' => $template_data['report'] ?? ''
                    ]
                ) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo $form->textArea($element, 'comments', [], false, ['rows' => 1]) ?>
            </td>
        </tr>
        <tr id="tr_Element_OphTrOperationnote_Cataract_iol_type">
            <td>
                <?php echo $element->getAttributeLabel('iol_type_id'); ?>
            </td>
            <td>
                <div class="cols-6">
                    <?php
                    if (isset(Yii::app()->modules["OphInBiometry"])) : ?>
                        <?php echo $form->dropDownList(
                            $element,
                            'iol_type_id',
                            CHtml::listData(
                                OphInBiometry_LensType_Lens::model()->findAll(
                                    array(
                                        'condition' => (($element->iol_type_id > 0)
                                            ? '(t.active=1 or t.id=:iol_type_id)'
                                            : 't.active=1') . ' AND institutions_institutions.institution_id = :institution_id',
                                        'with' => 'institutions',
                                        'order' => 'display_name',
                                        'params' => ($element->iol_type_id > 0) ? array(
                                            ':iol_type_id' => $element->iol_type_id,
                                            ':institution_id' => Institution::model()->getCurrent()->id,
                                        ) : array(
                                            ':institution_id' => Institution::model()->getCurrent()->id,
                                        ),
                                    )
                                ),
                                'id',
                                'display_name'
                            ),
                            array(
                                'empty' => 'Select',
                                'nolabel' => true,
                                'data-prefilled-value' => $template_data['iol_type_id'] ?? '',
                                'data-test' => 'iol-type'
                            ),
                            $element->iol_hidden,
                            array('field' => 12)
                        ); ?>
                    <?php else : ?>
                        <?php echo $form->dropDownList(
                            $element,
                            'iol_type_id',
                            array(
                                CHtml::listData(
                                    OphTrOperationnote_IOLType::model()->activeOrPk($element->iol_type_id)->findAll(
                                        array(
                                        'condition' => 'private=0',
                                        'order' => 'display_order asc',
                                        )
                                    ),
                                    'id',
                                    'name'
                                ),
                                CHtml::listData(OphTrOperationnote_IOLType::model()->activeOrPk($element->iol_type_id)->findAll(array(
                                    'condition' => 'private=1',
                                    'order' => 'display_order',
                                )), 'id', 'name'),
                            ),
                            array(
                                'empty' => 'Select',
                                'divided' => true,
                                'nolabel' => true,
                                'data-prefilled-value' => $template_data['iol_type_id'] ?? '',
                                'data-test' => 'iol-type'
                            ),
                            $element->iol_hidden,
                            array('field' => 12)
                        ) ?>
                    <?php endif; ?>
                    <style>
                        #Element_OphTrOperationnote_Cataract_iol_type_id {
                            min-width: 100%;
                            max-width: 100%;
                        }
                    </style>
                </div>
            </td>
        </tr>
        <tr id="div_Element_OphTrOperationnote_Cataract_iol_power">
            <td>
                <label>IOL power</label>
            </td>
            <td>
                <?= \CHtml::activeTextField(
                    $element,
                    'iol_power',
                    [
                        'data-prefilled-value' => $template_data['iol_power'] ?? '',
                        'data-test' => 'iol-power'
                    ]
                ); ?>
            </td>
        </tr>
        <tr>
            <td class="flex-layout flex-left">
                <label for="Element_OphTrOperationnote_Cataract_predicted_refraction">Predicted refraction:</label>
            </td>
            <td>
                <?= \CHtml::activeTextField(
                    $element,
                    'predicted_refraction',
                    [
                        'data-prefilled-value' => $template_data['predicted_refraction'] ?? '',
                        'data-test' => 'predicted-refraction'
                    ]
                ); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $element->getAttributeLabel('iol_position_id'); ?>
            </td>
            <td>
                <div class="cols-6">
                    <?php echo $form->dropDownList(
                        $element,
                        'iol_position_id',
                        'OphTrOperationnote_IOLPosition',
                        array(
                            'empty' => 'Select',
                            'options' => array(
                                8 => array('disabled' => 'disabled'),
                            ),
                            'nolabel' => true,
                            'data-prefilled-value' => $template_data['iol_position_id'] ?? '',
                            'data-test' => 'iol-position'
                        ),
                        $element->iol_hidden,
                        array('field' => 12)
                    ) ?>
                    <style>
                        #Element_OphTrOperationnote_Cataract_iol_position_id {
                            min-width: 100%;
                            max-width: 100%
                        }
                    </style>
                </div>
            </td>
        </tr>
        <tr>
            <td>Agents</td>
            <td>
                <?php echo $form->multiSelectList(
                    $element,
                    'OphTrOperationnote_CataractOperativeDevices',
                    'operative_devices',
                    'id',
                    $this->getOperativeDeviceList($element),
                    $this->getOperativeDeviceDefaults(),
                    array(
                        'empty' => '- Agents -',
                        'label' => 'Agents',
                        'nowrapper' => true,
                        'options' => array('data-test' => 'agents')
                    ),
                    false,
                    false,
                    null,
                    false,
                    false,
                    array('field' => 12)
                ) ?>
            </td>
        </tr>
        <tr>
            <td>
                Phaco CDE:
            </td>
            <td>
                <?= \CHtml::activeTextField(
                    $element,
                    'phaco_cde',
                    ['data-prefilled-value' => $template_data['phaco_cde'] ?? '']
                );?>
                <i class="oe-i info small pad js-has-tooltip "
                   data-tooltip-content="Cumulative Dissipated Energy, in 'seconds'"></i>
            </td>
        </tr>
        <tr>
            <td>Complications</td>
            <td>
                <?php echo $form->multiSelectList(
                    $element,
                    'OphTrOperationnote_CataractComplications',
                    'complications',
                    'id',
                    CHtml::listData(
                        OphTrOperationnote_CataractComplications::model()->activeOrPk($element->cataractComplicationValues)->findAll(
                            array('order' => 'display_order asc')
                        ),
                        'id',
                        'name'
                    ),
                    null,
                    array('empty' => '- Complications -',
                        'label' => 'Complications',
                        'nowrapper' => true ,
                        'class' => $element->hasErrors('Complications') ? 'error' : '',
                        'data-test' => 'complications'),
                    false,
                    false,
                    null,
                    false,
                    false,
                    array('field' => 4)
                ) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php echo $form->textArea(
                    $element,
                    'complication_notes',
                    array('nowrapper' => true),
                    false,
                    array(
                        'rows' => 6,
                        'cols' => 40,
                        'placeholder' => $element->getAttributeLabel('complication_notes'),
                        'data-prefilled-value' => $template_data['complication_notes'] ?? ''
                    )
                ) ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<style>
    .Element_OphTrOperationnote_Cataract .multi-select-dropdown-container select {
        max-width: 50%;
        min-width: 50%;
    }

    #ophTrOperationnotePCRRiskDiv #div_Element_OphTrOperationnote_Cataract_pcr_risk {
        padding-left: 135px;
        padding-right: 50px;
    }

    #ophTrOperationnotePCRRiskDiv #div_Element_OphTrOperationnote_Cataract_pcr_risk #left_eye_pcr,
    #ophTrOperationnotePCRRiskDiv #div_Element_OphTrOperationnote_Cataract_pcr_risk #right_eye_pcr {
        width: 91.66667% !important;
    }
</style>

<?php echo $form->hiddenInput($element, 'pcr_risk') ?>
<script>
    $(document).ready(function () {
        autosize($('#Element_OphTrOperationnote_Cataract_comments'));
        autosize($('#Element_OphTrOperationnote_Cataract_complication_notes'));
        autosize($('#Element_OphTrOperationnote_Cataract_report'));

        $('#Element_OphTrOperationnote_Cataract_report').css('overflow', '');

        $("#Element_OphTrOperationnote_Cataract_iol_type_id option").each(function() {
            if ($(this).text() === '-') {
                $(this).hide();
            }
        });

        if (window.event_has_errors !== true) {
            setTimeout(() => {
                let $op_note_surgeon = $('#Element_OphTrOperationnote_Surgeon_surgeon_id');
                $op_note_surgeon.trigger('input');
            }, 500);

        }
    });
</script>