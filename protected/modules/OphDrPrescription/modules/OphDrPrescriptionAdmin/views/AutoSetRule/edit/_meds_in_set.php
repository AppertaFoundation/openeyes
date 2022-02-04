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
<?php
$is_prescription_set = $medication_set->hasUsageCode("PRESCRIPTION_SET");
$default_dispense_location = \CHtml::listData(\OphDrPrescription_DispenseLocation::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION), 'id', 'name');
$default_dispense_condition = \CHtml::listData(\OphDrPrescription_DispenseCondition::model()->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION), 'id', 'name');

// FP10 settings
$fpten_setting = SettingMetadata::model()->getSetting('prescription_form_format');
$overprint_setting = SettingMetadata::model()->getSetting('enable_prescription_overprint');
$fpten_dispense_condition = OphDrPrescription_DispenseCondition::model()->findByAttributes(array('name' => 'Print to {form_type}'));

$dispense_condition_options = array(
    $fpten_dispense_condition->id => array('label' => "Print to $fpten_setting")
);
// End of FP10 settings
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
    </div>

    <div class="cols-6">
        <div class="flex-layout flex-right">
            <button class="button hint green" id="add-medication-btn" type="button"><i class="oe-i plus pro-theme"></i> Add medication</button>
        </div>
    </div>
