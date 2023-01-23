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

/**
 * @var \OEModule\OphCiExamination\models\Element_OphCiExamination_VisualAcuity $element
 */

list($values, $val_options) = $element->getUnitValuesForForm(null, false);
//Reverse the unit values to ensure bigger value display first.
$values = array_reverse($values, true);
//Get the base value that should be displayed whe popup open.
$unit_id = OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->findByAttributes(array('name' => 'Snellen Metre'))->id;
$default_display_value = OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue::model()->findByAttributes(array('unit_id' => $unit_id, 'value' => '6/6'))->base_value;

$methods = CHtml::listData(
    OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->findAll(),
    'id',
    'name'
);
$key = 0;

if (( null !== SettingMetadata::model()->getSetting('COMPLog_port')) && SettingMetadata::model()->getSetting('COMPLog_port') > 0) {
    ?>
    <script type="text/javascript">
            var valOptions = <?= CJSON::encode($val_options)?>;
            var OE_patient_firstname = "<?php echo $this->patient->first_name; ?>";
            var OE_patient_lastname = "<?php echo $this->patient->last_name; ?>";
            var OE_patient_dob = "<?php echo str_replace("-", "", $this->patient->dob); ?>";
            var OE_patient_address = "<?php echo $this->patient->getSummaryAddress("^"); ?>";
            var OE_patient_gender = "<?php echo $this->patient->gender; ?>";
            var OE_COMPLog_port = <?php echo SettingMetadata::model()->getSetting('COMPLog_port'); ?>;
    </script>
    <?php
    Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/CompLog.js", CClientScript::POS_END);
}


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
                'visualacuity_unit_change',
                @$element->unit_id,
                CHtml::listData(
                    OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::
                    model()->activeOrPk(@$element->unit_id)->findAllByAttributes(array('is_va' => '1', 'complex_only' => '0')),
                    'id',
                    'name'
                ),
                array('class' => 'inline visualacuity_unit_selector', 'data-record-mode' => $element::RECORD_MODE_SIMPLE)
            );
            if ($element->unit->information) { ?>
            <span class="js-has-tooltip fa oe-i info small"
                  data-tooltip-content="<?php echo $element->unit->information ?>"></span>
            <?php }
        }

        if (( null !== SettingMetadata::model()->getSetting('COMPLog_port')) && SettingMetadata::model()->getSetting('COMPLog_port') > 0) {
            ?>
          <button class="button blue hint" name="complog" id="et_complog">Measure in COMPLog</button>
          <iframe id="complog_launcher" src="" width="0" height="0" style="display:none;">
          </iframe>
            <?php
        }
        ?>
      </div>
</div>

