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

<nav class="oe-full-side-panel" id="vc-sidebar">
    <h3>Subspecialty, Context & Site</h3>
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

    <?php
        echo CHtml::dropDownList(
            'subspecialty-id',
            Yii::app()->request->getParam('subspecialty-id', null),
            Subspecialty::model()->getList(),
            [
                'empty' => 'All specialties',
                'class' => 'cols-full',
                'disabled' => (\Yii::app()->request->getParam('emergency_list') == 1 ? 'disabled' : ''),
            ]
        );

        echo CHtml::dropDownList(
            'firm-id',
            Yii::app()->request->getParam('firm-id'),
            Firm::model()->getList(Yii::app()->session['selected_institution_id'], \Yii::app()->request->getParam('subspecialty-id', null)),
            [
                'class' => 'cols-full',
                'empty' => 'All ' . Firm::contextLabel() . 's',
                'disabled' => (\Yii::app()->request->getParam('emergency_list', 0) == 1 ? 'disabled' : ''),
            ],
        );

        echo CHtml::dropDownList(
            'site-id',
            Yii::app()->request->getParam('site-id'),
            Site::model()->getListForCurrentInstitution(),
            [
                'class' => 'cols-full',
                'empty' => 'All sites',
                'disabled' => (\Yii::app()->request->getParam('emergency_list', 0) == 1 ? 'disabled' : ''),
            ]
        );
        ?>

    <hr class="divider">
    <h4>Lists</h4>
    <div class="js-multiselect-dropdown-wrapper">
        <?php
            $data = \CHtml::listData($qs_svc->getQueueSetQueues($queueset, false), 'id', 'name');
            $this->widget('application.widgets.MultiSelectDropDownList', [
                'options' => [
                    'dropDown' => [
                        'name' => null,
                        'id' => 'virtual-clinic-search-list',
                        'data' => $data,
                        'htmlOptions' => ['empty' => 'All Lists',],
                        'selectedItemsInputName' => 'queue-ids[]',
                        'selectedItems' => \Yii::app()->request->getParam('queue-ids', null),
                        'template' => "{DropDown}<div class='list-filters js-multiselect-dropdown-list-wrapper'>{List}</div>"
                    ],
                ],
            ]);
            ?>
    </div>

    <h4>Patients</h4>
    <?php $this->widget('application.widgets.AutoCompleteSearch', [
            'htmlOptions' => [
                    'placeholder' => 'Patient identifier, Firstname Surname or Surname, Firstname'
            ],
            'layoutColumns' => [
                    'field' => '11'
            ]
    ]); ?>
    <div class="list-filters">
        <ul id="patient-result-list" class="oe-multi-select">
            <?php foreach ($patients as $patient) : ?>
                <li data-patient_id="<?= $patient->id ?>">
                    <?="{$patient->first_name} {$patient->last_name}"?>
                    <i class="oe-i remove-circle small-icon pad-left"></i>
                    <input name="patient-ids[]" type="hidden" id="<?= "{$patient->id}"; ?>"
                           value="<?= "{$patient->id}"; ?>">
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <hr class="divider">
    <h4>Priority flags</h4>
    <fieldset>
        <?php $priorities = \Yii::app()->request->getParam('priority-ids', []); ?>
        <?= \CHtml::hiddenField('priority-ids[]', 0); ?>

        <?php foreach ([1 => 'red', 2 => 'amber', 3 => 'green'] as $num => $colour) :?>
            <label class="highlight inline">
                <?= \CHtml::checkBox('priority-ids[]', in_array($num, $priorities), ['value' => $num]); ?>
                <i class="oe-i circle-<?=$colour;?> small"></i>
            </label>
        <?php endforeach;?>
            <label class="inline highlight">
                <input type="hidden" value="0" name="closed-tickets">
                <?php $is_closed_tickets = \Yii::app()->request->getParam('closed-tickets', false);?>
                <?= \CHtml::checkBox('closed-tickets', $is_closed_tickets, ['value' => 1]); ?> Completed
            </label>
    </fieldset>

    <hr class="divider">
    <h4>Date Range</h4>
    <fieldset>
        <table class="standard">
            <colgroup>
                <col class="cols-5">
                <col class="cols-2">
                <col class="cols-5">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <input id="js-date-from-field"
                           name="date-from"
                           class="cols-full date"
                           placeholder="From"
                           value="<?=\Yii::app()->request->getParam('date-from');?>"
                           autocomplete="off"></td>
                <td style="text-align: center"> - </td>
                <td>
                    <input id="js-date-to-field"
                           name="date-to"
                           class="cols-full date"
                           placeholder="To"
                           value="<?=\Yii::app()->request->getParam('date-to'); ?>"
                           autocomplete="off">
                </td>
            </tr>
            </tbody>
        </table>
    </fieldset>
    <hr class="divider">
    <div class="button-stack">
        <button class="green hint">Search</button>
        <button id="reset-filters" >Reset all filters</button>
    </div>
    <?php $this->endWidget() ?>
</nav>

<script type="text/javascript">
    function correctDateRanges($fromField, $toField, favourFrom) {
        let fromTimestamp = Date.parse($fromField.val());
        let toTimestamp = Date.parse($toField.val());

        if (fromTimestamp > toTimestamp) {
            if (favourFrom) {
                $toField.val($fromField.val());
            } else {
                $fromField.val($toField.val());
            }
        }
    }

    $(document).ready(function () {
        let $fromField = $('#js-date-from-field');
        let $toField = $('#js-date-to-field');
        $fromField.on('change pickmeup-change', function () {
            correctDateRanges($fromField, $toField, true);
        });
        $toField.on('change pickmeup-change', function () {
            correctDateRanges($fromField, $toField, false);
        });

        pickmeup('#js-date-from-field', {
            format: 'd b Y',
            default_date: false,
        });
        pickmeup('#js-date-to-field', {
            format: 'd b Y',
            default_date: false,
        });

        const $ul = document.getElementById('patient-result-list');
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
                    let response = OpenEyes.UI.AutoCompleteSearch.getResponse();

                    const $li = document.createElement('li');
                    $li.innerHTML = `${response.last_name.toUpperCase()}, ${response.first_name}<i class="oe-i remove-circle small-icon pad-left"></i>`;

                    let $hidden = OpenEyes.UI.DOM.createElement('input', {
                        type: 'hidden',
                        id: response.id,
                        value: response.id,
                        name: 'patient-ids[]'
                    });
                    $ul.innerHTML = '';

                    $li.append($hidden);
                    $ul.append($li);
                    // clear input field
                    $(this).val('');
                    return false;
                }
            });
        }

        OpenEyes.UI.DOM.addEventListener($ul, 'click', '.remove-circle', function(e) {
            e.target.closest('li').remove();
        });

        const $reset_button = document.getElementById('reset-filters');
        OpenEyes.UI.DOM.addEventListener($reset_button, 'click', null, function(e) {
            e.preventDefault();
            const $form = document.getElementById('ticket-filter');
            const cat_id = document.getElementById('cat_id').value;
            const queueset_id = document.getElementById('queueset_id').value;
            window.location = $form.getAttribute('action') + `&cat_id=${cat_id}&queueset_id=${queueset_id}`;
        });
    });
</script>
