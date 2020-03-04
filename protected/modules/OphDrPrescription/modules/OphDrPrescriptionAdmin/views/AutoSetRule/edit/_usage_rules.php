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

$all_usage_codes = MedicationUsageCode::model()->findAll(['condition' => 'active = 1']);

$sites = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, Site::model()->findAll());
$subspecialties = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, Subspecialty::model()->findAll());
$filtered_usage_code_id = isset($filtered_usage_code) ? $filtered_usage_code : -1;
$usage_codes = array_map(function ($e) use ($filtered_usage_code_id) {
    return ['id' => $e->id, 'label' => $e->name, 'defaultSelected' => $e->id == $filtered_usage_code_id ];
}, $all_usage_codes);
?>

 <div class="row">
    <div class="cols-12">
        <h3 <?=($medication_set->hasErrors('medicationSetRule') ? "style='color:red'" : '');?>>Usage Rules</h3>
        <table class="standard" id="rule_tbl">
            <thead>
            <tr>
                <th>Site</th>
                <th>Subspecialty</th>
                <th>Usage Code</th>
                <th width="5%">Action</th>
            </tr>
            </thead>
            <tbody class='js-usage-rule-table'>
            <?php foreach ($medication_set->medicationSetRules as $k => $rule) : ?>
                <tr data-key="<?= $k; ?>">
                    <td>
                        <input type="hidden" name="MedicationSetRule[<?=$k?>][id]"  value="<?=$rule->id?>"/>
                        <input type="hidden" name="MedicationSetRule[<?=$k?>][site_id]"  value="<?=$rule->site_id?>"/>
                        <?= ($rule->site_id ? CHtml::encode($rule->site->name) : "") ?>
                    </td>
                    <td>
                        <input type="hidden" name="MedicationSetRule[<?=$k?>][subspecialty_id]"  value="<?=$rule->subspecialty_id?>"/>
                        <?= ($rule->subspecialty_id ? CHtml::encode($rule->subspecialty->name) : "") ?>
                    </td>
                    <td>
                        <input type="hidden" name="MedicationSetRule[<?=$k?>][usage_code_id]" value="<?= $rule->usage_code_id ?>" />
                        <?= ($rule->usage_code_id ? CHtml::encode($rule->usageCode->name) : "") ?>
                    </td>
                    <td>
                        <a href="javascript:void(0);" class="js-delete-rule"><i class="oe-i trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="4">
                    <div class="flex-layout flex-right">
                        <button class="button hint green js-add-set" type="button"><i
                                    class="oe-i plus pro-theme"></i>
                        </button>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<script type="x-tmpl-mustache" id="rule_row_template" style="display:none">
    <tr data-key="{{key}}">
        <td>
            <input type="hidden" name="MedicationSetRule[{{key}}][id]" />
            <input type="hidden" name="MedicationSetRule[{{key}}][site_id]" value="{{site.id}}" />
            {{site.label}}
        </td>
        <td>
            <input type="hidden" name="MedicationSetRule[{{key}}][subspecialty_id]" value="{{subspecialty.id}}" />
            {{subspecialty.label}}
        </td>
        <td>
            <input type="hidden" name="MedicationSetRule[{{key}}][usage_code_id]" value="{{usage_code.id}}" />
            {{usage_code.label}}
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-rule"><i class="oe-i trash"></i></a>
        </td>
    </tr>
</script>
<script>
    new OpenEyes.UI.AdderDialog({
        openButton: $('.js-add-set'),
        itemSets: [
            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($sites) ?>, {
                'id': 'site',
                'multiSelect': false,
                header: "Site"
            }),
            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($subspecialties) ?>, {
                'id': 'subspecialty',
                'multiSelect': false,
                header: "Subspecialty"
            }),
            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($usage_codes) ?>, {
                'id': 'usage_code',
                'multiSelect': false,
                'mandatory': true,
                'resetSelectionToDefaultOnReturn': true,
                header: "Usage Code"
            }),
        ],
        onReturn: function (adderDialog, selectedItems) {
            let selObj = {};

            $.each(selectedItems, function (i, e) {
                selObj[e.itemSet.options.id] = {
                    id: e.id,
                    label: e.label
                };
            });

            let lastkey = $("#rule_tbl > tbody > tr:last").attr("data-key");
            if (isNaN(lastkey)) {
                lastkey = 0;
            }
            let key = parseInt(lastkey) + 1;
            let template = $('#rule_row_template').html();
            Mustache.parse(template);

            selObj.key = key;

            let rendered = Mustache.render(template, selObj);
            $("#rule_tbl > tbody").append(rendered);
            $('.js-usage-rule-table tr:last select option[value=' + selObj['usage_code'].id + ']').attr('selected', 'selected');


            togglePrescriptionExtraInputs();
            return true;
        },
        enableCustomSearchEntries: true,
    });

    $(document).on("click", ".js-delete-rule", function (e) {
        $(e.target).closest("tr").remove();
        togglePrescriptionExtraInputs();
    });
</script>
