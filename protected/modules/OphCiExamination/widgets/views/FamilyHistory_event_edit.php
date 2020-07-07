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

<script type="text/javascript" src="<?= $this->getJsPublishedPath('FamilyHistory.js') ?>"></script>
<div class="element-fields flex-layout full-width">
    <?php $model_name = CHtml::modelName($element); ?>
    <div class="row"
         style="display: <?= count($element->entries) ? ' none' : ''?>"
         id="<?=$model_name?>_no_family_history_wrapper">
      <label class="inline highlight" for="<?=$model_name?>_no_family_history" id="<?=$model_name?>_no_family_history">
        <?=\CHtml::checkBox($model_name .'[no_family_history]', $element->no_family_history_date ? true : false); ?>
          No family history
      </label>
    </div>

  <input type="hidden" name="<?= $model_name ?>[present]" value="1" />

  <table id="<?= $model_name ?>_entry_table"
         style="display:  <?= !count($element->entries) ? 'none' :''?>"
         class="cols-10">
        <colgroup>
            <col>
            <col>
            <col>
            <col class="cols-4">
            <col class="cols-1">
        </colgroup>
      <thead>
      <tr>
          <th>Relative</th>
          <th>Side</th>
          <th>Condition</th>
          <th></th>
          <th></th>
      </tr>
      </thead>
      <tbody>
            <?php
            $row_count = 0;
            foreach ($element->entries as $entry) {
                $this->render(
                    'FamilyHistory_Entry_event_edit',
                    array(
                      'entry' => $entry,
                      'form' => $form,
                      'model_name' => $model_name,
                      'editable' => true,
                      'relative_options' => $element->getRelativeOptions(),
                      'side_options' => $element->getSideOptions(),
                      'condition_options' => $element->getConditionOptions(),
                      'field_prefix' => $model_name . '[entries][' . ($row_count) . ']',
                      'row_count' => $row_count,
                    )
                );
                $row_count++;
            }
            ?>
      </tbody>
  </table>

    <button class="button hint green js-add-new-row"
            id="add-family-history-button"
            style="display: <?= $element->no_family_history_date ? 'none' : 'block' ?>"
            type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
</div>

<script type="text/template" id="<?= CHtml::modelName($element).'_entry_template' ?>" class="hidden">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\FamilyHistory_Entry();
    $this->render(
        'FamilyHistory_Entry_event_edit',
        array(
            'entry' => $empty_entry,
            'form' => $form,
            'model_name' => $model_name,
            'editable' => true,
            'relative_options' => $element->getRelativeOptions(),
            'side_options' => $element->getSideOptions(),
            'condition_options' => $element->getConditionOptions(),
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'values' => array(
                'id' => '',
                'relative_id' => '{{relative_id}}',
                'relative_display' => '{{relative_display}}',
                'other_relative' => null,
                'side_id' => '{{side_id}}',
                'side_display' => '{{side_display}}',
                'condition_id' => '{{condition_id}}',
                'condition_display' => '{{condition_display}}',
                'other_condition' => null,
                'comments' => '{{comments}}',
            )
        )
    );
    ?>
</script>
<script type="text/javascript">
    $(document).ready(function() {
       var FamilyHistoryController = new OpenEyes.OphCiExamination.FamilyHistoryController();
       var templateText = $('#'+<?= CJSON::encode(CHtml::modelName($element).'_entry_template') ?>).text();
        <?php
        $relative_options = $element->getRelativeOptions();
        $side_options = $element->getSideOptions();
        $condition_options = $element->getConditionOptions();
        ?>

       new OpenEyes.UI.AdderDialog({
         id: 'add-family-history',
         openButton: $('#add-family-history-button'),
         itemSets: [
           new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
               array_map(function ($relative_item) {
                 return ['label' => $relative_item->name, 'id' => $relative_item->id];
               }, $relative_options)
           ) ?>, {'header':'Relative'}),
           new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
               array_map(function ($side_item) {
                   return ['label' => $side_item->name, 'id' => $side_item->id];
               }, $side_options)
           ) ?>, {'header':'Side'}),
           new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
               array_map(function ($condition_item) {
                   return ['label' => $condition_item->name, 'id' => $condition_item->id];
               }, $condition_options)
           ) ?>, {'header':'Condition'})
         ],
         onReturn: function (adderDialog, selectedItems) {
           data = {};
           var list = ['relative', 'side', 'condition'];
           for (var i in list){
             data[list[i]+'_id'] =  selectedItems[i]['id'];
             data[list[i]+'_display'] =  selectedItems[i]['label'];
           }
           data['row_count'] = OpenEyes.Util.getNextDataKey('#OEModule_OphCiExamination_models_FamilyHistory_entry_table tbody tr', 'key');
           var newRow =  Mustache.render(
             template = templateText,
             data
           );
           var row_tem = $(newRow);
           if (data['relative_display'] === 'Other') {
             row_tem.find('.other_relative_wrapper').show();
           }
           if (data['condition_display'] === 'Other') {
             row_tem.find('.other_condition_wrapper').show();
           }
           $('#OEModule_OphCiExamination_models_FamilyHistory_entry_table').find('tbody').append(row_tem);
           return true;
         },
         onClose: function () {
          FamilyHistoryController.showNoHistory();
         }
       });
    });
</script>
