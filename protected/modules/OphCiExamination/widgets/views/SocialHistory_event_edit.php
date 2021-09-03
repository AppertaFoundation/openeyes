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

/**
 * @var \OEModule\OphCiExamination\models\SocialHistory $element
 */
$nothing_selected_text = "Nothing selected.";
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath("SocialHistory.js") ?>"></script>
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
                <?= $form->labelEx($element, $element->getAttributeLabel('occupation_id')) ?>
            </td>
            <td>
                <div id="textField_occupation_id" class="cols-8">
                    <?= isset($element->occupation) ? $element->occupation->name : $nothing_selected_text; ?>
                </div>
                <?= $form->dropDownList(
                    $element,
                    'occupation_id',
                    CHtml::listData($element->occupation_options, 'id', 'name'),
                    ['empty' => '- Select -', 'nowrapper' => true, 'hidden' => true],
                    false,
                    array('label' => 4, 'field' => 4, 'full_dropdown' => true, 'class' => 'oe-input-is-read-only', 'hidden' => true)
                );
?>
                <?= $form->textField(
                    $element,
                    'type_of_job',
                    array(
                        'hide' => ($element->occupation_id !== 7),//Hide if the type is not other
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'style' => 'width: 100%',
                        'nowrapper' => true,
                        'hidden' => true
                    ),
                    null,
                    array('label' => 4, 'field' => 5)
                );
?>
            </td>
            <td>
                <?= $form->labelEx($element, $element->getAttributeLabel('driving_statuses')) ?>
            </td>
            <td>
                <div id="textField_driving_statuses" class="cols-8">
                    <?php if (!isset($element['driving_statuses']) || count($element['driving_statuses']) <= 0) {
                        echo $nothing_selected_text;
                    } else {
                        $driving_statuses = array_map(function ($driving_status) {
                            return trim($driving_status->name);
                        },
                            is_array($element->driving_statuses) ? $element->driving_statuses : []);
                        echo implode(', ', $driving_statuses);
                        $driving_status_ids = (isset($_POST[$model_name]['driving_statuses']) ?
                            $_POST[$model_name]['driving_statuses'] :
                            ($element->driving_statuses ?
                                array_map(function ($driving_status) {
                                    return $driving_status->id;
                                }, $element->driving_statuses) : null));
                    } ?>
                </div>
                <?php if (isset($driving_status_ids)) { ?>
                    <input type="hidden" class="js-driving-status-item" name="<?= $model_name ?>[driving_statuses][]"
                           value="<?= implode(" ,", $driving_status_ids) ?>">
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->labelEx($element, $element->getAttributeLabel('smoking_status_id')) ?>
            </td>
            <td>
                <div id="textField_smoking_status_id" class="cols-8">
                    <?= isset($element->smoking_status) ? $element->smoking_status->name : $nothing_selected_text; ?>
                </div>
                <?= $form->dropDownList(
                    $element,
                    'smoking_status_id',
                    CHtml::listData($element->smoking_status_options, 'id', 'name'),
                    ['empty' => '- Select -', 'nowrapper' => true, 'hidden' => true]
                );
?>
            </td>
            <td>
                <?= $form->labelEx($element, $element->getAttributeLabel('accommodation_id')) ?>
            </td>
            <td>
                <div id="textField_accommodation_id" class="cols-8">
                    <?= isset($element->accommodation) ? $element->accommodation->name : $nothing_selected_text; ?>
                </div>
                <?= $form->dropDownList(
                    $element,
                    'accommodation_id',
                    CHtml::listData($element->accommodation_options, 'id', 'name'),
                    ['empty' => '- Select -', 'nowrapper' => true, 'hidden' => true]
                ); ?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->labelEx($element, $element->getAttributeLabel('alcohol_intake')) ?>
            </td>
            <td class="flex-layout flex-left">
                <div id="textField_alcohol_intake"
                     class="cols-1 <?= (isset($element->alcohol_intake) ? '' : 'hidden') ?>">
                    <?= isset($element->alcohol_intake) ? $element->alcohol_intake : '' ?>
                </div>
                <?= $form->textField(
                    $element,
                    'alcohol_intake',
                    array(
                        'autocomplete' => Yii::app()->params['html_autocomplete'],
                        'nowrapper' => true,
                        'style' => 'width: 100px; margin-right: 10px;',
                        'append-text' => (isset($element->alcohol_intake) ? 'units/week' : $nothing_selected_text),
                        'hidden' => true
                    )
                );
?>
            </td>
            <td>
                <?= $form->labelEx($element, $element->getAttributeLabel('carer_id')) ?>
            </td>
            <td>
                <div id="textField_carer_id" class="cols-8">
                    <?= isset($element->carer) ? $element->carer->name : $nothing_selected_text; ?>
                </div>
                <?= $form->dropDownList(
                    $element,
                    'carer_id',
                    CHtml::listData($element->carer_options, 'id', 'name'),
                    ['empty' => '- Select -', 'nowrapper' => true, 'hidden' => true]
                );
?>
            </td>
        </tr>
        <tr>
            <td>
                <?= $form->labelEx($element, $element->getAttributeLabel('substance_misuse_id')) ?>
            </td>
            <td>
                <div id="textField_substance_misuse_id" class="cols-8">
                    <?= isset($element->substance_misuse) ? $element->substance_misuse->name : $nothing_selected_text; ?>
                </div>
                <?= $form->dropDownList(
                    $element,
                    'substance_misuse_id',
                    CHtml::listData($element->substance_misuse_options, 'id', 'name'),
                    ['empty' => '- Select -', 'nowrapper' => true, 'hidden' => true]
                ); ?>
            </td>
            <td colspan="2" class="js-comment-container"
                data-comment-button="#add-social-history-popup .js-add-comments"
                style="display: <?php if (!$element->comments) {
                    echo 'none';
                                } ?>">
           <textarea id="<?= $model_name ?>_comments"
                     name="<?= $model_name ?>[comments]"
                     class="js-comment-field cols-10"
                     placeholder="Enter comments here"
                     autocomplete="off" rows="1"
                     style="overflow: hidden; word-wrap: break-word; height: 24px;"><?= CHtml::encode($element->comments) ?></textarea>
                <i class="oe-i remove-circle small-icon js-remove-add-comments"></i>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="add-data-actions flex-item-bottom " id="add-social-history-popup">
        <button class="button js-add-comments"
                type="button"
                data-comment-container="#<?= $model_name ?>_entry_table .js-comment-container"
                style="visibility: <?php if ($element->comments) {
                    echo 'hidden';
                                   } ?>">
            <i class="oe-i comments small-icon "></i>
        </button>
        <button class="button hint green js-add-select-search" id="add-social-history-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>
    </div>
