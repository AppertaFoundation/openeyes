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

use OEModule\OphCiExamination\models\HistoryRisksEntry;

?>

<?php
$model_name = CHtml::modelName($element);
$risks_options = $this->getRiskOptions();
$missing_req_risks = $this->getMissingRequiredRisks();
$required_risk_ids = array_map(function ($r) {
    return $r->id;
}, $this->getRequiredRisks());
$is_draft_event = !empty(Yii::app()->request->getParam('draft_id'));
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryRisks.js') ?>"></script>

<div class="element-fields flex-layout full-width" id="<?= $model_name ?>_element">
  <div class="data-group cols-10">

    <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
    <div class="cols-5 align-left <?= (count($element->entries) + count($missing_req_risks)) ? ' hidden' : '' ?> <?= $model_name ?>_no_risks_wrapper">
        <label class="inline highlight" for="<?= $model_name ?>_no_risks">
            <?= \CHtml::checkBox(
                $model_name . '[no_risks]',
                $element->no_risks_date ? true : false,
                array('class' => $model_name . '_no_risks')
            ); ?>
            No relevant risks
        </label>
    </div>
    <table
        class="<?= $model_name ?>_entry_table cols-full">
      <colgroup>
        <col class="cols-3">
        <col class="cols-3">
        <col class="cols-4">
        <col class="cols-2">
      </colgroup>
      <!-- NOTE: Headers are needed for test automation. Do not remove -->
      <thead style="display:none;">
      <tr>
        <th>Name</th>
        <th>Checked</th>
        <th>Comment</th>
        <th>Action</th>
      <tr>
      </thead>
      <tbody data-test="risks_body">
        <?php if (count($element->entries) || count($missing_req_risks)) : ?>
            <?php
            $row_count = 0;
            foreach ($missing_req_risks as $entry) {
                $this->render(
                    'HistoryRisksEntry_event_edit',
                    array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                    'row_count' => $row_count,
                    'removable' => false,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count),
                    'is_draft_event' => $is_draft_event,
                    )
                );
                $row_count++;
            } ?>
            <?php
            foreach ($element->entries as $entry) {
                $this->render(
                    'HistoryRisksEntry_event_edit',
                    array(
                    'entry' => $entry,
                    'form' => $form,
                    'model_name' => $model_name,
                    'field_prefix' => $model_name . '[entries][' . $row_count . ']',
                    'row_count' => $row_count,
                    'removable' => !in_array($entry->risk_id, $required_risk_ids),
                    'risks' => $risks_options,
                    'posted_not_checked' => $element->widget->postedNotChecked($row_count),
                    'is_draft_event' => $is_draft_event,
                    )
                );
                $row_count++;
            } ?>
        <?php endif ?>
      </tbody>
    </table>
  </div>
  <div class="add-data-actions flex-item-bottom" id="add-history-risk-popup"
       style="display: <?php echo $element->no_risks_date ? 'none' : ''; ?>">
    <button id="show-add-risk-popup" class="button hint green js-add-select-search" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>
  </div>
  <script type="text/template" class="<?= CHtml::modelName($element) . '_entry_template' ?> hidden">
        <?php
        $empty_entry = new \OEModule\OphCiExamination\models\HistoryRisksEntry();
        $this->render(
            'HistoryRisksEntry_event_edit',
            array(
              'entry' => $empty_entry,
              'form' => $form,
              'model_name' => $model_name,
              'field_prefix' => $model_name . '[entries][{{row_count}}]',
              'row_count' => '{{row_count}}',
              'removable' => true,
              'risks' => $risks_options,
              'posted_not_checked' => false,
              'is_draft_event' => $is_draft_event,
                'values' => array(
                  'id' => '',
                  'risk_id' => '{{risk_id}}',
                  'risk_display' => '{{risk_display}}',
                  'other' => null,
                  'comments' => '{{comments}}',
                  'has_risk' => (string)HistoryRisksEntry::$PRESENT,
              ),
            )
        );
        ?>
  </script>
</div>


<script type="text/javascript">
  var controller;
  $(document).ready(function () {
    controller = new OpenEyes.OphCiExamination.HistoryRisksController({
      element: $('#<?=$model_name?>_element')
    });
  });

  new OpenEyes.UI.AdderDialog({
    openButton: $('#add-history-risk-popup'),
    itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
        array_map(function ($risk) {
            return ['label' => $risk->name, 'id' => $risk->id];
        }, $risks_options)
    ) ?>, {multiSelect: true, id: "risk_dialog_list"}),
    ],
    onOpen: function (adderDialog) {
      adderDialog.popup.find('li').each(function() {
        let risk_id = $(this).data('id');
        var alreadyUsed = controller.$table.find('input[type="hidden"][id$="risk_id"][value="' + risk_id + '"]').length > 0;
        $(this).toggle(!alreadyUsed || $(this).data('label') === 'Other');
      });
    },
    onReturn: function (adderDialog, selectedItems) {
      for (let i = 0; i < selectedItems.length; ++i) {
        controller.addEntry(selectedItems[i].id, selectedItems[i].label);
      }
    },
  });
</script>
