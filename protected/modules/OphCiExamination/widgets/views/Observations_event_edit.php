<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php use OEModule\OphCiExamination\models\ObservationEntry; ?>
<?php
$model_name = CHtml::modelName($element);
?>

<div class="element-fields full-width">
    <div id="observations-container">
    <?php if (!count($element->entries)) { ?>
        <div class="data-value not-recorded left" style="text-align: left;">
            No entries recorded
        </div>
        <?php
    } else {
        $entry_index = 0;

        foreach ($element->entries as $entry) {
            $this->render(
                'ObservationEntry_event_edit',
                array(
                    'entry' => $entry,
                    'entry_index' => $entry_index,
                    'field_prefix' => $model_name . "[entries][$entry_index]",
                )
            );

            $entry_index++;
        }
    }
    ?>
    </div>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green js-add-select-search" id="add-observation-btn" type="button"><i
                    class="oe-i plus pro-theme"></i></button>
    </div>
</div>

<script type="text/template" id="<?= CHtml::modelName($element) . '_entry_template' ?>" class="entry-template hidden">
<?php
    $empty_entry = new ObservationEntry;

$this->render(
    'ObservationEntry_event_edit',
    array(
        'entry' => $empty_entry,
        'entry_index' => '{{entry_index}}',
        'field_prefix' => $model_name . '[entries][{{entry_index}}]',
        'values' => array(
            'id' => '',
            'element_id' => '',
            'blood_pressure_systolic' => '',
            'blood_pressure_diastolic' => '',
            'blood_glucose' => '',
            'weight' => '',
            'o2_sat' => '',
            'hba1c' => '',
            'height' => '',
            'pulse' => '',
            'temperature' => '',
            'taken_at' => '{{taken_at}}',
        ),
    )
);
?>
</script>

<script type="text/javascript">
    $(document).ready(function () {
        let heightElements = $(".bmi-height-field");
        let weightElements = $(".bmi-weight-field");
        let bmiContainers = $('.bmi-container');

        for (i = 0; i < bmiContainers.length; i++) {
            let height = heightElements[i].value;
            let weight = weightElements[i].value;

            getAndSetBMI(height, weight, bmiContainers[i]);
        }

        $('#observations-container .data-group .trash').click(function() {
                $(this).parent().parent().remove();
        });

        $('.bmi-keyup-event input[type="text"]').keyup(function () {
            let index = this.dataset.bmiIndex;

            let height = $(".bmi-height-field[data-bmi-index=" + index + "]").val();
            let weight = $(".bmi-weight-field[data-bmi-index=" + index + "]").val();
            let bmiContainer = $(".bmi-container[data-bmi-index=" + index + "]");

            getAndSetBMI(height, weight, bmiContainer);
        });

        $('#add-observation-btn').click(function() {
            let template = $("#<?= CHtml::modelName($element) . '_entry_template' ?>").text();
            let data = {};

            let now = new Date();
            let hours = now.getHours() + '';
            let minutes = now.getMinutes() + '';

            hours = hours.length === 1 ? '0' + hours : hours;
            minutes = minutes.length === 1 ? '0' + minutes : minutes;

            let taken_at = hours + ':' + minutes;

            data.entry_index = OpenEyes.Util.getNextDataKey($("#observations-container div"), 'key');
            data.taken_at = taken_at;

            $("#observations-container .not-recorded").hide();

            $("#observations-container").append(Mustache.render(template, data));

            $('#observations-container .data-group[data-key="' + data.entry_index + '"] .trash').click(function() {
                $(this).parent().parent().remove();
            });

            $('.bmi-keyup-event input[type="text"][data-bmi-index="' + data.entry_index + '"]').keyup(function () {
                let index = this.dataset.bmiIndex;

                let height = $(".bmi-height-field[data-bmi-index=" + index + "]").val();
                let weight = $(".bmi-weight-field[data-bmi-index=" + index + "]").val();
                let bmiContainer = $(".bmi-container[data-bmi-index=" + index + "]");

                getAndSetBMI(height, weight, bmiContainer);
            });
        });

        function getAndSetBMI(height, weight, bmiContainer) {
            bmiContainer = $(bmiContainer);

            bmiContainer.removeClass('highlighter good warning');

            let bmi = 0;
            let result = 'N/A';

            if ((height > 0) && (weight > 0)) {
                bmi = bmi_calculator(weight, height);
                result = bmi.toFixed(2) || 'N/A';

                let resultFloat = parseFloat(result);

                if (resultFloat < 18.5 || resultFloat >= 30) {
                    bmiContainer.addClass('highlighter warning');
                } else {
                    bmiContainer.addClass('highlighter good');
                }
            }

            bmiContainer.text(result);
        }
    });
</script>
