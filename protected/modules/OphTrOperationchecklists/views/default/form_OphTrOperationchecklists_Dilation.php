<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\OphCiExamination_Dilation_Drugs;

/**
 * @var OphTrOperationchecklists_Dilation $name_stub
 * @var OphTrOperationchecklists_Dilation $model
 */
?>
<?php
$key = 0;
$dilation_drugs = OphCiExamination_Dilation_Drugs::model()->findAll();
$dilation = null;
foreach ($results as $result) {
    if (isset($result->dilation)) {
        $dilation = $result->dilation;
    }
}
$model = $dilation ?? new $model;
$dilation_drugs_order = array();
$dilation_drugs_status = array();
foreach ($dilation_drugs as $d_drug) {
    $dilation_drugs_order[$d_drug['id']] = $d_drug['display_order'];
    $dilation_drugs_status[$d_drug['id']] = $d_drug['is_active'];
}
$name_stub = $name_stub . '[' . $question->id . ']' . '[' . $relation . ']';
?>
<?php
echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][mandatory]', $question->mandatory);
if (isset($results)) {
    echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][id]', @$results[$question->id]->id);
}
echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults]['. $question->id . '][question_id]', $question->id);
echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][answer_id]', @$results[$question->id]->answer_id, array('id'=> 'result_answer_id' . 'Element_OphTrOperationchecklists_Admission[checklistResults]' . $question->id));
echo \CHtml::hiddenField('Element_OphTrOperationchecklists_Admission[checklistResults][' . $question->id . '][answer]', @$results[$question->id]->answer, array('id'=> 'result_answer' . 'Element_OphTrOperationchecklists_Admission[checklistResults]' . $question->id));
?>
<td>
    <?= $question->question; ?>
</td>
<td>
    <label class="inline highlight">
        <?= \CHtml::checkBox(
            $name_stub. '[is_not_required]',
            $model->is_not_required ? true : false
        ); ?>
        <?= $model->attributeLabels()['is_not_required'] ?>
    </label>
</td>
<td>
    <div class="element-fields edit-Dilation">
        <div class="active-form data-group flex-layout">
            <div class="cols-9">
                <table id="<?= CHtml::modelName($model) ?>_treatments" class="cols-full dilation_table">
                    <tbody class="plain" id="dilation">
                    <?php
                    if (isset($model->{'treatments'})) {
                        foreach ($model->{'treatments'} as $treatment) {
                            $this->renderPartial(
                                'form_OphTrOperationchecklists_DilationTreatment',
                                array(
                                    'name_stub' => CHtml::modelName($name_stub) . '[treatments]',
                                    'treatment' => $treatment,
                                    'key' => $key,
                                    'drug_name' => $treatment->drug->name,
                                    'drug_id' => $treatment->drug_id,
                                    'data_order' => $treatment->drug->display_order,
                                )
                            );
                            ++$key;
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="add-data-actions flex-item-bottom">
                <button class="button hint green js-add-select-search" type="button">
                    <i class="oe-i plus pro-theme"></i>
                </button>
                <div id="add-to-dilation" class="oe-add-select-search" style="display: none;" type="button">
                    <div class="close-icon-btn">
                        <i class="oe-i remove-circle medium"></i>
                    </div>
                    <button class="button hint green add-icon-btn" type="button">
                        <i class="oe-i plus pro-theme"></i>
                    </button>
                    <table class="select-options">
                        <tbody>
                        <tr>
                            <td>
                                <div class="flex-layout flex-top flex-left">
                                    <ul class="add-options" data-multi="false" data-clickadd="false">
                                        <?php foreach ($model->getAllDilationDrugs() as $id => $drug) : ?>
                                            <?php if ($dilation_drugs_status[$id]) : ?>
                                                <li data-str="<?= $id ?>"
                                                    data-order="<?= $dilation_drugs_order[$id] ?>"><?= $drug ?></li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script id="dilation_treatment_template" type="text/html">
        <?php
        $this->renderPartial(
            'form_OphTrOperationchecklists_DilationTreatment',
            array(
                'name_stub' => CHtml::modelName($name_stub) . '[treatments]',
                'key' => '{{key}}',
                'drug_name' => '{{drug_name}}',
                'drug_id' => '{{drug_id}}',
                'treatment_time' => '{{treatment_time}}',
                'data_order' => '{{data_order}}',
            )
        ); ?>
    </script>
</td>

<script type="text/javascript">
    $(document).ready(function() {
        let $isRequiredId = $('[name="<?=$name_stub?>[is_not_required]"]');
        let $editDilation = $('.edit-Dilation');

        toggleDilation($isRequiredId, $editDilation);

        $isRequiredId.click(function(){
            toggleDilation(this, $editDilation);
        });
    });

    function toggleDilation($element, $editDilation) {
        if($($element).prop("checked") === true){
            $editDilation.hide();
            $('.edit-Dilation #dilation').empty();
        }
        if($($element).prop("checked") === false){
            $editDilation.show();
        }
    }

    $(function () {
        let $dilation = $('.edit-Dilation');
        let popup = $dilation.find('#add-to-dilation');
        let table = $dilation.find('.dilation_table');
        let addList = popup.find('ul');

        table.delegate('.removeTreatment', 'click', function (e) {
            let wrapper = $(this).closest('.js-element-eye');
            let row = $(this).closest('tr');
            let id = row.find('.drugId').val();
            addList.find('li[data-str=\'' + id + '\']').show();
            row.remove();
            if ($('.dilation_table tbody tr', wrapper).length === 0) {
                $('.dilation_table', wrapper).hide();
                $('.timeDiv', wrapper).hide();
            }
            e.preventDefault();
        });

        getNextKey = function () {
            let keys = $('.main-event .edit-Dilation .dilationTreatment').map(function (index, el) {
                return parseInt($(el).attr('data-key'));
            }).get();
            if (keys.length) {
                return Math.max.apply(null, keys) + 1;
            } else {
                return 0;
            }
        };

        function addTreatment(element) {
            let drug_id = $(element).attr('data-str');
            let data_order = $(element).attr('data-order');
            if (drug_id) {
                let drug_name = $(element).text();
                let template = $('#dilation_treatment_template').html();
                let data = {
                    "key": getNextKey(),
                    "drug_name": drug_name,
                    "drug_id": drug_id,
                    "data_order": data_order,
                    "treatment_time": (new Date).toTimeString().substr(0, 5)
                };
                let form = Mustache.render(template, data);
                table.show();
                $(element).closest('.js-element-eye').find('.timeDiv').show();
                $('tbody', table).append(form);
            }
        }

        popup.find('.add-icon-btn').click(function () {
            popup.find('li.selected').each(function () {
                addTreatment($(this));
                $(this).removeClass('selected');
            });
        });

        setUpAdder(
            popup,
            'multi',
            null,
            $dilation.find('.js-add-select-search'),
            popup.find('.add-icon-btn'),
            popup.find('.close-icon-btn')
        );
    });
</script>