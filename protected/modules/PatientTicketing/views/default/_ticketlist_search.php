<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var int $cat_id
 * @var \OEModule\PatientTicketing\services\PatientTicketing_QueueSet $queueset
 */
?>

<?php $this->beginWidget('CActiveForm', array(
    'id' => 'ticket-filter',
    'method' => 'get',
    'action' => [
        '/PatientTicketing/default',
        'cat_id' => $cat_id,
        'queueset_id' => $queueset->getId(),
    ],
    'htmlOptions' => array(
        'class' => 'data-group',
    ),
    'enableAjaxValidation' => false,
)); ?>


<nav class="oe-virtual-clinic-search">

    <table class="standard" style="margin-bottom: 0px">
        <colgroup>
            <col class="cols-1">
            <col class="cols-4">
            <col class="cols-1">
            <col class="cols-3">
            <col class="cols-3">
        </colgroup>

        <tbody>
        <tr class="col-gap">
            <?php
            $data = \CHtml::listData($qs_svc->getQueueSetQueues($queueset, false), 'id', 'name');
            $this->widget('application.widgets.MultiSelectDropDownList', [
                'options' => [
                    'label' => 'Lists:',
                    'dropDown' => [
                        'name' => null,
                        'id' => 'virtual-clinic-search-list',
                        'data' => $data,
                        'htmlOptions' => ['empty' => 'All Lists',],
                        'selectedItemsInputName' => 'queue-ids[]',
                        'selectedItems' => \Yii::app()->request->getParam('queue-ids', null),
                    ],
                ],
            ]);
            ?>

            <?php

            if ($queueset->filter_subspecialty) : ?>
                <td class="fade">Subspecialty:</td>
                <td>
                    <?= \CHtml::dropDownList(
                        'subspecialty-id',
                        \Yii::app()->request->getParam('subspecialty-id', null),
                        Subspecialty::model()->getList(),
                        [
                            'empty' => 'All specialties',
                            'class' => 'cols-11',
                            'disabled' => (\Yii::app()->request->getParam('emergency_list') == 1 ? 'disabled' : ''),
                        ]
                    );
                    ?>
                </td>
            <?php endif; ?>

            <td colspan="2">
                <?php $priorities = \Yii::app()->request->getParam('priority-ids', []); ?>
                <?= \CHtml::hiddenField('priority-ids[]', 0); ?>
                <label class="inline highlight">
                    <?= \CHtml::checkBox('priority-ids[]', in_array(1, $priorities), ['value' => 1]); ?>
                    <i class="oe-i circle-red small pad"></i>
                </label>
                <label class="inline highlight">
                    <?= \CHtml::checkBox('priority-ids[]', in_array(2, $priorities), ['value' => 2]); ?>
                    <i class="oe-i circle-amber small pad"></i>
                </label>
                <label class="inline highlight">
                    <?= \CHtml::checkBox('priority-ids[]', in_array(3, $priorities), ['value' => 3]); ?>
                    <i class="oe-i circle-green small pad"></i>
                </label>
                <small>
                    <label class="inline highlight">
                        <input type="hidden" value="0" name="closed-tickets">
                        <?= \CHtml::checkBox(
                            'closed-tickets',
                            \Yii::app()->request->getParam('closed-tickets', false),
                            ['value' => 1]
                        ); ?>
                        Completed
                    </label></small>
            </td>
        </tr>
        <tr class="col-gap">
            <td class="fade">Patients:</td>
            <td id="patient-search-wrapper">
                <?php $this->widget('application.widgets.AutoCompleteSearch', [
                        'htmlOptions' => [
                                'placeholder' => 'Patient identifier, Firstname Surname or Surname, Firstname'
                        ],
                        'layoutColumns' => [
                                'field' => '11'
                        ]
                ]); ?>
                <div style="display:inline-block">
                    <div class="js-spinner-as-icon loader" style="display: none;"><i class="spinner as-icon"></i></div>
                </div>
                <div style="display:none" class="cols-11 no-result-patients warning alert-box">
                    <div class="cols-11 column text-center">
                        No patients found in virtual clinic.
                    </div>
                </div>
            </td>
            <td class="fade">Context</td>
            <td>
                <?php if (!$subspecialty_id = \Yii::app()->request->getParam('subspecialty-id', null)) { ?>
                    <?= \CHtml::dropDownList('firm-id', '', array(), array(
                        'class' => 'cols-11',
                        'empty' => 'All ' . Firm::contextLabel() . 's',
                        'disabled' => 'disabled',
                    )) ?>
                <?php } else { ?>
                    <?= \CHtml::dropDownList(
                        'firm-id',
                        \Yii::app()->request->getParam('firm-id'),
                        Firm::model()->getList(Yii::app()->session['selected_institution_id'], $subspecialty_id),
                        array(
                            'class' => 'cols-11',
                            'empty' => 'All ' . Firm::contextLabel() . 's',
                            'disabled' => (\Yii::app()->request->getParam('emergency_list', 0) == 1 ? 'disabled' : ''),
                        )
                    ) ?>
                <?php } ?>
            </td>
            <td colspan="2">
                <button class="green hint cols-11">Update Search</button>
            </td>
        </tr>

        </tbody>
    </table>
    <table class="standard" style="margin-top:0px">
        <colgroup>
            <col class="cols-1">
            <col class="cols-4">
            <col class="cols-7">
        </colgroup>
        <tr class="col-gap">
            <td></td>
            <td style="padding-top:0px" id="patient-result-wrapper">
                <ul id="patient-result-list" class="oe-multi-select inline">
                    <?php foreach ($patients as $patient) : ?>
                        <li data-patient_id="<?= $patient->id ?>">
                            <?="{$patient->first_name} {$patient->last_name}"?>
                            <i class="oe-i remove-circle small-icon pad-left"></i>
                            <input name="patient-ids[]" type="hidden" id="<?= "{$patient->id}"; ?>"
                                   value="<?= "{$patient->id}"; ?>">
                        </li>
                    <?php endforeach; ?>
                </ul>

            </td>
            <td></td>
        </tr>
    </table>
</nav>

<?php $this->endWidget() ?>
<script type="text/javascript">

    $(document).ready(function () {
        if (OpenEyes.UI.AutoCompleteSearch !== undefined) {
            OpenEyes.UI.AutoCompleteSearch.init({
                input: $('#oe-autocompletesearch'),
                url: '/PatientTicketing/default/patientSearch',
                params: {
                    closedTickets: function () {
                        return +$('#closed-tickets').is(':checked')
                    }
                },
                onSelect: function () {
                    let autoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
                    let $list = $('#patient-result-list');
                    let $item = $('<li>', {'data-patient-id': autoCompleteResponse.id}).html(autoCompleteResponse.label + '<i class="oe-i remove-circle small-icon pad-left"></i>');
                    let $hidden = $('<input>', {
                        type: 'hidden',
                        id: autoCompleteResponse.id,
                        value: autoCompleteResponse.id,
                        name: 'patient-ids[]'
                    });
                    $list.html('');
                    $list.append($item.append($hidden));
                    // clear input field
                    $(this).val('');
                    return false;
                }
            });
        }


        $('#patient-result-wrapper').on('click', '.remove-circle', function () {
            let id = $(this).data('patient_id');
            $('#' + id).remove();
            $(this).closest('li').remove();
        });
    });
</script>
