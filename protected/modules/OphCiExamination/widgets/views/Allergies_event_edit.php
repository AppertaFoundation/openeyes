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
<script type="text/javascript" src="<?= $this->getJsPublishedPath('Allergies.js') ?>"></script>
<?php
$model_name = CHtml::modelName($element);

$missing_req_allergies = $this->getMissingRequiredAllergies();
$required_allergy_ids = array_map(function ($r) {
    return $r->id;
}, $this->getRequiredAllergies());
?>

<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_element">
    <input type="hidden" name="<?= $model_name ?>[present]" value="1" />
    <div class="cols-11 align-left" >
            <div class="cols-full" id="<?= $model_name ?>_no_allergies_wrapper" <?= $this->isAllergiesSetYes($element) ? 'style="display:none"' : '' ?>>
        <label class="inline highlight"  for="<?= $model_name ?>_no_allergies" id="<?= $model_name ?>_no_allergies_label">
            <input type="hidden" name="<?=$model_name?>[no_allergies]" value="off">
            <?=\CHtml::checkBox($model_name . '[no_allergies]', $element->no_allergies_date ? true : false)?>
            No allergies
        </label>
        
</div>
    <div class="cols-full">
    <table id="<?= $model_name ?>_entry_table" class="cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-3">
            <col class="cols-4">
            <col class="cols-2">
        </colgroup>
        <tbody>
        <?php
            $row_count = 0;
        foreach ($missing_req_allergies as $entry) {
            $this->render(
                'AllergyEntry_event_edit',
                array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'removable' => false,
                    'allergies' => $element->getAllergyOptions(),
                    'field_prefix' => $model_name . '[entries][' . ($row_count) . ']',
                    'row_count' => $row_count,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count),
                    'has_allergy' => $entry->has_allergy,
                )
            );
            $row_count++;
        }

        foreach ($element->entries as $i => $entry) {
            $this->render(
                'AllergyEntry_event_edit',
                array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'removable' => !in_array($entry->allergy_id, $required_allergy_ids),
                    'allergies' => $element->getAllergyOptions(),
                    'field_prefix' => $model_name . '[entries][' . ($row_count) . ']',
                    'row_count' => $row_count,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count),
                    'has_allergy' => $entry->has_allergy,
                )
            );
            $row_count++;
        }
        ?>
            </tbody>
        </table>
    </div>
</div>
    <div class="add-data-actions flex-item-bottom" id="history-allergy-popup">
        <button class="button hint green js-add-select-search" id="add-allergy-btn" type="button"><i
                    class="oe-i plus pro-theme"></i></button>
    </div>
</div>

<script type="text/template" id="<?= CHtml::modelName($element) . '_entry_template' ?>" style="display:none">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\AllergyEntry();
    $this->render(
        'AllergyEntry_event_edit',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'model_name' => $model_name,
            'removable' => true,
            'allergies' => $element->getAllergyOptions(),
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'posted_not_checked' => false,
            'values' => array(
                'id' => '',
                'allergy_id' => '{{allergy_id}}',
                'allergy_display' => '{{allergy_display}}',
                'other' => '{{other}}',
                'comments' => null,
                'has_allergy' => (string)\OEModule\OphCiExamination\models\AllergyEntry::$PRESENT,
            ),
        )
    );
    ?>
</script>

<script type="text/javascript">

    $(function () {
      var allergyController;
      $(document).ready(function () {
            allergyController = new OpenEyes.OphCiExamination.AllergiesController({
                element: $('#<?=$model_name?>_element'),
                allAllergies: <?= CJSON::encode(CHtml::listData(\OEModule\OphCiExamination\models\OphCiExaminationAllergy::model()->findAll(), 'id', 'name')) ?>
            });
      });

      new OpenEyes.UI.AdderDialog({
        openButton: $('#add-allergy-btn'),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($allergy) {
                return ['label' => $allergy->name, 'id' => $allergy->id];
            }, $element->getAllergyOptions())
        )?>, {'multiSelect': true})],
        onReturn: function (adderDialog, selectedItems) {
            allergyController.addEntry(selectedItems);
            allergyController.showTable();
            return true;
        }
      });
    });

</script>
