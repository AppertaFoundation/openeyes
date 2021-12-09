<?php
/**
 * OpenEyes.
 *
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

use OEModule\OphCiExamination\models;

$readings = models\OphCiExamination_IntraocularPressure_Reading::model()->findAll();
$scale_readings = models\OphCiExamination_Qualitative_Scale::model()->findByAttributes(['name' => 'digital'])->values;
$scale_values = [];
foreach ($scale_readings as $reading) {
    $scale_values [$reading->id] = $reading->name;
}

$reading_values = [];
foreach ($readings as $reading) {
    $reading_values[$reading->name] = $reading->id;
}

?>

<div class="cols-full">
    <table id="<?= CHtml::modelName($element) . '_readings_' . $side ?>" class="cols-full">
        <tbody>
        <?php
        // show input after validation fail
        $instrument_model = OEModule\OphCiExamination\models\OphCiExamination_Instrument::model();
        if (isset($_POST['OEModule_OphCiExamination_models_HistoryIOP']["{$side}_values"])) {
            foreach ($_POST['OEModule_OphCiExamination_models_HistoryIOP']["{$side}_values"] as $index => $value) {
                $this->render(
                    "HistoryIOP_event_edit_reading",
                    [
                        'element' => $element,
                        'form' => $form,
                        'side' => $side,
                        'index' => $index,
                        'time' => substr($value['reading_time'], 0, 5),
                        'instrumentId' => $value['instrument_id'],
                        'instrumentName' => $instrument_model->findByPk($value['instrument_id'])->name,
                        'value_reading_id' => isset($value['reading_id']) ? $value['reading_id'] : null,
                        'value_reading_name' => isset($value['reading_id']) ? array_search($value['reading_id'], $reading_values) : null,
                        'value_qualitative_reading_id' => isset($value['qualitative_reading_id']) ? $value['qualitative_reading_id'] : null,
                        'value_qualitative_reading_name' => isset($value['qualitative_reading_id']) ? $scale_values[$value['qualitative_reading_id']] : null,
                        'examinationDate' => $value['examination_date'],
                        'comment' => $value["{$side}_comments"],
                    ]
                );
            }
        }
        ?>
        </tbody>
    </table>

    <table id="<?= $model_name ?>_entry_table" class="cols-full">
        <thead>
        <th>Past IOPs</th>
        <th></th>
        <th></th>
        <th colspan="2"><i class="oe-i small pad js-patient-expand-btn expand"></i></th>
        </thead>
        <tbody style="display: none">
        <?php foreach ($pastIOPs as $iop) { ?>
            <?php $date = $iop->event->event_date; ?>
            <?php foreach ($iop->{$side.'_values'} as $iop_value) { ?>
                <tr>
                    <td><?= $iop_value->instrument->scale ? $iop_value->qualitative_reading->name : $iop_value->reading->name.'mm Hg' ?></td>
                    <td><?=$iop_value->instrument->name?></td>
                    <td colspan="2">
                        <span class="oe-date"><?=date('d M Y', strtotime($date));?></span>

                    </td>
                    <td>
                      <i class="oe-i time small no-click pad-left"></i>
                      <?=$iop_value->reading_time?>
                    </td>
                    <td>
                        <?php if (isset($iop->{$side.'_comments'}) && $iop->{$side.'_comments'}) { ?>
                            <i class="oe-i comments-added medium js-has-tooltip" data-tooltip-content="<?= $iop->{$side.'_comments'} ?>"></i>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        <?php } ?>
        </tbody>
    </table>
</div>

<div class="add-data-actions flex-item-bottom">
    <div class="flex-item-bottom">
        <button type="button" class="button hint green js-add-select-search">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>


<script type="text/template" id="<?= CHtml::modelName($element) . '_reading_template_' . $side ?>" class="hidden">
    <?php
    $this->render(
        "HistoryIOP_event_edit_reading",
        [
            'element' => $element,
            'form' => $form,
            'side' => $side,
            'time' => '{{time}}',
            'index' => '{{index}}',
            'instrument' => '{{instrument}}',
            'instrumentId' => '{{instrumentId}}',
            'instrumentName' => '{{instrumentName}}',
            'examinationDate' => '{{examinationDate}}',
            'value_reading_id' => '{{value_reading_id}}',
            'value_reading_name' => '{{value_reading_name}}',
            'value_qualitative_reading_id' => '{{value_qualitative_reading_id}}',
            'value_qualitative_reading_name' => '{{value_qualitative_reading_name}}',
            'comment' => null,
        ]
    );
    ?>
</script>


<script type="text/javascript">
    $(function () {
        let side = $('.<?= CHtml::modelName($element) ?> .<?=$side?>-eye');
        let readings = JSON.parse('<?= print_r(json_encode($reading_values), 1) ?>');
        let previouslySelectedColumn = null;
        let readingsValueNumberColumns = 2;

        let AdderDialog = new OpenEyes.UI.AdderDialog({
            id: 'add-iop-value-to-historyIOP',
            openButton: side.find('.js-add-select-search'),
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($instrument) {
                        return ['label' => $instrument->name, 'id' => $instrument->id, 'scale' => isset($instrument->scale->values) ? true : false];
                    }, OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->findAllByAttributes(['active' => 1]))
                ) ?>, {'id': 'instrument', 'header': 'Instrument'}),
                new OpenEyes.UI.AdderDialog.ItemSet([], {'id': 'reading_value', 'header': 'mm Hg',
                    'splitIntegerNumberColumns': [{'min': 0, 'max': 9},{'min': 0, 'max': 9}],
                    'style': 'display: none'}),
                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($scale) {
                        return ['label' => $scale->name, 'id' => $scale->id];
                    }, models\OphCiExamination_Qualitative_Scale_Value::model()->findAllByAttributes(['scale_id' => models\OphCiExamination_Qualitative_Scale::model()->findByAttributes(['name' => 'digital'])->id]))
                ) ?>, {'id': 'scale_value', 'header': 'Scale value', 'style': 'display: none'}),
                new OpenEyes.UI.AdderDialog.ItemSet([], {'id': 'time', 'header': 'Time',
                    'splitIntegerNumberColumns': [{'min': 1, 'max': 24}],
                    'decimalValues': [':00' , ':15' , ':30' , ':45'],
                    'supportDecimalValues': true}),
            ],
            onReturn: function (adderDialog, selectedItems, selectedAdditions) {
                if (selectedItems.length < 1
                    || selectedItems[0].scale && selectedItems.length < 2
                    || !selectedItems[0].scale && selectedItems.length < 3) {
                    return false;
                }


                let value_reading_id = null;
                let value_reading_name = null;
                if (!selectedItems[0].scale) {
                    let value_reading = 0;
                    for (let i = 1; i <= readingsValueNumberColumns; i++) {
                        if (selectedItems[i].reading_value == null) {
                            return false;
                        }
                        value_reading = 10 * value_reading + parseInt(selectedItems[i].reading_value);
                    }
                    value_reading_id = readings[value_reading];
                    value_reading_name = value_reading;
                }

                if (selectedItems[0].scale && (selectedItems[1]['id'] == null || selectedItems[1]['label'] == null)) {
                    return false;
                }
                let value_qualitative_reading_id = selectedItems[0].scale ? selectedItems[1]['id'] : null;
                let value_qualitative_reading_name = selectedItems[0].scale ? selectedItems[1]['label'] : null;

                let time = "00:00";
                if (selectedItems[selectedItems.length - 1].time != null) {
                    let H = selectedItems[selectedItems.length - 1].time;
                    let M = selectedAdditions[0].addition;
                    time = H + M;
                    if (H < 10) {
                        time = '0' + time;
                    }
                }

                HistoryIOP_addReading(
                    '<?=$side?>',
                    selectedItems[0]['id'],
                    selectedItems[0]['label'],
                    time,
                    value_reading_id,
                    value_reading_name,
                    value_qualitative_reading_id,
                    value_qualitative_reading_name,
                );

                // activate the datePicker
                addDatePicker($("#OEModule_OphCiExamination_models_HistoryIOP_readings_" + "<?=$side?>" + ' input[id*="OEModule_OphCiExamination_models_HistoryIOP_"].iop-date'));

                // hide reading_value and scale_value columns
                adderDialog.toggleColumnById(['reading_value', 'scale_value'], false);
                previouslySelectedColumn = null;
                return true;
            },
        });

         // activate all datepicker inputs
         addDatePicker($("#OEModule_OphCiExamination_models_HistoryIOP_readings_" + "<?=$side?>" + ' input[id*="OEModule_OphCiExamination_models_HistoryIOP_"].iop-date'));

        // show / hide reading value column and scale value column
        side.on('click', 'ul[data-id="instrument"] li', function() {
            if ($(this).hasClass("selected")) {
                if ($(this).data('scale')) {
                    AdderDialog.toggleColumnById(['reading_value'], false);
                    AdderDialog.toggleColumnById(['scale_value'], true);
                    if (previouslySelectedColumn === 'reading_value') {
                        AdderDialog.removeSelectedColumnById(['reading_value', 'scale_value']);
                        side.find('ul[data-id="scale_value"] li').first().click();
                    }
                    if (!previouslySelectedColumn) {
                        side.find('ul[data-id="scale_value"] li').first().click();
                    }
                    previouslySelectedColumn = "scale_value";
                } else {
                    AdderDialog.toggleColumnById(['reading_value'], true);
                    AdderDialog.toggleColumnById(['scale_value'], false);
                    if (previouslySelectedColumn === 'scale_value') {
                        AdderDialog.removeSelectedColumnById(['reading_value', 'scale_value']);
                        side.find('ul[data-id="reading_value"] li').first().click();
                    }
                    if (!previouslySelectedColumn) {
                        side.find('ul[data-id="reading_value"] li').first().click();
                    }
                    previouslySelectedColumn = "reading_value";
                }
            } else {
                AdderDialog.toggleColumnById(['reading_value'], false);
                AdderDialog.toggleColumnById(['scale_value'], false);
                AdderDialog.removeSelectedColumnById(['reading_value', 'scale_value']);
                previouslySelectedColumn = null;
            }
        });

        // select the default instrument when pressing on the adder button
        let default_instrument_id = <?= models\Element_OphCiExamination_IntraocularPressure::model()->getSetting('default_instrument_id') ?>;
        side.find('.js-add-select-search').on('click', function() {
            let $first_instrument_li = null;
            if (default_instrument_id) {
                // select the default instrument
                $first_instrument_li = side.find('ul[data-id="instrument"] li[data-id=' + default_instrument_id + ']').first();
            } else {
                // select the first instrument by default
                $first_instrument_li = side.find('ul[data-id="instrument"] li').first().click();
            }
            if (!$first_instrument_li.hasClass('selected')) {
                $first_instrument_li.click();
            }
        });

        // scroll the numbers list so 9 is at top
        side.find('.js-add-select-search').on('click', function () {
            let $selected = side.find('ul.add-options.cols-full.single[data-id="time"] ul.number li[data-time="9"]');
            side.find('ul.add-options.cols-full.single[data-id="time"]').scrollTop($selected.offset().top - $selected.parent().offset().top);
        });

    });

    function HistoryIOP_addReading(side, instrumentId, instrumentName, time,
                value_reading_id, value_reading_name, value_qualitative_reading_id, value_qualitative_reading_name) {
        let table = $("#OEModule_OphCiExamination_models_HistoryIOP_readings_" + side);
        let indices = table.find('tr').map(function () {
            return $(this).data('index');
        });

        let tr = Mustache.render(
            template = $("#OEModule_OphCiExamination_models_HistoryIOP_reading_template_" + side).text(),
            {
                index: indices.length ? Math.max.apply(null, indices) + 1 : 0,
                time: time ? time : (new Date).toTimeString().substr(0, 5),
                instrumentId: instrumentId,
                instrumentName: instrumentName,
                value_reading_id: value_reading_id,
                value_reading_name: value_reading_name,
                value_qualitative_reading_id: value_qualitative_reading_id,
                value_qualitative_reading_name: value_qualitative_reading_name,
                comment: null,
            }
        );

        table.find("tbody").append(tr);

        // hide value reading column
        if (!value_reading_id) {
            table.find("tbody tr:last").find('input[name*="[reading_id]"]').parent().remove();
        }
        // hide qualitative reading column
        if (!value_qualitative_reading_id) {
            table.find("tbody tr:last").find('input[name*="[qualitative_reading_id]"]').parent().remove();
        }

        table.show();
    }
</script>
