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
\Yii::app()->clientScript->registerScriptFile($url . '/js/IntraocularPressure.js');
?>

<div class="cols-9">
    <table id="<?= CHtml::modelName($element) . '_readings_' . $side ?>"
           class="cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-2">
        </colgroup>
        <thead>
        <tr>
            <th>Instrument</th>
            <th>mm Hg</th>
            <th>Time</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>

<!--    TODO: remove display: NONE  -->
    <table id="<?= $model_name ?>_entry_table" class="cols-10" style="display: none">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-2">
            <col class="cols-2">
        </colgroup>
        <tbody>
        <tr class="divider">
            <td>Goldmann</td>
            <td>11 mmHg</td>
            <td>
                <input class="fixed-width-small" value="12:15">
                <?php
                $this->widget('application.widgets.DatePicker', array(
                    'element' => new \OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Value(),
                    'name' => 'reading_time',
                    'field' => 'reading_time',
                    'options' => array('maxDate' => 'today'),
                    'htmlOptions' => array(
                        'form' => null, // TODO get form
                        'nowrapper' => true,
                        'class' => 'js-iop-date-input'
                    ),
                    'layoutColumns' => array(
                        'label' => 2,
                        'field' => 2,
                    ),
                ));
                ?>
            </td>

            <td>
                <div class="cols-full ">
                    <button class="button  js-add-comments" data-input="block1" style="">
                        <i class="oe-i comments small-icon "></i>
                    </button>
                    <div id="block1" class="cols-full" style="display: none;">
                        <div class=" flex-layout flex-left">
                        <textarea placeholder="Comments" autocomplete="off" rows="1" class="js-input-comments cols-full "
                                  style="overflow-x: hidden; overflow-wrap: break-word;"></textarea>
                            <i class="oe-i remove-circle small-icon pad-left  js-remove-add-comments"></i>
                        </div>
                    </div>
                </div>
            </td>
            <td><i class="oe-i trash"></i></td>
        </tr>

        <?php foreach ($pastIOPs as $iop) { ?>
            <?php $date = $iop->event->event_date; ?>
            <?php foreach ($iop->{$side.'_values'} as $iop_value) { ?>
                <tr>
                    <td><?=$iop_value->instrument->name?></td>
                    <td><?=$iop_value->reading->value?>mmHg</td>
                    <td>
                        <i class="oe-i time small no-click pad-right"></i>
                        <?=$iop_value->reading_time?>
                        <span class="oe-date"><?=date('d M Y', strtotime($date));?></span>
                    </td>
                    <td colspan="2">
                        <i class="oe-i comments-added medium js-has-tooltip" data-tooltip-content="Comments shown here...">
                        </i>
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
                $('.iop-date').datepicker();

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
</script>
