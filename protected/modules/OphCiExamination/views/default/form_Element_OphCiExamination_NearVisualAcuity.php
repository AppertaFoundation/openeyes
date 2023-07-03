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
<?php
/***
 * @var $element \OEModule\OphCiExamination\models\Element_OphCiExamination_NearVisualAcuity
 */
list($values, $val_options) = $element->getUnitValuesForForm(null, true);
$methods = CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->findAll(), 'id', 'name');
$key = 0;
?>

<?php $this->beginClip('element-header-additional');?>
    <button class="va-change-complexity change-complexity"
            data-element-type-class="<?= \CHtml::modelName($element) ?>"
            data-record-mode="<?= $element::RECORD_MODE_COMPLEX ?>"
            data-eye-id="<?= $element::BEO | $element::LEFT | $element::RIGHT ?>"
    >Complex inputs</button>
<?php $this->endClip('element-header-additional');?>

<div class="element-both-eyes">
  <div>
        <?php if ($element->isNewRecord) { ?>
        <span class="data-label">VA Scale &nbsp;&nbsp;</span>
            <?=\CHtml::dropDownList(
                'nearvisualacuity_unit_change',
                @$element->unit_id,
                CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()
                  ->activeOrPk(@$element->unit_id)
                  ->findAllByAttributes(array('is_near' => '1')), 'id', 'name'),
                array('class' => 'inline', 'data-record-mode' => $element::RECORD_MODE_SIMPLE, 'data-test' => 'near-visual-acuity-unit-selector'));
            if ($element->unit->information) { ?>
            <div class="info">
              <small><em><?php echo $element->unit->information ?></em></small>
            </div>
                  <?php
            }
        } ?>
  </div>
</div>

<div class="element-fields element-eyes">
    <input type="hidden" name="nearvisualacuity_readings_valid" value="1" />
    <?php echo $form->hiddenInput($element, 'unit_id', false); ?>
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
    <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> <?php if (!$element->hasEye($eye_side)) {
        ?> inactive <?php
                               } ?>"
          data-side="<?= $eye_side ?>"
          data-test="near-visual-acuity-eye-column"
    >
      <div class="active-form data-group flex-layout"
           style="<?= $element->hasEye($eye_side)? '': 'display: none;'?>"
      >
        <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
        <div class="cols-9">
          <table class="cols-full blank near-va-readings">
            <tbody>
            <?php foreach ($element->{$eye_side.'_readings'} as $reading) {
                // Adjust currently element readings to match unit steps
                $reading->loadClosest($element->unit->id);
                $this->renderPartial('form_Element_OphCiExamination_NearVisualAcuity_Reading', array(
                    'name_stub' => CHtml::modelName($element).'[' . $eye_side . '_readings]',
                    'key' => $key,
                    'reading' => $reading,
                    'side' => $reading->side,
                    'values' => $values,
                    'val_options' => $val_options,
                    'methods' => $methods,
                    'asset_path' => $this->getAssetPathForElement($element),
                ));
                ++$key;
            }?>
            </tbody>
          </table>
          <div class="data-group noReadings" style="<?= count($element->{$eye_side . '_readings'}) > 0 ? 'display: none;' : '' ?>">
            <div class="cols-8 column">
                <?php echo $form->checkBox($element, $eye_side . '_unable_to_assess', array('text-align' => 'right', 'nowrapper' => true, 'data-test' => 'unable_to_assess-input'))?>
                <?php echo $form->checkBox($element, $eye_side . '_eye_missing', array('text-align' => 'right', 'nowrapper' => true, 'data-test' => 'eye_missing-input'))?>
            </div>
          </div>
            <div id="nearvisualacuity-<?= $eye_side ?>-comments" class="flex-layout flex-left comment-group js-comment-container"
                 style="<?= !$element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>" data-comment-button="#nearvisualacuity-<?= $eye_side ?>-comment-button">
                <?=\CHtml::activeTextArea(
                    $element,
                    $eye_side . '_notes',
                    array(
                        'rows' => 1,
                        'placeholder' => $element->getAttributeLabel($eye_side . '_notes'),
                        'class' => 'cols-full js-comment-field',
                        'style' => 'overflow-wrap: break-word; height: 24px;',
                    )
                ) ?>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
            </div>
        </div>
        <div class="add-data-actions flex-item-bottom" id="<?= $eye_side ?>-add-NearVisualAcuity-reading">
            <button id="nearvisualacuity-<?= $eye_side ?>-comment-button"
                    class="button js-add-comments" data-comment-container="#nearvisualacuity-<?= $eye_side ?>-comments"
                    type="button" style="<?= $element->{$eye_side . '_notes'} ? 'visibility: hidden;' : '' ?>">
                <i class="oe-i comments small-icon"></i>
            </button>
          <button class="button hint green addReading" id="add-NearVisualAcuity-reading-btn-<?= $eye_side?>"
                  style=" <?= !$element->eyeCanHaveReadings($eye_side) ? 'display: none; ': '' ?>"
                  type="button">
            <i class="oe-i plus pro-theme"></i>
          </button>
        </div>
      </div>
      <div class="inactive-form"
           style="<?= $element->hasEye($eye_side)? 'display: none;': ''?>" >
        <div class="add-side">
          <a href="#">
            Add <?= $eye_side ?> side <span class="icon-add-side"></span>
          </a>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      new OpenEyes.UI.AdderDialog({
        openButton: $("#add-NearVisualAcuity-reading-btn-<?= $eye_side?>"),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($key, $value) {
                return ['label' => $value, 'id' => $key];
            },
            array_keys($values),
            $values)
                                                       )?>, {'header':'Value', 'id':'reading_val'}),
          new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
              array_map(function ($key, $method) {
                return ['label' => $method, 'id' => $key];
              },
                array_keys($methods),
                $methods)
                                              )?>, {'header':'Method', 'id':'method'})
         ],
        onReturn: function (adderDialog, selectedItems) {
          var tableSelector = $('.<?= $eye_side ?>-eye .near-va-readings');
          if (selectedItems.length==2){
            var selected_data = {};
            for (i in selectedItems) {
              if (selectedItems[i]['itemSet'].options['id'] == 'reading_val'){
                selected_data.reading_value = selectedItems[i]['id'];
                selected_data.reading_display = selectedItems[i]['label'];
                selected_data.tooltip =  <?= CJSON::encode($val_options)?>[selectedItems[i]['id']]['data-tooltip']
              }
              if (selectedItems[i]['itemSet'].options['id'] == 'method'){
                selected_data.method_id = selectedItems[i]['id'];
                selected_data.method_display = selectedItems[i]['label'];
              }
            }
            OphCiExamination_NearVisualAcuity_addReading('<?= $eye_side ?>', selected_data);
            newRow = tableSelector.find('tbody tr:last');
            OphCiExamination_VisualAcuity_ReadingTooltip(newRow);
            newRow.find('.va-selector').trigger('change');
            return true;
          } else {
            return false;
          }
        }

      });
    </script>
    <?php endforeach; ?>