</div>
<div class="row flex-layout flex-stretch flex-right">
    <div class="cols-12">
        <table id="meds-list" class="standard last-right js-inline-edit" <?= !$medication_data_provider->totalItemCount ? 'style="display:none"' : ''?>>
            <thead>
                <tr>
                    <th>Preferred Term</th>
                    <th>Default dose</th>
                    <th>Default Unit</th>
                    <th>Default Route</th>
                    <th>Default frequency</th>
                    <th>Default duration</th>
                    <th>Default Dispense Condition</th>
                    <th>Default Dispense Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $route_options = \CHtml::listData(\MedicationRoute::model()->findAll([
                        'condition' => 'source_type =:source_type',
                        'params' => [':source_type' => 'DM+D'],
                        'order' => "term ASC"]), 'id', 'term');
                $frequency_options = \CHtml::listData(\MedicationFrequency::model()->findAll(), 'id', 'term');
                $duration_options = \CHtml::listData(\MedicationDuration::model()->findAll(), 'id', 'name');
                ?>
            <?php foreach ($medication_data_provider->getData() as $k => $med) : ?>
                    <?php
                    if (isset($med->defaultDispenseCondition)) {
                        $dispense_condition_institution = OphDrPrescription_DispenseCondition_Institution::model()->findByAttributes(['dispense_condition_id' => $med->defaultDispenseCondition->id, 'institution_id' => Yii::app()->session['selected_institution_id']]);
                        $med_dispense_locations = CHtml::listData(array_map(
                            static function ($item) {
                                return $item->dispense_location;
                            },
                            $dispense_condition_institution->dispense_location_institutions
                        ), 'id', 'name');
                    } else {
                        $med_dispense_locations = $default_dispense_location;
                    }
                    ?>
                    <tr class="js-row-of-<?=$med->id?>" data-id="<?=$med->id?>" data-med_id="<?=$med->id?>" data-key="<?=$k;?>">
                        <td>
                            <input type="hidden" name="set_id" class="js-input js-medication-set-id" value="<?=$medication_set->id;?>">

                            <?= (isset($med->preferred_term) ? $med->preferred_term : $med->medication->getLabel(true)); ?>
                            <?= \CHtml::activeHiddenField($med, 'id', ['class' => 'js-input', 'name' => "MedicationSetAutoRuleMedication[$k][id]"]); ?>
                            <?= \CHtml::activeHiddenField($med, 'medication_id', ['class' => 'js-input', 'name' => "MedicationSetAutoRuleMedication[$k][medication_id]"]); ?>
                        </td>
                        <td class="js-input-wrapper">
                            <?= \CHtml::activeTextField(
                                $med,
                                'default_dose',
                                [
                                    'class' => 'js-input cols-full',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_dose]"
                                ]
                            ); ?>
                        </td>
                        <td>
                            <input type="hidden" name="MedicationSetAutoRuleMedication[<?= $k ?>][default_dose_unit_term]" value="<?= $med->default_dose_unit_term ? $med->default_dose_unit_term : ''; ?>">
                            <span data-type="default_dose" data-id="<?= $med->default_dose_unit_term ? $med->default_dose_unit_term : ''; ?>"><?= $med->default_dose_unit_term ? $med->default_dose_unit_term : '-'; ?></span>
                        </td>
                        <td class="js-input-wrapper">
                            <?= \CHtml::activeDropDownList(
                                $med,
                                'default_route_id',
                                $route_options,
                                [
                                    'class' => 'js-input cols-full',
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_route_id]"
                                ]
                            ); ?>
                        </td>
                        <td class="js-input-wrapper">
                            <?= \CHtml::activeDropDownList(
                                $med,
                                'default_frequency_id',
                                $frequency_options,
                                [
                                    'class' => 'js-input cols-full',
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_frequency_id]"
                                ]
                            ); ?>
                        </td>
                        <td class="js-input-wrapper">

                            <?= \CHtml::activeDropDownList(
                                $med,
                                'default_duration_id',
                                $duration_options,
                                [
                                    'class' => 'js-input cols-full',
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_duration_id]"
                                ]
                            ); ?>
                        </td>

                        <td class="js-input-wrapper">
                            <div class="js-prescription-extra js-prescription-dispense-condition">
                            <?= \CHtml::activeDropDownList(
                                $med,
                                'default_dispense_condition_id',
                                CHtml::listData(
                                    OphDrPrescription_DispenseCondition::model()->withSettings($overprint_setting, $fpten_dispense_condition->id)->findAllAtLevel(ReferenceData::LEVEL_INSTITUTION),
                                    'id',
                                    'name'
                                ),
                                [
                                    'disabled' => !$is_prescription_set,
                                    'class' => 'js-input cols-full dispense-condition',
                                    'style' => ($is_prescription_set ? '' : 'background: lightgray; color:gray'),
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_dispense_condition_id]",
                                    'options' => $dispense_condition_options
                                ]
                            ); ?>
                            </div>
                        </td>
                        <td class="js-input-wrapper" >
                            <div class="js-prescription-extra js-prescription-dispense-location">
                            <?= \CHtml::activeDropDownList(
                                $med,
                                'default_dispense_location_id',
                                $med_dispense_locations,
                                [
                                    'disabled' => !$is_prescription_set,
                                    'class' => 'js-input cols-full dispense-location',
                                    'empty' => '-- select --',
                                    'style' => ($is_prescription_set ? '' : 'background: lightgray; color:gray'),
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_dispense_location_id]"
                                ]
                            ); ?>
                            </div>
                        </td>

                        <td class="actions" style="text-align:center">
                            <button class="js-add-taper" data-action_type="add-taper" type="button" title="Add taper">
                                <i class="oe-i child-arrow small"></i>
                            </button>
                        </td>
                        <td></td>
                        <td>
                            <a data-action_type="delete" class="js-delete-set-medication"><i class="oe-i trash"></i></a>
                        </td>
                    </tr>
                    <tr class="js-row-of-<?=$med->id?> no-line js-addition-line" id="medication_set_item_<?=$k?>_addition_line" data-id="<?=$med->id?>" data-med_id="<?=$med->id?>">
                        <td class="right" colspan="99">

                            <div class="js-input-wrapper" style="display: inline-block;">
                                <label class="inline highlight js-input">
                                    <?=\CHtml::activeCheckBox($med, 'include_parent', ['name' => "MedicationSetAutoRuleMedication[$k][include_parent]"]); ?> Include Parent
                                </label>
                            </div>

                            <div class="js-input-wrapper" style="display: inline-block;">
                                <label class="inline highlight js-input">
                                    <?=\CHtml::activeCheckBox($med, 'include_children', ['name' => "MedicationSetAutoRuleMedication[$k][include_children]"]); ?> Include Children
                                </label>
                            </div>
                            <span class="tabspace"></span>
                        </td>
                    </tr>

                <?php
                if (!empty($med->tapers)) {
                    foreach ($med->tapers as $count => $taper) {
                        $this->renderPartial('/AutoSetRule/edit/MedicationSetItemTaper_edit', array(
                            "taper" => $taper,
                            "set_item_medication_id" => $med->id,
                            "data_parent_key" => $k,
                            "set_item_medication" => $med,
                            "taper_count" => $count,
                            "frequency_options" => $frequency_options,
                            "duration_options" => $duration_options,
                            'is_prescription_set' => $is_prescription_set
                        ));
                    }
                }
                ?>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <td colspan="9">
                <?php $this->widget('LinkPager', ['pages' => $medication_data_provider->pagination]); ?>
            </td>
            </tfoot>
        </table>
    </div>