</div>

<script type="text/javascript">

  var disabledSocialHistoryFields = [
    $('#<?= $model_name ?>_occupation_id'),
    $('#<?= $model_name ?>_smoking_status_id'),
    $('#<?= $model_name ?>_alcohol_intake'),
    $('#<?= $model_name ?>_substance_misuse_id'),
    $('#<?= $model_name ?>_driving_statuses'),
    $('#<?= $model_name ?>_accommodation_id'),
    $('#<?= $model_name ?>_carer_id'),
  ];

  // Disable all form fields
  disabledSocialHistoryFields.forEach(function (field) {
    field.attr('disabled', 'disabled');
  });

  // Re-enable all fields on form submit (otherwise the data isn't sent)
  // Note: document.currentScript relies on this being run outside of $(document).ready
  $('#<?= $model_name ?>_entry_table').closest('form').submit(function () {
      disabledSocialHistoryFields.forEach(function (field) {
          field.removeAttr('disabled');
      });
  });

    $(document).ready(function () {
        // hide the driving status select
        $('#OEModule_OphCiExamination_models_SocialHistory_driving_statuses').hide();

        const controller = new OpenEyes.OphCiExamination.SocialHistoryController({nothing_selected_text: "<?= $nothing_selected_text; ?>"});

        // Disable removing items from the driving status multi-select list
        $('#<?= $model_name ?>_driving_statuses').closest('.multi-select-list').css("pointer-events", "none");

        new OpenEyes.UI.AdderDialog({
            openButton: $('#add-social-history-btn'),
            deselectOnReturn: false,
            itemSets: [
                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode(array_map(function ($item, $label) use ($element) {
                            return ['label' => $item->name, 'id' => $item->id, 'selected' => $item->id === $element->occupation_id];
                    }, $element->occupation_options, [])
                    ) ?>, {'header': 'Employment', 'id': 'occupation_id'}),

                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?php
                    $selected_driving_statuses = array_map(function ($status) {
                        return $status->id;
                    }, is_array($element->driving_statuses) ? $element->driving_statuses : []);

                    echo CJSON::encode(array_map(function ($item, $label) use ($selected_driving_statuses) {
                            return [
                                'label' => $item->name,
                                'id' => $item->id,
                                'selected' => in_array($item->id, $selected_driving_statuses),
                            ];
                    }, $element->driving_statuses_options, [])
                    ) ?>, {'header': 'Driving Status', 'id': 'driving_statuses'}),

                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode(array_map(function ($item, $label) use ($element) {
                            return [
                                'label' => $item->name,
                                'id' => $item->id,
                                'selected' => $element->smoking_status_id === $item->id,
                            ];
                    }, $element->smoking_status_options, [])
                    ) ?>, {'header': 'Smoking Status', 'id': 'smoking_status_id'}),

                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode(array_map(function ($item, $label) use ($element) {
                            return [
                                'label' => $item->name,
                                'id' => $item->id,
                                'selected' => $element->accommodation_id === $item->id,
                            ];
                    }, $element->accommodation_options, [])
                    ) ?>, {'header': 'Accommodation', 'id': 'accommodation_id'}),

                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode(array_map(function ($item, $label) use ($element) {
                            return ['label' => $item->name, 'id' => $item->id, 'selected' => $element->carer_id === $item->id];
                    }, $element->carer_options, [])
                    ) ?>, {'header': 'Carer', 'id': 'carer_id'}),

                new OpenEyes.UI.AdderDialog.ItemSet(
                    <?= CJSON::encode(array_map(function ($item, $label) use ($element) {
                            return [
                                'label' => $item->name,
                                'id' => $item->id,
                                'selected' => $element->substance_misuse_id === $item->id,
                            ];
                    }, $element->substance_misuse_options, [])
                    ) ?>, {'header': 'Substance Misuse', 'id': 'substance_misuse_id'}),

                new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
                    array_map(function ($item) use ($element) {
                        return ['label' => $item, 'id' => $item,
                            'selected' => isset($element->alcohol_intake) && $element->alcohol_intake == $item];
                    }, array_merge(range(0, 15, 5), range(20, 100, 10), [150], range(200, 400, 100)))
                ) ?>, {'header': 'Alcohol units', 'id': 'alcohol_intake'})
            ],
            onReturn: function (adderDialog, selectedItems) {
                controller.addEntry(selectedItems);
                return true;
            }
        });
    });

</script>