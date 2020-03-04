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
$default_dispense_location = \CHtml::listData(\OphDrPrescription_DispenseLocation::model()->findAll(), 'id', 'name');
$default_dispense_condition = \CHtml::listData(\OphDrPrescription_DispenseCondition::model()->findAll(), 'id', 'name');

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
                $route_options = \CHtml::listData(\MedicationRoute::model()->findAll(), 'id', 'term');
                $frequency_options = \CHtml::listData(\MedicationFrequency::model()->findAll(), 'id', 'term');
                $duration_options = \CHtml::listData(\MedicationDuration::model()->findAll(), 'id', 'name');
            ?>
            <?php foreach ($medication_data_provider->getData() as $k => $med) : ?>
                <?php $set_item = \MedicationSetAutoRuleMedication::model()->findByAttributes(['medication_id' => $med->id, 'medication_set_id' => $medication_set->id]);?>
                <?php if ($set_item) : ?>
                    <tr class="js-row-of-<?=$med->id?>" id="medication_set_item_<?=$k?>" data-id="<?=$med->id?>" data-med_id="<?=$med->id?>" data-key="<?=$k;?>">
                        <td>
                            <input type="hidden" name="set_id" class="js-input js-medication-set-id" value="<?=$medication_set->id;?>">

                            <?= $med->preferred_term; ?>
                            <?= \CHtml::activeHiddenField($set_item, 'id', ['class' => 'js-input', 'name' => "MedicationSetAutoRuleMedication[$k][id]"]); ?>
                            <?= \CHtml::activeHiddenField($med, 'id', ['class' => 'js-input', 'name' => "MedicationSetAutoRuleMedication[$k][medication_id]"]); ?>
                        </td>
                        <td class="js-input-wrapper">
                            <span data-type="default_dose" data-id="<?= $set_item->default_dose ? $set_item->default_dose : ''; ?>" class="js-text"><?= $set_item->default_dose ? $set_item->default_dose : '-'; ?></span>
                            <?= \CHtml::activeTextField($set_item, 'default_dose',
                                [
                                    'class' => 'js-input cols-full',
                                    'style' => 'display:none',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_dose]"
                                ]); ?>
                        </td>
                        <td>
                            <span data-type="default_dose" data-id="<?= $set_item->default_dose_unit_term ? $set_item->default_dose_unit_term : ''; ?>"><?= $set_item->default_dose_unit_term ? $set_item->default_dose_unit_term : '-'; ?></span>
                        </td>
                        <td class="js-input-wrapper">
                            <span data-type="default_route" data-id="<?= $set_item->defaultRoute ? $set_item->default_route_id : ''; ?>" class="js-text"><?= $set_item->defaultRoute ? $set_item->defaultRoute->term : '-'; ?></span>
                            <?= \CHtml::activeDropDownList(
                                $set_item,
                                'default_route_id',
                                $route_options,
                                [
                                    'class' => 'js-input cols-full',
                                    'style' => 'display:none',
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_route_id]"
                                ]); ?>
                        </td>
                        <td class="js-input-wrapper">
                            <span data-type="default_frequency" data-id="<?= $set_item->defaultFrequency ? $set_item->default_frequency_id : ''; ?>" class="js-text"><?= $set_item->defaultFrequency ? $set_item->defaultFrequency->term : '-'; ?></span>
                            <?= \CHtml::activeDropDownList(
                                $set_item,
                                'default_frequency_id',
                                $frequency_options,
                                [
                                    'class' => 'js-input cols-full',
                                    'style' => 'display:none',
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_frequency_id]"
                                ]); ?>
                        </td>
                        <td class="js-input-wrapper">
                            <span data-type="default_duration" data-id="<?= $set_item->defaultDuration ? $set_item->default_duration_id : ''; ?>" class="js-text"><?= $set_item->defaultDuration ? $set_item->defaultDuration->name : '-'; ?></span>
                            <?= \CHtml::activeDropDownList(
                                $set_item,
                                'default_duration_id',
                                $duration_options,
                                [
                                    'class' => 'js-input cols-full',
                                    'style' => 'display:none',
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_duration_id]"
                                ]
                            ); ?>
                        </td>

                        <td class="js-input-wrapper">
                            <div class="js-prescription-extra js-prescription-dispense-condition" style="display:<?=$is_prescription_set ? 'block':'none';?>">
                            <span data-type="default_dispense_condition" data-id="<?= $set_item->defaultDispenseCondition ? $set_item->default_dispense_condition_id : ''; ?>" class="js-text">
                                <?php if ($set_item->default_dispense_condition_id) {
                                    if ($set_item->defaultDispenseCondition->name === 'Print to {form_type}') {
                                        echo str_replace(
                                                '{form_type}',
                                                $fpten_setting,
                                                $set_item->defaultDispenseCondition->name
                                            );
                                    } else {
                                        echo  $set_item->defaultDispenseCondition->name;
                                    }
                                } else {
                                    echo '-';
                                } ?>

                            </span>
                            <?= \CHtml::activeDropDownList(
                                $set_item,
                                'default_dispense_condition_id',
                                CHtml::listData(
                                    OphDrPrescription_DispenseCondition::model()->findAll(array(
                                        'condition' => '(active'
                                            . ($overprint_setting === 'off' ? " and id != '" . $fpten_dispense_condition->id . "'" : null)
                                            . ") or id='" . $set_item->default_dispense_condition_id . "'",
                                        'order' => 'display_order',
                                    )), 'id', 'name'),
                                [
                                    'class' => 'js-input cols-full dispense-condition',
                                    'style' => 'display:none',
                                    'empty' => '-- select --',
                                    'id' => null,
                                    'name' => "MedicationSetAutoRuleMedication[$k][default_dispense_condition_id]",
                                    'options' => $dispense_condition_options
                                ]
                            ); ?>
                            </div>
                        </td>
                        <td class="js-input-wrapper" >
                            <div class="js-prescription-extra js-prescription-dispense-location" style="display:<?=$is_prescription_set ? 'block':'none';?>">
                            <span data-type="default_dispense_location" data-id="<?= $set_item->defaultDispenseLocation ? $set_item->default_dispense_location_id : ''; ?>" class="js-text">
                                <?= $set_item->defaultDispenseLocation ? $set_item->defaultDispenseLocation->name : '-'; ?>
                            </span>
                            <?php $default_dispense_location = $set_item->default_dispense_condition_id ? CHtml::listData(OphDrPrescription_DispenseCondition::model()->findByPk($set_item->default_dispense_condition_id)->locations, 'id', 'name') : []; ?>
                            <?= \CHtml::activeDropDownList(
                                $set_item,
                                'default_dispense_location_id',
                                $default_dispense_location,
                                [
                                    'class' => 'js-input cols-full dispense-location',
                                    'style' => 'display:none',
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
                                <span data-type="include_parent" data-id="<?= $set_item->include_parent ? $set_item->include_parent : ''; ?>" class="js-text" data-display-label="Include Parent: ">
                                    <?=$set_item->getAttributelabel('include_parent') . ': ' . ($set_item->include_parent ? 'yes' : 'no')?>
                                </span>

                                <label class="inline highlight js-input" style="display: none">
                                    <?=\CHtml::activeCheckBox($set_item, 'include_parent', ['name' => "MedicationSetAutoRuleMedication[$k][include_parent]"]); ?> Include Parent
                                </label>
                            </div>

                            <div class="js-input-wrapper" style="display: inline-block;">
                                <span data-type="include_children" data-id="<?= $set_item->include_children ? $set_item->include_children : ''; ?>" class="js-text" data-display-label="Include Children: ">
                                    <?=$set_item->getAttributelabel('include_children') . ': ' . ($set_item->include_children ? 'yes' : 'no')?>
                                </span>
                                <label class="inline highlight js-input" style="display: none">
                                    <?=\CHtml::activeCheckBox($set_item, 'include_children', ['name' => "MedicationSetAutoRuleMedication[$k][include_children]"]); ?> Include Children
                                </label>
                            </div>
                            <span class="tabspace"></span>

                            <a data-action_type="save" class="js-tick-set-medication" style="display:none"><i class="oe-i save pad"></i></a>
                            <a data-action_type="cancel" class="js-cross-set-medication" style="display:none"><i class="oe-i cancel pad"></i></a>
                            <a data-action_type="edit" class="js-edit-set-medication"><i class="oe-i pencil pad"></i></a>

                        </td>
                    </tr>
                <?php endif; ?>

                <?php
                if (!empty($set_item->tapers)) {
                    foreach ($set_item->tapers as $count => $taper) {
                        $this->renderPartial('/DrugSet/MedicationSetItemTaper_edit', array(
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

        $this->renderPartial('/DrugSet/MedicationSetItemTaper_edit', array(
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
    <tr class="js-row-of-{{medication_id}} new" id="medication_set_item_{{key}}" data-id="{{medication_id}}" data-med_id="{{medication_id}}" data-key="{{key}}" style="cursor: default;">
        <td>
            {{preferred_term}}
            <input class="js-input" name="MedicationSetAutoRuleMedication[{{key}}][id]" type="hidden" value="{{id}}">
            <input class="js-input" name="MedicationSetAutoRuleMedication[{{key}}][medication_id]" type="hidden" value="{{medication_id}}">
        </td>
        <td class="js-input-wrapper">
            <span data-type="default_dose" class="js-text" style="display: inline;">{{^default_dose}}-{{/default_dose}}{{#default_dose}}{{default_dose}}{{/default_dose}}</span>
            <input class="js-input cols-full" style="display: none;" name="MedicationSetAutoRuleMedication[{{key}}][default_dose]" id="MedicationSetAutoRuleMedication" type="text" value="{{default_dose}}">
        </td>
        <td class="js-input-wrapper">
            <span data-type="default_dose_unit_term">{{^default_dose_unit_term}}-{{/default_dose_unit_term}}{{#default_dose_unit_term}}{{default_dose_unit_term}}{{/default_dose_unit_term}}</span>
        </td>
        <td class="js-input-wrapper">
            <span data-id="{{#default_route_id}}{{default_route_id}}{{/default_route_id}}" data-type="default_route" class="js-text" style="display: inline;">{{^default_route}}-{{/default_route}}{{#default_route}}{{default_route}}{{/default_route}}</span>
            <?=\CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_route_id]', null, $route_options, ['id' => null, 'style' => 'display:none', 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
        </td>
        <td class="js-input-wrapper">
            <span data-id="{{#default_frequency_id}}{{default_frequency_id}}{{/default_frequency_id}}" data-type="default_frequency" class="js-text" style="display: inline;">{{^default_frequency}}-{{/default_frequency}}{{#default_frequency}}{{default_frequency}}{{/default_frequency}}</span>
            <?=\CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_frequency_id]', null, $frequency_options, ['id' => null, 'style' => 'display:none', 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
        </td>
        <td class="js-input-wrapper">
            <span data-id="{{#default_duration_id}}{{default_duration_id}}{{/default_duration_id}}" data-type="default_duration" class="js-text" style="display: inline;">{{^default_duration}}-{{/default_duration}}{{#default_duration}}{{default_duration}}{{/default_duration}}</span>
            <?=\CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_duration_id]', null, $duration_options, ['id' => null, 'style' => 'display:none', 'class' => 'js-input cols-full', 'empty' => '-- select --']);?>
        </td>

        <td class="js-input-wrapper">
            <div class="js-prescription-extra js-prescription-dispense-condition" style="display:none;?>">
                <span data-id="{{#default_dispense_condition_id}}{{default_dispense_condition_id}}{{/default_dispense_condition_id}}" data-type="default_dispense_condition" class="js-text">{{^default_dispense_condition}}-{{/default_dispense_condition}}{{#default_dispense_condition}}{{default_dispense_condition}}{{/default_dispense_condition}}</span>
                <?= \CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_dispense_condition_id]', null, $default_dispense_condition, ['class' => 'js-input cols-full dispense-condition', 'style' => 'display:none', 'empty' => '-- select --', 'id' => null]); ?>
            </div>
        </td>
        <td class="js-input-wrapper">
            <div class="js-prescription-extra js-prescription-dispense-location" style="display:none;">
                <span data-id="{{#default_dispense_location_id}}{{default_dispense_location_id}}{{/default_dispense_location_id}}" data-type="default_dispense_location" class="js-text">{{^default_dispense_location_id}}-{{/default_dispense_location_id}}{{#default_dispense_location_id}}{{default_dispense_location_id}}{{/default_dispense_location_id}}</span>
                <?= \CHtml::dropDownList('MedicationSetAutoRuleMedication[{{key}}][default_dispense_location_id]', null, $default_dispense_location, ['class' => 'js-input cols-full dispense-location', 'style' => 'display:none', 'empty' => '-- select --', 'id' => null]); ?>
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
    <tr class="js-row-of-{{medication_id}} no-line js-addition-line new" id="medication_set_item_{{key}}_addition_line" data-id="{{medication_id}}" data-med_id="{{medication_id}}">
        <td class="right" colspan="99">
            <div class="js-input-wrapper" style="display: inline-block;">
                <span data-type="include_parent" data-id="{{#include_parent}}{{include_parent}}{{/include_parent}}" class="js-text" data-display-label="Include Parent: ">
                    Include Parent: {{^include_parent}}-{{/include_parent}}{{#include_parent}}{{include_parent}}{{/include_parent}}
                </span>

                <label class="inline highlight js-input" style="display: none">
                    <?=\CHtml::checkBox('MedicationSetAutoRuleMedication[{{key}}][include_parent]', false); ?> Include Parent
                </label>
            </div>

            <div class="js-input-wrapper" style="display: inline-block;">
                <span data-type="include_children" data-id="{{#include_children}}{{include_children}}{{/include_children}}" class="js-text" data-display-label="Include Children: ">
                    Include Children: {{^include_children}}-{{/include_children}}{{#include_children}}{{include_children}}{{/include_children}}
                </span>

                <label class="inline highlight js-input" style="display: none">
                    <?=\CHtml::checkBox('MedicationSetAutoRuleMedication[{{key}}][include_children]', false); ?> Include Children
                </label>
            </div>
            <span class="tabspace"></span>
            <a data-action_type="save" class="js-tick-set-medication" style="display:none"><i class="oe-i save pad"></i></a>
            <a data-action_type="cancel" class="js-cross-set-medication" style="display:none"><i class="oe-i cancel pad"></i></a>
            <a data-action_type="edit" class="js-edit-set-medication"><i class="oe-i pencil pad"></i></a>
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
                if (item.label && item.label === 'Include branded drugs in search results') {
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

                const $tr_html = Mustache.render($('#medication_template').html(), data);
                $(drugSetController.options.tableSelector + ' tbody').append($tr_html);
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
        booleanSearchFilterLabel: 'Include branded',
        booleanSearchFilterURLparam: 'include_branded'
    });

    $('body').on('change', "select[id$='_usage_code_id']", function() {
        togglePrescriptionExtraInputs();
    });

    $('#meds-list').delegate('select.dispense-condition', 'change', function () {
        let $dispense_condition = $(this);
        let $dispense_location = $dispense_condition.closest('tr').find('.js-prescription-dispense-location');
        let $dispense_location_dropdown = $dispense_location.find('.dispense-location');

        $.get(baseUrl + "/OphDrPrescription/PrescriptionCommon/GetDispenseLocation", {
            condition_id: $dispense_condition.val(),
        }, function (data) {
            if ($dispense_condition.is(':visible')) { //check if still visible otherwise ignore the request
                $dispense_location.find('option').remove();
                if (data) {
                    $dispense_location_dropdown.append(data);
                    $dispense_location.show();
                    $dispense_location.removeClass('hidden');
                } else {
                    $dispense_location_dropdown.append('<option value>-- select --</option>');
                    $dispense_location.hide();
                    $dispense_location.addClass('hidden');
                }
            }
        });
    });

</script>