</div>

<script type="text/template" id="medication_item_taper_template">
    <?php
        $empty_entry = new MedicationSetAutoRuleMedicationTaper();

        $this->renderPartial('/AutoSetRule/edit/MedicationSetItemTaper_edit', array(
            "taper" => $empty_entry,
            "set_item_medication_id" => "{{data_med_id}}",
            "data_parent_key" => "{{data_parent_key}}",
            "set_item_medication" => null,
            "taper_count" => "{{taper_count}}",
            "frequency_options" => $frequency_options,
            "duration_options" => $duration_options,
            'is_prescription_set' => $is_prescription_set
        ));

        ?>
</script>

<script type="x-tmpl-mustache" id="medication_template" style="display:none">
    <tr class="js-row-of-{{medication_id}} new" id="medication_set_item_{{key}}" data-id="{{id}}" data-med_id="{{medication_id}}" data-key="{{key}}" style="cursor: default;">
        <td>
            {{preferred_term}}
            <input class="js-input" name="MedicationSetAutoRuleMedication[{{key}}][id]" type="hidden" value="{{id}}">
            <input class="js-input" name="MedicationSetAutoRuleMedication[{{key}}][medication_id]" type="hidden" value="{{medication_id}}">
        </td>
        <td class="js-input-wrapper">
            <input class="js-input cols-full" name="MedicationSetAutoRuleMedication[{{key}}][default_dose]" id="MedicationSetAutoRuleMedication" type="text" value="{{default_dose}}">
        </td>
        <td class="js-input-wrapper">
            <input type="hidden" name="MedicationSetAutoRuleMedication[{{key}}][default_dose_unit_term]" value="{{default_dose_unit_term}}">
            <span data-type="default_dose_unit_term">{{^default_dose_unit_term}}-{{/default_dose_unit_term}}{{#default_dose_unit_term}}{{default_dose_unit_term}}{{/default_dose_unit_term}}</span>
        </td>
        <td class="js-input-wrapper">
            <?=\CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_route_id]', null, $route_options, ['id' => null, 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
        </td>
        <td class="js-input-wrapper">
            <?=\CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_frequency_id]', null, $frequency_options, ['id' => null, 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
        </td>
        <td class="js-input-wrapper">
            <?=\CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_duration_id]', null, $duration_options, ['id' => null, 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
        </td>
        <?php
            $css = $is_prescription_set ? '' : 'background: lightgray; color: gray';
        ?>
        <td class="js-input-wrapper js-prescription-extra js-prescription-dispense-condition">
            <?= \CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_dispense_condition_id]', null, $default_dispense_condition, ['disabled' => !$is_prescription_set, 'class' => 'js-input cols-full dispense-condition', 'style' => $css, 'empty' => '-- select --', 'id' => null]); ?>
        </td>
        <td class="js-input-wrapper js-prescription-extra js-prescription-dispense-location">
            <?= \CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_dispense_location_id]', null, $default_dispense_location, ['disabled' => !$is_prescription_set, 'class' => 'js-input cols-full dispense-location', 'style' => $css, 'empty' => '-- select --', 'id' => null]); ?>
        </td>

        <td class="actions" style="text-align:center">
            <button class="js-add-taper" data-action_type="add-taper" type="button" title="Add taper">
                <i class="oe-i child-arrow small"></i>
            </button>
        </td>
        <td></td>
        <td>
            <a data-action_type="delete" class="js-delete-set-medication"><i class="oe-i trash"></i></a>
        </td>
    </tr>
    <tr class="js-row-of-{{medication_id}} no-line js-addition-line new" id="medication_set_item_{{key}}_addition_line" data-id="{{medication_id}}" data-med_id="{{medication_id}}">
        <td class="right" colspan="99">
            <div class="js-input-wrapper" style="display: inline-block;">
                <label class="inline highlight js-input">
                    <?=\CHtml::checkBox('MedicationSetAutoRuleMedication[{{key}}][include_parent]', false); ?> Include Parent
                </label>
            </div>

            <div class="js-input-wrapper" style="display: inline-block;">
                <label class="inline highlight js-input">
                    <?=\CHtml::checkBox('MedicationSetAutoRuleMedication[{{key}}][include_children]', false); ?> Include Children
                </label>
            </div>
            <span class="tabspace"></span>
        </td>
    </tr>
