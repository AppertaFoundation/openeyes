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

$sites = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, Site::model()->findAll());
$subspecialties = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, Subspecialty::model()->findAll());
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
            <tbody>
            <?php foreach ($medication_set->medicationSetRules as $k => $rule): ?>
                <tr data-key="<?= $k; ?>">
                    <td>
                        <?= \CHtml::activeHiddenField($rule, "[{$k}]id"); ?>
                        <?= \CHtml::activeHiddenField($rule, "[{$k}]site_id"); ?>
                        <?= ($rule->site_id ? CHtml::encode($rule->site->name) : "") ?>
                    </td>
                    <td>
                        <?= \CHtml::activeHiddenField($rule, "[{$k}]subspecialty_id"); ?>
                        <?= ($rule->subspecialty_id ? CHtml::encode($rule->subspecialty->name) : "") ?>
                    </td>
                    <td>
                        <?= CHtml::activeDropDownList($rule, "[{$k}]usage_code_id", CHtml::listData(MedicationUsageCode::model()->findAll(), 'id', 'name')); ?>
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
            <?= CHtml::dropDownList('MedicationSetRule[{{key}}][usage_code_id]', null, CHtml::listData(MedicationUsageCode::model()->findAll(), 'id', 'name')); ?>
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
            return true;
        },
        enableCustomSearchEntries: true,
    });

    $(document).on("click", ".js-delete-rule", function (e) {
        $(e.target).closest("tr").remove();
    });
</script>
