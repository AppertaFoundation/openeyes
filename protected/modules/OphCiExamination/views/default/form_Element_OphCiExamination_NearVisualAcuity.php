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
list($values, $val_options) = $element->getUnitValuesForForm(null, true);
$methods = CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->findAll(), 'id', 'name');
$key = 0;
?>

<div class="element-both-eyes">
  <div>
      <?php if ($element->isNewRecord) { ?>
        <span class="data-label">VA Scale &nbsp;&nbsp;</span>
          <?php echo CHtml::dropDownList(
              'nearvisualacuity_unit_change',
              @$element->unit_id,
              CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()
                  ->activeOrPk(@$element->unit_id)
                  ->findAllByAttributes(array('is_near' => '1')), 'id', 'name'),
              array('class' => 'inline'));
          ?>
      <?php } ?>
      <?php if ($element->unit->information) { ?>
        <div class="info">
          <small><em><?php echo $element->unit->information ?></em></small>
        </div>
      <?php } ?>
  </div>
</div>

<div class="element-fields element-eyes">
	<input type="hidden" name="nearvisualacuity_readings_valid" value="1" />
	<?php echo $form->hiddenInput($element, 'unit_id', false); ?>
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side): ?>
    <div class="element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side <?php if (!$element->hasEye($eye_side)) { ?> inactive <?php } ?>"
          data-side="<?= $eye_side ?>">
      <div class="active-form data-group flex-layout">
        <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
        <div class="cols-9">
          <table class="cols-full blank near-va-readings"
                 style=" <?= ($element->isNewRecord || !sizeof($element->{$eye_side .'_readings'})) ? 'display: none; ': '' ?> ">
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
          <div class="data-group noReadings"
               style=" <?= ($element->isNewRecord || !sizeof($element->{$eye_side .'_readings'})) ?  '': 'display: none; ' ?>" >
            <div class="cols-8 column">
                <?php echo $form->checkBox($element, $eye_side . '_unable_to_assess', array('text-align' => 'right', 'nowrapper' => true))?>
                <?php echo $form->checkBox($element, $eye_side . '_eye_missing', array('text-align' => 'right', 'nowrapper' => true))?>
            </div>
          </div>
        </div>
        <div class="add-data-actions flex-item-bottom" id="<?= $eye_side ?>-add-near-va-reading">
          <button class="button hint green addReading" id="<?= $eye_side ?>-add-near-va-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
          </button>
        </div>
      </div>
      <div class="inactive-form" style="display: none;">
        <div class="add-side">
          <a href="#">
            Add <?= $eye_side ?> side <span class="icon-add-side"></span>
          </a>
        </div>
      </div>
    </div>
    <script type="text/javascript">
      new OpenEyes.UI.AdderDialog({
        openButton: $('#<?= $eye_side ?>-add-near-va-btn'),
        itemSets: [new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($key, $value) {
              return ['label' => $value, 'id' => $key];
              }, array_keys($values), $values))?>, {'header':'Value', 'id':'reading_val'}),
          new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
              array_map(function ($key, $method) {
                return ['label' => $method, 'id' => $key];
                }, array_keys($methods), $methods))?>, {'header':'Method', 'id':'method'})
         ],
        onReturn: function (adderDialog, selectedItems) {
          var tableSelector = $('.<?= $eye_side ?>-eye .near-va-readings');
          if(selectedItems.length==2){
            var selected_data = {};
            for (i in selectedItems) {
              if(selectedItems[i]['itemSet'].options['id'] == 'reading_val'){
                selected_data.reading_value = selectedItems[i]['id'];
                selected_data.reading_display = selectedItems[i]['label'];
                selected_data.tooltip =  <?= CJSON::encode($val_options)?>[selectedItems[i]['id']]['data-tooltip']
              }
              if(selectedItems[i]['itemSet'].options['id'] == 'method'){
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
$assetManager->publish($baseAssetsPath.'/components/chosen/');

Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath.'/components/chosen/').'/chosen.jquery.min.js');

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
	});
</script>
