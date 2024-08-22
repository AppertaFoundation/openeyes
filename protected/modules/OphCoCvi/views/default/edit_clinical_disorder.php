<?php

/**
 * (C) Copyright Apperta Foundation 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCoCvi\models\OphCoCvi_ClinicalInfo_Disorder_Section;
?>

<div class="cols-9">

    <div class="row divider">
        <h2><?php echo $clinical_info_disorder->id ? 'Edit' : 'Add' ?> Clinical Disorder</h2>
    </div>
    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#username',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    ));

    // send default empty disorder_id if none selected
    echo \CHtml::hiddenField('OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder[disorder_id]', '');

    ?>

    <table class="standard">
        <colgroup>
            <col class="cols-1">
            <col class="cols-4">
        </colgroup>
        <tbody>
            <tr>
                <td>Patient type</td>
                <td>
                    <?=\CHtml::dropDownList('OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder[patient_type]', $clinical_info_disorder->patient_type, $patient_types, [
                        'empty' => '- Select -',
                        'data-test' => 'patient-type-dropdown',
                        'options' => [
                            0 => ['data-patient-type' => 0],
                            1 => ['data-patient-type' => 1],
                        ],
                    ])?>
                </td>
            </tr>
            <tr>
                <td><?=$clinical_info_disorder->getAttributeLabel('name')?></td>
                <td><?=CHtml::activeTextField($clinical_info_disorder, 'name', ['class' => 'cols-4', 'data-test' => 'name'])?></td>
            </tr>
            <tr>
                <td><?=$clinical_info_disorder->getAttributeLabel('code')?></td>
                <td><?=CHtml::activeTextField($clinical_info_disorder, 'code', ['class' => 'cols-4', 'data-test' => 'icd-10-code'])?></td>
            </tr>
            <tr>
                <td>Disorder:</td>
                <td>
                    <?php
                    $this->widget('application.widgets.AutoCompleteSearch',
                        [
                            'field_name' => 'autocomplete_disorder_id',
                            'htmlOptions' =>
                                [
                                    'placeholder' => 'search disorder',
                                    'data-test' => 'disorder-autocomplete'
                                ],
                            'layoutColumns' => ['field' => '4']
                        ]);
                    ?>

                    <ul class="oe-multi-select inline">
                        <?php if ($clinical_info_disorder->disorder_id):?>
                            <li>
                                <?=$clinical_info_disorder->disorder->term . " ({$clinical_info_disorder->disorder->id})";?><i class="oe-i remove-circle small-icon pad-left"></i>
                                <?=\CHtml::activeHiddenField($clinical_info_disorder, 'disorder_id');?>
                            </li>
                        <?php endif;?>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>Section:</td>
                <td>
                    <?php
                    $section_data = [];
                    foreach ($sections as $section) {
                        $section_data[$section->id] = ['data-patient-type' => $section->patient_type];
                    }

                    echo CHtml::dropDownList(
                        'OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder[section_id]',
                        $clinical_info_disorder->section_id,
                        CHtml::listData($sections, 'id', 'name'),
                        ['empty' => '- None -', 'class' => 'cols-4', 'data-test' => 'section-dropdown', 'options' => $section_data]
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td>Active:</td>
                <td><?php echo CHtml::activeCheckBox($clinical_info_disorder, 'active', ['data-test' => 'active-checkbox']) ?></td>
            </tr>
        </tbody>
    </table>

    <?php echo $form->formActions(); ?>
    <?php $this->endWidget() ?>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        const autocomplete_ul = document.querySelector('.oe-multi-select');

        OpenEyes.UI.AutoCompleteSearch.init({
            input: $('#autocomplete_disorder_id'),
            url: '/OphCoCvi/admin/CilinicalDisorderAutocomplete',
            params: {
            },
            maxHeight: '200px',
            onSelect: function() {
                const response = OpenEyes.UI.AutoCompleteSearch.getResponse();
                autocomplete_ul.innerHTML = '';
                const li = OpenEyes.UI.DOM.createElement('li');
                const icon = OpenEyes.UI.DOM.createElement('i', {"class": "oe-i remove-circle small-icon pad-left"});
                li.innerText = `${response.label} (${response.id})`;
                li.appendChild(icon);
                const hidden = OpenEyes.UI.DOM.createElement('input', {
                        "type": "hidden",
                        "id": "disorder_id",
                        "name": "OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder[disorder_id]",
                        "value": response.id
                    });

                li.appendChild(hidden);
                autocomplete_ul.appendChild(li);
            }
        });

        OpenEyes.UI.DOM.addEventListener(autocomplete_ul, 'click', '.remove-circle', function(e) {
            autocomplete_ul.innerHTML = '';
        });

        const section_dropdown = document.getElementById('OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder_section_id');
        function hideSectionOptionsByPatientType(patient_type, reset = false) {

            if (reset) {
                section_dropdown.selectedIndex = 0;
            }

            Array.from(section_dropdown.querySelectorAll('option')).forEach(option => {
                option.style.display = option.dataset.patientType === patient_type ? 'block' : "none";
            });
        }

        const patient_type_select = document.getElementById('OEModule_OphCoCvi_models_OphCoCvi_ClinicalInfo_Disorder_patient_type');
        hideSectionOptionsByPatientType(patient_type_select.value);

        OpenEyes.UI.DOM.addEventListener(patient_type_select, 'change', null, function(e) {
            const patient_type = e.target.options[e.target.options.selectedIndex].dataset.patientType;
            hideSectionOptionsByPatientType(patient_type, true);
        });

    });
</script>