<?php
// CVI alert
$cvi_api = Yii::app()->moduleAPI->get('OphCoCvi');
if ($cvi_api) {
    echo $form->hiddenInput($element, 'cvi_alert_dismissed', false, array('class' => 'cvi_alert_dismissed'));
}
?>
<div class="element-fields element-eyes">
    <input type="hidden" name="visualacuity_readings_valid" value="1"/>
    <?php echo $form->hiddenInput($element, 'unit_id', false); ?>
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side"
          data-side="<?= $eye_side ?>"
      >
        <div class="active-form data-group flex-layout"
             style="<?= $element->hasEye($eye_side) ? '' : 'display: none;'?>"
        >
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <div class="cols-9">
            <table class="cols-full blank va_readings">
              <tbody>
              <?php foreach ($element->{$eye_side . '_readings'} as $reading) {
                  // Adjust currently element readings to match unit steps
                    $reading->loadClosest($element->unit->id);
                    $this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
                      'name_stub' => CHtml::modelName($element) . '[' . $eye_side . '_readings]',
                      'key' => $key,
                      'reading' => $reading,
                      'side' => $reading->side,
                      'values' => $values,
                      'val_options' => $val_options,
                      'methods' => $methods,
                      'asset_path' => $this->getAssetPathForElement($element),
                    ));
                    ++$key;
              } ?>
              </tbody>
            </table>
            <div class="data-group noReadings">
              <div class="cols-8 column end">
                  <?php echo $form->checkBox(
                      $element,
                      $eye_side . '_unable_to_assess',
                      array('text-align' => 'right', 'nowrapper' => true)
                  ) ?>
                  <?php echo $form->checkBox(
                      $element,
                      $eye_side . '_eye_missing',
                      array('text-align' => 'right', 'nowrapper' => true)
                  ) ?>
              </div>
            </div>
              <div id="visualacuity-<?= $eye_side ?>-comments"
                   class="flex-layout flex-left comment-group js-comment-container"
                   style="<?= !$element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>"
                   data-comment-button="#visualacuity-<?= $eye_side ?>-comment-button">
                  <?= \CHtml::activeTextArea(
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
          <div class="add-data-actions flex-item-bottom" id="<?= $eye_side ?>-add-VisualAcuity-reading">
              <button id="visualacuity-<?= $eye_side ?>-comment-button"
                      class="button js-add-comments" data-comment-container="#visualacuity-<?= $eye_side ?>-comments"
                      type="button" style="<?= $element->{$eye_side . '_notes'} ? 'visibility: hidden;' : '' ?>">
                  <i class="oe-i comments small-icon"></i>
              </button>
            <button class="button hint green addReading" id="add-VisualAcuity-reading-btn-<?= $eye_side?>"
                    style="<?= !$element->eyeCanHaveReadings($eye_side) ? 'display: none;' : '' ?>"
                    type="button">
              <i class="oe-i plus pro-theme"></i>
            </button>
            <!-- oe-add-select-search -->
          </div>
          <!--flex bottom-->
        </div>
        <!-- active form-->
        <div class="inactive-form"  style="<?= $element->hasEye($eye_side) ? 'display: none;' : ''?> ">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
  <script type="text/javascript">
    $(function () {
      new OpenEyes.UI.AdderDialog({
        openButton:$("#add-VisualAcuity-reading-btn-<?= $eye_side?>"),
        itemSets:[new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($key, $value) use ($default_display_value) {
                return $key == $default_display_value ? ['label' => $value, 'id' => $key, 'set-default' => true] : ['label' => $value, 'id' => $key];
            },
            array_keys($values),
            $values)
                                                      ) ?>, {'header':'Value', 'id':'reading_val'}),
          new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
              array_map(function ($key, $method) {
                  return ['label' => $method, 'id' => $key];
              },
                array_keys($methods),
                $methods)
                                              ) ?>, {'header':'Method', 'id':'method'})
        ],
        onReturn: function(adderDialog, selectedItems){
          var tableSelector = $('.<?= $eye_side ?>-eye .va_readings');
          if (selectedItems.length==2){
            var selected_data = {};
            for (let item of selectedItems) {
              if (item['itemSet'].options['id'] == 'reading_val'){
                selected_data.reading_value = item['id'];
                selected_data.reading_display = item['label'];
                selected_data.tooltip =  <?= CJSON::encode($val_options)?>[item['id']]['data-tooltip'];
              }
              if (item['itemSet'].options['id'] == 'method'){
                selected_data.method_id = item['id'];
                selected_data.method_display = item['label'];
              }
            }
            OphCiExamination_VisualAcuity_addReading('<?= $eye_side ?>', selected_data);
            var newRow =  tableSelector.find('tbody tr:last');
            OphCiExamination_VisualAcuity_ReadingTooltip(newRow);
            newRow.find('.va-selector').trigger('change');
            return true;
          } else {
            return false;
          }
        },
      });
    });
  </script>
    <?php endforeach; ?>
</div>
<script id="visualacuity_reading_template" type="text/html">
    <?php
    $this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
        'name_stub' => CHtml::modelName($element) . '[{{side}}_readings]',
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
?>
<script type="text/javascript">
  $(document).ready(function () {

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
