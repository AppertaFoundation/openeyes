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

$url = Yii::app()->assetManager->getPublishedPathOfAlias('application.modules.OphCiExamination.assets');
\Yii::app()->clientScript->registerScriptFile($url . '/js/CurrentManagement.js');

?>

<div class="cols-9">
    <table id="<?= CHtml::modelName($element) . '_readings_' . $side ?>"
           class="cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-3">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <?php
        // show input after validation fail
        $instrument_model = OEModule\OphCiExamination\models\OphCiExamination_Instrument::model();
        if ($_POST && isset($_POST['OEModule_OphCiExamination_models_HistoryIOP']["{$side}_values"])) {
            foreach ($_POST['OEModule_OphCiExamination_models_HistoryIOP']["{$side}_values"] as $index => $value) {
                $recorded_value = new models\OphCiExamination_IntraocularPressure_Value();
                if (isset($value['qualitative_reading_id'])) {
                    $recorded_value->instrument = models\OphCiExamination_Instrument::model()->findByPk($value['instrument_id']);
                    $recorded_value = new models\OphCiExamination_IntraocularPressure_Value();
                    $recorded_value->instrument->scale = models\OphCiExamination_Qualitative_Scale::model()->findByAttributes(['name' => 'digital']);
                } else {
                    $recorded_value->reading_id = $value['reading_id'];
                }

                $this->render(
                    "HistoryIOP_event_edit_reading",
                    array(
                        'element' => $element,
                        'form' => $form,
                        'side' => $side,
                        'index' => $index,
                        'time' => substr($value['reading_time'], 0, 5),
                        'instrumentId' => $value['instrument_id'],
                        'instrumentName' => $instrument_model->findByPk($value['instrument_id'])->name,
                        'value' => $recorded_value,
                        'examinationDate' => $value['examination_date'],
                    )
                );

            }
        }
        ?>
        </tbody>
    </table>

    <table id="<?= $model_name ?>_entry_table">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-5">
            <col class="cols-1">
        </colgroup>
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
                    <td><?=$iop_value->instrument->name?></td>
                    <td><?= $iop_value->instrument->scale ? $iop_value->qualitative_reading->name : $iop_value->reading->name.'mm Hg' ?></td>
                    <td colspan="2">
                        <i class="oe-i time small no-click pad-right"></i>
                        <?=$iop_value->reading_time?>
                        <span class="oe-date"><?=date('d M Y', strtotime($date));?></span>
                    </td>
                    <td>
                        <?php if (isset($iop->{$side.'_comments'})) { ?>
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
        array(
            'element' => $element,
            'form' => $form,
            'side' => $side,
            'time' => '{{time}}',
            'index' => '{{index}}',
            'instrument' => '{{instrument}}',
            'instrumentId' => '{{instrumentId}}',
            'instrumentName' => '{{instrumentName}}',
            'examinationDate' => '{{examinationDate}}',
            'value' => new models\OphCiExamination_IntraocularPressure_Value(),
        )
    );
    ?>
</script>


<script type="text/javascript">
    $(function () {
        var side = $('.<?= CHtml::modelName($element) ?> .<?=$side?>-eye');

        new OpenEyes.UI.AdderDialog({
            id: 'add-to-iop',
            openButton: side.find('.js-add-select-search'),
            itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                array_map(function ($instrument) {
                    return ['label' => $instrument->name, 'id' => $instrument->id];
                },
                    OEModule\OphCiExamination\models\OphCiExamination_Instrument::model()->findAllByAttributes(['visible' => 1]))
            ) ?>, {'multiSelect': true})],
            onReturn: function (adderDialog, selectedItems) {
                for (let i = 0; i < selectedItems.length; i++) {
                    OphCiExamination_IntraocularPressure_addReading(
                        '<?=$side?>',
                        selectedItems[i]['id'],
                        selectedItems[i]['label']);
                    let $table = $("#OEModule_OphCiExamination_models_HistoryIOP_readings_" + '<?=$side?>');
                    let $table_row = $table.find("tr:last");
                    let $scale_td = $table_row.find("td.scale_values");
                    let index = $table_row.data('index');

                    getScaleDropdown(selectedItems[i]['id'], $scale_td, index, '<?=$side?>');
                };

                // activate the datePicker
                $('.iop-date').datepicker({ dateFormat: 'dd/mm/yy' });

                return true;
            },
        });
    });

    function OphCiExamination_IntraocularPressure_addReading(side, instrumentId, instrumentName) {
        var table = $("#OEModule_OphCiExamination_models_HistoryIOP_readings_" + side);
        var indices = table.find('tr').map(function () {
            return $(this).data('index');
        });

        table.find("tbody").append(
            Mustache.render(
            template = $("#OEModule_OphCiExamination_models_HistoryIOP_reading_template_" + side).text(),
            {
                index: indices.length ? Math.max.apply(null, indices) + 1 : 0,
                time: (new Date).toTimeString().substr(0, 5),
                instrumentId: instrumentId,
                instrumentName: instrumentName
            }
        ));

        table.show();
    }

    // TODO: copy pasted from IOP.js; need to move them into a separate .js file
    $(document).ready(function () {
        function deleteReading(e) {
            var table = $(this).closest('table');
            if (table.find('tbody tr').length <= 1) table.hide();

            if ($(this).closest('tr').data('side') == 'left') {
                setCurrentManagementIOP('left');
            } else {
                setCurrentManagementIOP('right');
            }

            $(this).closest('tr').remove();

            return false;
        }

        $("#OEModule_OphCiExamination_models_HistoryIOP_readings_right").on("click", "i.trash", null, deleteReading);
        $("#OEModule_OphCiExamination_models_HistoryIOP_readings_right").on("click", "i.trash", null, deleteReading);

        $('select.IOPinstrument').die('change').live('change', function (e) {
            e.preventDefault();

            var instrument_id = $(this).val();

            var scale_td = $(this).closest('tr').children('td.scale_values');
            var index = $(this).closest('tr').data('index');
            var side = $(this).closest('tr').data('side');

            getScaleDropdown(instrument_id, scale_td, index, side);
        });
    });

    function getScaleDropdown(instrument_id, scale_td, index, side){
        $.ajax({
            'type': 'GET',
            'url': baseUrl + '/OphCiExamination/default/getScaleForInstrument?name=OEModule_OphCiExamination_models_HistoryIOP' +
                '&instrument_id=' + instrument_id + '&side=' + side + '&index=' + index,
            'success': function (html) {
                if (html.length > 0) {
                    scale_td.html(html);
                    scale_td.show();
                    scale_td.prev('td').hide();
                } else {
                    scale_td.html('');
                    scale_td.hide();
                    scale_td.prev('td').show();
                }
            }
        });
    }
</script>
