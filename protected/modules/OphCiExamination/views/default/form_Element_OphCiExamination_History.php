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

<?php use OEModule\OphCiExamination\models\HistoryMacro; ?>
<div class="element-fields flex-layout full-width ">
  <div class="cols-10 flex-layout col-gap">
    <div class="cols-half">
        <?php echo $form->textArea(
            $element,
            'description',
            array('rows' => '1', 'class' => 'autosize', 'nowrapper' => true),
            false,
            array('placeholder' => 'Enter comments here', 'data-test' => 'history-description')
        ) ?>
    </div>
    <div class="cols-half">
      <div class="data-label">Previous Management</div>
        <?php
        $exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
      // Get the latest summary from the array although the method seems
      // to currently only return the latest summary.
        $summary = $exam_api->getManagementSummaries($this->patient);
        $summary = $summary ? $summary[0] : null;
        ?>
      <strong>
        <?php if ($summary) : ?>
            <?= $summary->service ?> <?= implode(" ", $summary->date) ?> (<?= $summary->user ?> <span
        class="js-has-tooltip fa oe-i info small"
        data-tooltip-content="This is the user that last modified the Examination event. It is not necessarily the person that originally added the comment."></span>):</strong> <?= $summary->comments ?>
        <?php else : ?>
        No previous managements recorded.
        <?php endif; ?>
      </strong>

    </div>
  </div>
  <div class="add-data-actions flex-item-bottom">
    <button class="button hint green js-add-standard-set" type="button">
        Add template
    </button>
    <button class="button hint green js-add-select-search" type="button" id="show-add-to-history">
      <i class="oe-i plus pro-theme"></i>
    </button>
  </div>
</div>
<script type="text/html" id="previous-management-template">
  <strong>{{subspecialty}} {{event_date}} ({{last_modified_user_display}} <span
        class="js-has-tooltip fa oe-i info small"
        data-tooltip-content="This is the user that last modified the Examination event. It is not necessarily the person that originally added the comment."></span>):</strong> {{comments}}
</script>

<?php
$firm = Firm::model()->findByPk(Yii::app()->session['selected_firm_id']);
$itemSets = [];
foreach ($this->getAttributes($element, $firm->serviceSubspecialtyAssignment->subspecialty_id) as $attribute) {
    $items = array();

    foreach ($attribute->getAttributeOptions() as $option) {
        $items[] = ['label' => (string)$option->slug];
    }

    $itemSets[] = ['items' => $items ,
        'header' => $attribute->label ,
        'multiSelect' => $attribute->is_multiselect === '1' ? true : false
    ];
}

$macros = HistoryMacro::model()->findAll([
        'with' => 'subspecialties',
        'condition' => '(subspecialties_subspecialties.subspecialty_id = :subspecialty_id OR subspecialties_subspecialties.subspecialty_id IS NULL) AND active = 1',
        'params' => [':subspecialty_id' => $firm->getSubspecialtyID()],
        'order' => 'display_order asc',
    ]);
$standardSet = [];
foreach ($macros as $macro) {
    $standardSet[] = [
        'items' => $macro->name,
        'id' => $macro->id,
        'body' => $macro->body,
    ];
}
?>
<script type="text/javascript" id="history-add-to-dialog">
  $(function () {
    var inputText = $('#OEModule_OphCiExamination_models_Element_OphCiExamination_History_description');

    new OpenEyes.UI.AdderDialog({
      openButton: $('#show-add-to-history'),
      itemSets: $.map(<?= CJSON::encode($itemSets) ?>, function ($itemSet) {
        return new OpenEyes.UI.AdderDialog.ItemSet($itemSet.items, {'header': $itemSet.header,'multiSelect': $itemSet.multiSelect });
      }),
      liClass: 'restrict-width',
      onReturn: function (adderDialog, selectedItems) {
                inputText.val(formatStringToEndWithCommaAndWhitespace(inputText.val()) + concatenateArrayItemLabels(selectedItems));
        autosize.update(inputText);
        inputText.trigger('oninput');
        return true;
      }
    });

    new OpenEyes.UI.AdderDialog({
        openButton: $('.js-add-standard-set'),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($macro) {
                return ['label' => $macro['items'], 'id' => $macro['id']];
            }, $standardSet)
        )?>, {'multiSelect': true})],
        liClass: 'restrict-width',
        onReturn: function (adderDialog, selectedItems) {
            selectedItems.forEach( function (item) {
                <?php foreach ($standardSet as $set) : ?>
                if (<?= $set['id'] ?> === item.id) {
                    inputText.val(addLineBreakToString(inputText.val()) + "<?= Yii::app()->format->Ntext(rtrim(preg_replace('/[\r\n]+/', '\n', $set['body']))) ?>");
                }
                <?php endforeach; ?>
            });
            autosize.update(inputText);
            inputText.trigger('oninput');
            return true;
        }
    });

  });

</script>
