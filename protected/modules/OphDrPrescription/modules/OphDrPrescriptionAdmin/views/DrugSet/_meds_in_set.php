<?php
/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<h2>Medications in set</h2>
<div class="row flex-layout flex-top col-gap">
    <div class="cols-6">

        <input type="text"
               class="search cols-12"
               autocomplete=""
               name="search"
               id="search_query"
               placeholder="Search medication in set..."
               <?= !$medication_data_provider->totalItemCount ? 'style="display:none"' : ''?>
        >
        <small class="empty-set" <?= $medication_data_provider->totalItemCount ? 'style="display:none"' : ''?>>Empty set</small>

        <div class="alert-box success" style="display:none"><b>Success!</b> Medication added to the set.</div>
    </div>

    <div class="cols-6">
        <div class="flex-layout flex-right">
            <button class="button hint green" id="add-medication-btn" type="button"><i class="oe-i plus pro-theme"></i> Add medication</button>
        </div>
    </div>
</div>
<div class="row flex-layout flex-stretch flex-right">
    <div class="cols-12">
        <table id="meds-list" class="standard js-inline-edit" <!--id="rule_tbl"--> <?= !$medication_data_provider->totalItemCount ? 'style="display:none"' : ''?>
            <colgroup>
                <col class="cols-3">
                <col class="cols-1">
                <col class="cols-3">
                <col class="cols-3" style="width:20%">
            </colgroup>
            <thead>
                <tr>
                    <th>Preferred Term</th>
                    <th>Default dose</th>
                    <th>Default route</th>
                    <th>Default frequency</th>
                    <th>Default duration</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $route_options = \Chtml::listData(\MedicationRoute::model()->findAll(), 'id', 'term');
                $frequency_options = \Chtml::listData(\MedicationFrequency::model()->findAll(), 'id', 'term');
                $duration_options = \Chtml::listData(\MedicationDuration::model()->findAll(), 'id', 'name');
            ?>
            <?php foreach ($medication_data_provider->getData() as $k => $med) : ?>
                <?php $link = \MedicationSetItem::model()->findByAttributes(['medication_id' => $med->id, 'medication_set_id' => $medication_set->id]);?>
                <tr data-med_id="<?=$med->id?>">
                    <td>
                        <?= $med->preferred_term; ?>
                        <?= \CHtml::activeHiddenField($link, 'id', ['class' => 'js-input']); ?>
                        <?= \CHtml::activeHiddenField($med, 'id', ['class' => 'js-input']); ?>
                    </td>
                    <td>
                        <span data-type="default_dose" data-fk="<?= $link->default_dose ? $link->default_dose : ''; ?>" class="js-text"><?= $link->default_dose ? $link->default_dose : '-'; ?></span>
                        <?= \CHtml::activeTextField($link, 'default_dose', ['class' => 'js-input cols-full', 'style' => 'display:none']); ?>
                    </td>
                    <td>
                        <span data-type="default_route" data-fk="<?= $link->defaultRoute ? $link->default_route_id : ''; ?>" class="js-text"><?= $link->defaultRoute ? $link->defaultRoute->term : '-'; ?></span>
                        <?= \CHtml::activeDropDownList($link, 'default_route_id',
                            $route_options,
                            ['class' => 'js-input cols-full', 'style' => 'display:none']); ?>
                    </td>
                    <td>
                        <span data-type="default_frequency" data-fk="<?= $link->defaultFrequency ? $link->default_frequency_id : ''; ?>" class="js-text"><?= $link->defaultFrequency ? $link->defaultFrequency->term : '-'; ?></span>
                        <?= \CHtml::activeDropDownList($link, 'default_frequency_id',
                            $frequency_options,
                            ['class' => 'js-input cols-full', 'style' => 'display:none']); ?>
                    </td>
                    <td>
                        <span data-type="default_duration" data-fk="<?= $link->defaultDuration ? $link->default_duration_id : ''; ?>" class="js-text"><?= $link->defaultDuration ? $link->defaultDuration->name : '-'; ?></span>
                        <?= \CHtml::activeDropDownList($link, 'default_duration_id',
                            $duration_options,
                            ['class' => 'js-input', 'style' => 'display:none']); ?>
                    </td>

                    <td class="actions" style="text-align:center">
                        <a data-action_type="edit" class="js-edit-set-medication"><i class="oe-i pencil"></i></a>
                        <a data-action_type="delete" class="js-delete-set-medication"><i class="oe-i trash"></i></a>

                        <a data-action_type="save" class="js-tick-set-medication" style="display:none"><i class="oe-i tick-green"></i></a>
                        <a data-action_type="cancel" class="js-cross-set-medication" style="display:none"><i class="oe-i cross-red"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <td colspan="7">
                <?php $this->widget('LinkPager', ['pages' => $medication_data_provider->pagination]); ?>
            </td>
            </tfoot>
        </table>
    </div>
