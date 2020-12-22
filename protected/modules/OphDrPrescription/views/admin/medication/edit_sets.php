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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/** @var Medication $medication */

$sets = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->name];
}, MedicationSet::model()->findAllByAttributes(['hidden' => 0, 'deleted_date' => null]));
$units = [];
$medication_attribute_options = MedicationAttributeOption::model()->with('medicationAttribute')->findAll(
    ["condition" => "medicationAttribute.name = 'UNIT_OF_MEASURE'", 'order' => 'description asc']
);
if ($unit_attr = MedicationAttribute::model()->find("name='UNIT_OF_MEASURE'")) {
    $units = array_map(function ($e) {
        return ['id' => $e->id, 'label' => $e->description];
    }, $medication_attribute_options);
} else {
    $units = array();
}
$routes = array_map(function ($e) {
    return ['id' => $e->id, 'label' => $e->term];
}, MedicationRoute::model()->findAllByAttributes(['deleted_date' => null]));
$freqs = array_map(function ($e) {
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
        <input type="hidden" name="Medication[medicationSetItems][id][]" value="-1" />
        <input type="hidden" name="Medication[medicationSetItems][medication_set_id][]" value="{{set.id}}" />
        {{set.label}}
        </td>
       <td>
            <?php echo CHtml::textField('Medication[medicationSetItems][default_dose][]', "1"); ?>
        </td>
        <td>
                <?php echo CHtml::dropDownList(
                    'Medication[medicationSetItems][default_dose_unit_term][]',
                    '{{unit.label}}',
                    CHtml::listData($medication_attribute_options, "description", "description"),
                    array('empty' => '-- None --', 'class' => 'js-dose-unit')
                ) ?>
        </td>
        <td>
            <input type="hidden" name="Medication[medicationSetItems][default_route_id][]" value="{{route.id}}" />
            {{route.label}}
        </td>
        <td>
            <input type="hidden" name="Medication[medicationSetItems][default_frequency_id][]" value="{{frequency.id}}" />
            {{frequency.label}}
        </td>
        <td>
            <input type="hidden" name="Medication[medicationSetItems][default_duration_id][]" value="{{duration.id}}" />
            {{duration.label}}
        </td>
        <td>
            <a href="javascript:void(0);" class="js-delete-set"><i class="oe-i trash"></i></a>
        </td>
    </tr>


</script>
<script type="text/javascript">
    $(function () {
        $(document).on("click", ".js-delete-set", function (e) {
            $(e.target).closest("tr").remove();
        });
    });
</script>
<h3>Medication is member of the following sets</h3>
<table class="standard" id="medication_set_assignment_tbl">
    <thead>
    <tr>
        <th width="17%">Name</th>
        <th width="13%">Default dose</th>
        <th width="13%">Default dose unit</th>
        <th width="13%">Default route</th>
        <th width="13%">Default freq</th>
        <th width="13%">Default duration</th>
        <th width="5%">Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($medicationSetItems as $rowkey => $assignment) : ?>
        <?php
        $set_id = $assignment->medication_set_id;
        $id = is_null($assignment->id) ? -1 : $assignment->id;
        ?>
        <tr data-key="<?= $rowkey ?>">
            <td>
                <input type="hidden" name="Medication[medicationSetItems][id][]" value="<?= $id ?>"/>
                <input type="hidden" name="Medication[medicationSetItems][medication_set_id][]"
                       value="<?= $assignment->medication_set_id ?>"/>
                <?= CHtml::encode($assignment->medicationSet->name) ?>
            </td>
            <td>
                <?php echo CHtml::textField('Medication[medicationSetItems][default_dose][]', $assignment->default_dose); ?>
            </td>
            <td>
                <?php echo CHtml::dropDownList(
                    'Medication[medicationSetItems][default_dose_unit_term][]',
                    $assignment->default_dose_unit_term,
                    CHtml::listData($medication_attribute_options, "description", "description"),
                    array('empty' => '-- None --' )
                ) ?>
            </td>
            <td>
                <input type="hidden" name="Medication[medicationSetItems][default_route_id][]"
                       value="<?= $assignment->default_route_id ?>"/>
                <?= $assignment->default_route_id ? CHtml::encode($assignment->defaultRoute->term) : "" ?>
            </td>
            <td>
                <input type="hidden" name="Medication[medicationSetItems][default_frequency_id][]"
                       value="<?= $assignment->default_frequency_id ?>"/>
                <?= $assignment->default_frequency_id ? CHtml::encode($assignment->defaultFrequency->term) : "" ?>
            </td>
            <td>
                <input type="hidden" name="Medication[medicationSetItems][default_duration_id][]"
                       value="<?= $assignment->default_duration_id ?>"/>
                <?= $assignment->default_duration_id ? CHtml::encode($assignment->defaultDuration->name) : "" ?>
            </td>
            <td>
                <a href="javascript:void(0);" class="js-delete-attribute"><i class="oe-i trash"></i></a>
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
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($sets) ?>, {
                                'id': 'set',
                                'multiSelect': false,
                                header: "Set"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($units) ?>, {
                                'id': 'unit',
                                'multiSelect': false,
                                header: "Default unit"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($routes) ?>, {
                                'id': 'route',
                                'multiSelect': false,
                                header: "Default route"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($freqs) ?>, {
                                'id': 'frequency',
                                'multiSelect': false,
                                header: "Default frequency"
                            }),
                            new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode($durations) ?>, {
                                'id': 'duration',
                                'multiSelect': false,
                                header: "Default duration"
                            })
                        ],
                        onReturn: function (adderDialog, selectedItems) {
                            var selObj = {};

                            $.each(selectedItems, function (i, e) {
                                selObj[e.itemSet.options.id] = {
                                    id: e.id,
                                    label: e.label
                                };
                            });

                            var lastkey = $("#medication_set_assignment_tbl > tbody > tr:last").attr("data-key");
                            if (isNaN(lastkey)) {
                                lastkey = 0;
                            }
                            var key = parseInt(lastkey) + 1;
                            var template = $('#set_row_template').html();
                            Mustache.parse(template);

                            selObj.key = key;

                            var rendered = Mustache.render(template, selObj);

                            $("#medication_set_assignment_tbl > tbody").append(rendered);
                            var newRow = $("#medication_set_assignment_tbl > tbody").find('tr:last');
                            if (typeof selObj.unit !== "undefined") {
                                newRow.find('.js-dose-unit').val(selObj.unit.label).change();
                            }
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
