<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/** @var Medication $medication */

    $sets = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->name];

    }, MedicationSet::model()->findAllByAttributes(['hidden' => 0, 'deleted_date' => null]));
    $units = [];
    $unit_attr = MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'");
    if ($unit_attr) {
        $units = array_map(function ($e) {
            return ['id' => $e->id, 'label' => $e->description];
        }, $unit_attr->medicationAttributeOptions);
    } else {
        $units = array();
    }
    $routes = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->term];

    }, MedicationRoute::model()->findAllByAttributes(['deleted_date' => null]));
    $frequencies = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->term];

    }, MedicationFrequency::model()->findAllByAttributes(['deleted_date' => null]));
    $durations = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->name];

    }, MedicationDuration::model()->findAllByAttributes(['deleted_date' => null]));

    $medicationSetItems = $medication->medicationSetItems;

    ?>
<script id="set_row_template" type="x-tmpl-mustache">
    <tr data-key="{{ key }}">
        <td>
        <input type="hidden" name="MedicationSetItem[{{key}}][medication_set_id]" value="{{set.id}}" />
        {{set.label}}
        </td>
       <td>
            <?= CHtml::textField('MedicationSetItem[{{key}}][default_dose]', "1"); ?>
        </td>
        <td>
            <?= CHtml::textField('MedicationSetItem[{{key}}][default_dose_unit_term]', '{{unit.label}}'); ?>
        </td>
        <td>
            <input type="hidden" name="MedicationSetItem[{{key}}][default_route_id]" value="{{route.id}}" />
            {{route.label}}
        </td>
        <td>
            <input type="hidden" name="MedicationSetItem[{{key}}][default_frequency_id]" value="{{frequency.id}}" />
            {{frequency.label}}
        </td>
        <td>
            <input type="hidden" name="MedicationSetItem[{{key}}][default_duration_id]" value="{{duration.id}}" />
            {{duration.label}}
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-set"><i class="oe-i trash"></i></a>
        </td>
    </tr>
</script>
<h3>Medication is member of the following sets</h3>
<table class="standard" id="medication_set_assignment_tbl">
    <colgroup>
        <col width="17%">
        <col width="13%">
        <col width="13%">
        <col width="13%">
        <col width="13%">
        <col width="13%">
        <col width="5%">
    </colgroup>
    <thead>
        <tr>
            <th>Name</th>
            <th>Default dose</th>
            <th>Default dose unit</th>
            <th>Default route</th>
            <th>Default freq</th>
            <th>Default duration</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($medicationSetItems as $rowkey => $assignment) : ?>
        <tr data-key="<?=$rowkey?>"<?=$assignment->medicationSet->hidden ? ' style="display:none;"' : ''; ?>>
            <td>
                <input type="hidden" name="MedicationSetItem[<?=$rowkey?>][id]" value="<?=$assignment->id?>" />
                <input type="hidden" name="MedicationSetItem[<?=$rowkey?>][medication_set_id]" value="<?=$assignment->medication_set_id?>" />
                <?=CHtml::encode($assignment->medicationSet->name)?>
            </td>
            <td>
                <?= CHtml::textField("MedicationSetItem[$rowkey][default_dose]", $assignment->default_dose); ?>
            </td>
            <td>
                <?= CHtml::textField("MedicationSetItem[$rowkey][default_dose_unit_term]", $assignment->default_dose_unit_term); ?>
            </td>
            <td>
                <input type="hidden" name="MedicationSetItem[<?=$rowkey?>][default_route_id]" value="<?=$assignment->default_route_id?>" />
                <?=$assignment->default_route_id ? CHtml::encode($assignment->defaultRoute->term) : ""?>
            </td>
            <td>
                <input type="hidden" name="MedicationSetItem[<?=$rowkey?>][default_frequency_id]" value="<?=$assignment->default_frequency_id?>" />
                <?=$assignment->default_frequency_id ? CHtml::encode($assignment->defaultFrequency->term) : ""?>
            </td>
            <td>
                <input type="hidden" name="MedicationSetItem[<?=$rowkey?>][default_duration_id]" value="<?=$assignment->default_duration_id?>" />
                <?=$assignment->default_duration_id ? CHtml::encode($assignment->defaultDuration->name) : ""?>
            </td>
            <td>
                <a class="js-delete-set"><i class="oe-i trash"></i></a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot class="pagination-container">
        <tr>
            <td colspan="7">
                <div class="flex-layout flex-right">
                    <button class="button hint green js-add-set" type="button"><i class="oe-i plus pro-theme"></i></button>
                    <script type="text/javascript">
                        new OpenEyes.UI.AdderDialog({
                            openButton: $('.js-add-set'),
                            itemSets: [
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($sets) ?>, {'id': 'set', 'multiSelect': false, 'mandatory': true, header: "Set"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($units) ?>, {'id': 'unit','multiSelect': false, header: "Default unit"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($routes) ?>, {'id': 'route', 'multiSelect': false, header: "Default route"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($frequencies) ?>, {'id': 'frequency', 'multiSelect': false, header: "Default frequency"}),
                                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($durations) ?>, {'id': 'duration', 'multiSelect': false, header: "Default duration"})
                            ],
                            onReturn: function (adderDialog, selectedItems) {

                                let selectedEntry = {};
                                let $table = $("#medication_set_assignment_tbl > tbody");

                                $.each(selectedItems, function(i,e){
                                    selectedEntry[e.itemSet.options.id] = {
                                        id: e.id,
                                        label: e.label
                                    };
                                });

                                selectedEntry.key = OpenEyes.Util.getNextDataKey($table.find('tr'), 'key');
                                let template = $('#set_row_template').html();
                                Mustache.parse(template);

                                let rendered = Mustache.render(template, selectedEntry);
                                $table.append(rendered);
                                return true;
                            },
                            enableCustomSearchEntries: true,
                        });
                    </script>
                </div>
            </td>
        </tr>
    </tfoot>
</table>
<script>
    $(document).ready(function() {
        $('#medication_set_assignment_tbl').on("click", ".js-delete-set", function (e) {
            $(this).closest("tr").remove();
        });
    });
</script>
