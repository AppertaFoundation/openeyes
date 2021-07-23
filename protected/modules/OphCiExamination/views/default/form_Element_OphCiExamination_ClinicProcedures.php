<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
use OEModule\OphCiExamination\models\OphCiExamination_ClinicProcedure;
use OEModule\OphCiExamination\models\OphCiExamination_ClinicProcedures_Entry;
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/ClinicProcedures.js", CClientScript::POS_HEAD);
$model_name = CHtml::modelName($element);
$past_procedures = $this->getPastClinicProcedures();
?>

<div class="element-fields full-width">
    <div class="flex-t">
        <div class="cols-11">
            <table id="<?= $model_name ?>_table" class="cols-full" >
                <colgroup>
                    <col class="cols-4">
                    <col>
                    <col>
                    <col>
                    <col class="cols-1">
                    <col class="cols-1">
                    <col class="cols-3">
                </colgroup>
                <tbody>
                <?php if (count($element->entries)) {
                    $row_count = 0;
                    foreach ($element->entries as $entry) {
                        $this->renderPartial(
                            'ClinicProcedures_Entry_edit',
                            [
                                'entry' => $entry,
                                'form' => $form,
                                'model_name' => $model_name,
                                'field_prefix' => $model_name . '[entries]['.($row_count).']',
                                'row_count' => ($row_count),
                            ]
                        );
                        $row_count++;
                    }
                } ?>
                </tbody>
            </table>
        </div>
        <div class="add-data-actions flex-item-bottom" >
            <button class="adder js-add-select-btn" type="button" id="add-clinic-procedures"></button>
        </div>
    </div>
    <hr class="divider">
    <div class="collapse-data" id="past_clinic_procedures">
        <div class="collapse-data-header-icon expand">
            Previous clinic procedures
        </div>
        <div class="collapse-data-content" style="display: none;">
            <?php foreach ($past_procedures as $procedure) {
                $this->renderPartial(
                    'ClinicProcedures_past_procedures',
                    [
                        'procedure_entries' => OphCiExamination_ClinicProcedures_Entry::model()->findAll('element_id = ?', [$procedure['id']]),
                    ]
                );
            } ?>
        </div>
    </div>
</div>
<script type="text/template" id="<?= CHtml::modelName($element) . '_template' ?>" class="hidden">
    <?php
    $empty_procedure = new OphCiExamination_ClinicProcedures_Entry();
    $this->renderPartial(
        'ClinicProcedures_Entry_edit',
        [
            'entry' => $empty_procedure,
            'form' => $form,
            'model_name' => $model_name,
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'values' => [
                'id' => '',
                'procedure' => '{{procedure}}',
                'procedure_id' => '{{procedure_id}}',
                'eye_id' => '{{eye_id}}',
                'outcome_time' => '{{outcome_time}}',
                'date' => '{{date}}',
                'comments' => '{{comments}}',
            ]
        ]
    );
    ?>
</script>

<?php $clinic_procedures = OphCiExamination_ClinicProcedure::model()->getClinicProceduresItemSet(); ?>

<script type="text/javascript">
    $(function () {
        let controller;

        $(document).ready(function () {
            controller = new OpenEyes.OphCiExamination.ClinicProceduresController();

            new OpenEyes.UI.AdderDialog({
                openButton: $('#add-clinic-procedures'),
                itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($procedure) {
                        return ['label' => $procedure['items'], 'id' => $procedure['id']];
                    }, $clinic_procedures)
                )?>, {'multiSelect': true})],
                liClass: 'add-options multi',
                onReturn: function (adderDialog, selectedItems) {
                    if (selectedItems.length < 1) {
                        return false;
                    }
                    controller.addEntry(selectedItems);
                    return true;
                }
            });
        })
    });
</script>
