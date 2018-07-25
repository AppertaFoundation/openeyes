<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<script type="text/javascript" src="<?=$this->getJsPublishedPath("SocialHistory.js")?>"></script>
<div class="element-fields flex-layout full-width">
    <?php $model_name = CHtml::modelName($element); ?>
  <table id="<?= $model_name ?>_entry_table" class="cols-10 last-left">
    <colgroup>
      <col class="cols-2">
      <col class="cols-4">
      <col class="cols-2">
      <col class="cols-4">
    </colgroup>
    <tbody>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('occupation_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'occupation_id',
                CHtml::listData($element->occupation_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 4, 'full_dropdown' => true)
            );
            ?>
            <?= $form->textField(
                $element,
                'type_of_job',
                array(
                    'hide' => ($element->occupation_id !== 7),//Hide if the type is not other
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'style' => 'width: 100%',
                    'nowrapper' => true
                ),
                null,
                array('label' => 4, 'field' => 5)
            );
            ?>
        </td>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('driving_statuses'))?>
        </td>
        <td>
          <?= $form->multiSelectList(
                $element,
                CHtml::modelName($element) . '[driving_statuses]',
                'driving_statuses',
                'id',
                CHtml::listData($element->driving_statuses_options, 'id', 'name'),
                array(),
                array('empty' => '- Select -', 'nowrapper' => true),//'label' => $element->getAttributeLabel('driving_statuses')),
                false,
                false,
                null,
                false,
                false, // various attributes we don't care about
                array('stretch' => true)
            ); ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('smoking_status_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'smoking_status_id',
                CHtml::listData($element->smoking_status_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 4, 'full_dropdown' => true)
            );
            ?>
        </td>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('accommodation_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'accommodation_id',
                CHtml::listData($element->accommodation_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 5, 'full_dropdown' => true)
            );
            ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('alcohol_intake'))?>
        </td>
        <td class="flex-layout flex-left">
            <?= $form->textField(
                $element,
                'alcohol_intake',
                array(
                    'autocomplete' => Yii::app()->params['html_autocomplete'],
                    'nowrapper' => true,
                    'style'=> 'margin-right: 10px;',
                ),
                null,
                array('label' => 4, 'field' => 4, 'append-text' => 4, 'stretch' => false)
            );
            ?> units/week
        </td>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('carer_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'carer_id',
                CHtml::listData($element->carer_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 8, 'full_dropdown' => true)
            );
            ?>
        </td>
      </tr>
      <tr>
        <td>
            <?= $form->labelEx($element,$element->getAttributeLabel('substance_misuse_id'))?>
        </td>
        <td>
            <?= $form->dropDownList(
                $element,
                'substance_misuse_id',
                CHtml::listData($element->substance_misuse_options, 'id', 'name'),
                array('empty' => '- Select -', 'nowrapper' => true),
                false,
                array('label' => 4, 'field' => 5, 'full_dropdown' => true)
            );?>
        </td>
        <td colspan="2" class="js-comment-container"
            data-comment-button="#add-social-history-popup .js-add-comments"
            style="display: <?php if(!$element->comments){echo 'none';} ?>">
          <textarea id="<?= $model_name ?>_comments"
                    name="<?= $model_name ?>[comments]"
                    class="js-comment-field cols-10"
                    placeholder="Enter comments here"
                    autocomplete="off" rows="1"
                    style="overflow: hidden; word-wrap: break-word; height: 24px;"><?= CHtml::encode($element->comments) ?></textarea>
          <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
        </td>
      </tr>
    </tbody>
  </table>
  <div class="add-data-actions flex-item-bottom " id="add-social-history-popup">
    <button class="button js-add-comments"
            type="button"
            data-comment-container="#<?= $model_name ?>_entry_table .js-comment-container"
            style="visibility: <?php if($element->comments){echo 'hidden';} ?>">
      <i class="oe-i comments small-icon "></i>
    </button>
    <button class="button hint green js-add-select-search" id="add-social-history-btn" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button><!-- popup to add data to element -->
  </div>
</div>
<?php
$occupation_options = $element->occupation_options;
$driving_status_options = $element->driving_statuses_options;
$smoking_options = $element->smoking_status_options;
$accommodation_options = $element->accommodation_options;
$options_list = array(
    'occupation'=>$occupation_options,
    'driving_status'=>$driving_status_options,
    'smoking_status'=>$smoking_options,
    'accommodation'=>$accommodation_options,
);
$alcohol_options = range(1,20);
?>
<script type="text/javascript">
  $(document).ready(function() {
    var controller = new OpenEyes.OphCiExamination.SocialHistoryController();

    new OpenEyes.UI.AdderDialog({
      openButton: $('#add-social-history-btn'),
      itemSets: [<?php foreach ($options_list as $key=>$options) {?>
        new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($item, $label) {
                return ['value' => $item->name, 'id' => $item->id, 'option-label'=>$label];
            }, $options, array_fill(0,sizeof($options), $key))
        ) ?>, '<?= $key ?>'),
          <?php } ?>
        new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($item) {
                return ['value' => $item, 'id' => $item, 'option-label'=>'alcohol'];
            }, $alcohol_options)
        ) ?>, 'alcohol')
      ],
      onReturn: function (adderDialog, selectedItems) {
        controller.addEntry(selectedItems);
        return true;
      }
    });
  });
</script>