</div>

<script>
    new OpenEyes.UI.AdderDialog({
        openButton: $('#add-medication-btn'),
        onReturn: function (adderDialog, selectedItems) {
            const $table = $(drugSetController.options.tableSelector + ' tbody');

            selectedItems.forEach(item => {
                $.ajax({
                    'type': 'POST',
                    'data': {
                        set_id: $('#MedicationSet_id').val(),
                        medication_id: item.id,
                        YII_CSRF_TOKEN: YII_CSRF_TOKEN
                    },
                    'url': '/OphDrPrescription/admin/DrugSet/addMedicationToSet',
                    'dataType': 'json',
                    'beforeSend': function() {

                        if (!$('.oe-popup-wrap').length) {
                            // load spinner
                            let $overlay = $('<div>', {class: 'oe-popup-wrap'});
                            let $spinner = $('<div>', {class: 'spinner'});
                            $overlay.append($spinner);
                            $('body').prepend($overlay);
                        }

                    },
                    'success': function (resp) {
                    },
                    'error': function(resp) {
                        alert('Add medication to set FAILED. Please try again.');
                        console.error(resp);
                    },
                    'complete': function(resp) {
                        const result = JSON.parse(resp.responseText);
                        let callback;
                        if (result.success && result.success === true) {
                            callback = function() {
                                $('.alert-box.success').show();
                                $('.alert-box.success').fadeOut(3000);
                            }
                        }
                        $('.oe-popup-wrap').remove();
                        drugSetController.refreshResult(1, callback);
                    }
                });
            });
        },
        searchOptions: {
            searchSource: '/medicationManagement/findRefMedications',
        },
        enableCustomSearchEntries: true,
        searchAsTypedItemProperties: {id: "<?php echo EventMedicationUse::USER_MEDICATION_ID ?>"},
        booleanSearchFilterEnabled: true,
        booleanSearchFilterLabel: 'Include branded',
        booleanSearchFilterURLparam: 'include_branded'
    });

    $(document).ready(function(){
        $(drugSetController.options.tableSelector).on('click', '.js-delete-set-medication', function() {
            const $tr = $(this).closest('tr');
            const $a = $(this);
            const $trash = $a.find('.oe-i.trash');

            $.ajax({
                'type': 'POST',
                'data': {
                    set_id: $('#MedicationSet_id').val(),
                    medication_id: $a.data('med_id'),
                    YII_CSRF_TOKEN: YII_CSRF_TOKEN
                },
                'url': '/OphDrPrescription/admin/DrugSet/removeMedicationFromSet',
                'dataType': 'json',
                'beforeSend': function() {
                    $('.js-delete-set-medication').find('i').addClass('disabled');
                    $('.js-delete-set-medication').removeClass('js-delete-set-medication');

                    $trash.toggleClass('oe-i trash spinner as-icon');
                },
                'success': function (resp) {
                    if (resp.success === true) {
                        $a.replaceWith("<small style='color:red'>Removed</small>");
                        $tr.fadeOut(1000, function(){ $(this).remove(); });
                    }
                },
                'error': function(resp){
                    alert('Remove medication from set FAILED. Please try again.');
                    console.error(resp);
                },
                'complete': function(){
                    drugSetController.refreshResult();
                }
            });
        });
    });
</script>