</script>

<script>
    new OpenEyes.UI.AdderDialog({
        openButton: $('#add-medication-btn'),
        onReturn: function (adderDialog, selectedItems) {
            const $table = $(drugSetController.options.tableSelector + ' tbody');
            selectedItems.forEach(item => {
                //how nice that filter is coming back as a selected item
                if (item.label && item.label === 'Include brand names') {
                    return;
                }

                $('.empty-set').hide();
                $('#search_query').show();
                $(drugSetController.options.tableSelector).show();

                const medication_id = item.id;
                let data = item;

                data.id = '';
                data.medication_id = medication_id;
                data.key = OpenEyes.Util.getNextDataKey($table.find('tr'), 'key');
                if (data.short_term) {
                    data.preferred_term = data.short_term;
                }
                data.preferred_term += (data.amp_term && data.vtm_term) ? ' (' + data.vtm_term + ')' : '';

                const $tr_html = Mustache.render($('#medication_template').html(), data);
                $(drugSetController.options.tableSelector + ' tbody').append($tr_html);

                const usage_code = $('.js-usage-rule-table').find('input[name$="usage_code_id]"]').val();
                if ($('select.dispense-condition').prop('disabled') && typeof(usage_code) !== "undefined" && parseInt(usage_code) === prescriptionUsageRuleId) {
                    for (const dropdownName of ['condition', 'location']) {
                        let $dropdown = $('select.dispense-' + dropdownName);
                        $dropdown.prop('disabled', false);
                        $dropdown.prop('style', '');
                    }
                }

                $('#meds-list').trigger('medicationAdded');
                const $tr = $table.find('tr.js-row-of-' + medication_id);
                $tr.css({'background-color': '#3ba93b'});
                $tr.next().css({'background-color': '#3ba93b'});
                setTimeout(() => {
                    $tr.next().find('.js-edit-set-medication').trigger('click');
                    $tr.animate({'background-color': 'transparent'}, 2000);
                    $tr.next().animate({'background-color': 'transparent'}, 2000);
                },500);
            });
        },
        searchOptions: {
            searchSource: '/medicationManagement/findRefMedications',
        },
        enableCustomSearchEntries: true,
        searchAsTypedItemProperties: {id: "<?php echo EventMedicationUse::USER_MEDICATION_ID ?>"},
        booleanSearchFilterEnabled: true,
        booleanSearchFilterLabel: 'Include brand names',
        booleanSearchFilterURLparam: 'include_branded'
    });

    $('#meds-list').delegate('select.dispense-condition', 'change', function () {
        let $dispense_condition = $(this);
        let data_med_id = $dispense_condition.closest('tr').data('med_id');
        let $dispense_location = $dispense_condition.closest('tr').find('.js-prescription-dispense-location');
        let $dispense_location_dropdown = $dispense_location.find('.dispense-location');
        let $confirm_btn = $(`.js-row-of-${data_med_id}.js-addition-line`).find('.js-tick-set-medication');

        $confirm_btn.removeAttr('data-action_type');
        $confirm_btn.find('i').css('opacity', '0.4');
        $.ajax({
            type: 'GET',
            url: baseUrl + "/OphDrPrescription/PrescriptionCommon/GetDispenseLocation",
            data: {condition_id: $dispense_condition.val()},
            success: function (data) {
                if ($dispense_condition.is(':visible')) { //check if still visible otherwise ignore the request
                    $dispense_location.find('option').remove();
                    if (data) {
                        $dispense_location_dropdown.append(data);
                        $dispense_location.show();
                        $dispense_location.removeClass('js-hide-field');
                    } else {
                        $dispense_location_dropdown.append('<option value>-- select --</option>');
                        $dispense_location.hide();
                        $dispense_location.addClass('js-hide-field');
                    }
                }
            },
            complete: function () {
                $confirm_btn.attr('data-action_type', 'save');
                $confirm_btn.find('i').css('opacity', '');
            }
        });
    });

</script>