</div>
<script id="nearvisualacuity_reading_template" type="text/html">
    <?php
    $this->renderPartial('form_Element_OphCiExamination_NearVisualAcuity_Reading', array(
            'name_stub' => CHtml::modelName($element).'[{{side}}_readings]',
            'key' => '{{key}}',
            'side' => '{{side}}',
            'values' => $values,
            'val_options' => $val_options,
            'methods' => $methods,
            'asset_path' => $this->getAssetPathForElement($element),
            'selected_data' => array(
                'reading_unit_id' => $element->unit_id,
                'reading_value' => '{{reading_value}}',
                'reading_display' => '{{reading_display}}',
                'method_id' => '{{method_id}}',
                'method_display' => '{{method_display}}',
                'tooltip' => '{{tooltip}}'
            )
    ));
    ?>
</script>
<?php
$assetManager = Yii::app()->getAssetManager();
$baseAssetsPath = Yii::getPathOfAlias('application.assets');
$assetManager->publish($baseAssetsPath . '/components/chosen/', true);

Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath.'/components/chosen/', true).'/chosen.jquery.min.js');
?>
<script type="text/javascript">
    $(document).ready(function() {

        OphCiExamination_VisualAcuity_method_ids = [ <?php
        $first = true;
        foreach ($methods as $index => $method) {
            if (!$first) {
                echo ', ';
            }
            $first = false;
            echo $index;
        } ?> ];

        $('.element[data-element-type-class="<?= \CHtml::modelName($element) ?>"] .js-duplicate-element')
          .data('copy-element-callback', function() {
            const inside = $('.element[data-element-type-class="<?= \CHtml::modelName($element) ?>"]');

            /*
             * When the previous examination visual acuity data is return in the new element, it includes two hidden fields with the ids
             * of the readings from the previous events. Unless those ids are removed, they will be sent back to the server such that
             * the existing readings will have their element_id fields updated to the newly created element, instead of those readings
             * being preserved with new readings being created for the new element.
             *
             * In effect, it moves the readings instead of copying them unless the existing ids are removed.
             */
            inside.find(`input[name^="<?= \CHtml::activeName($element, 'left_readings') ?>"][name$="[id]"]`).remove();
            inside.find(`input[name^="<?= \CHtml::activeName($element, 'right_readings') ?>"][name$="[id]"]`).remove();
          });
    });
</script>
