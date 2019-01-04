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
<style>
    div.fadeFullScreen {
        background: rgba(0,0,0,0.5);
        width:      100%;
        height:     100%;
        z-index:    10000;
        top:        0;
        left:       0;
        position:   fixed;
    }

    div.fadeContent{
        background-color: white;
        width:      30%;
        height:     30%;
        top:        50%;
        left:       40%;
        position:   fixed;
        color:      black;
        padding:    20px;
    }
</style>
<?php
if(isset(Yii::app()->params['COMPLog_port']) && Yii::app()->params['COMPLog_port'] > 0) {
    ?>
    <script type="text/javascript">
			let valOptions = <?= json_encode($element->getUnitValuesForForm(null, false)[1]); ?>;
        var OE_patient_firstname = "<?php echo $this->patient->first_name; ?>";
        var OE_patient_lastname = "<?php echo $this->patient->last_name; ?>";
        var OE_patient_dob = "<?php echo str_replace("-","",$this->patient->dob); ?>";
        var OE_patient_address = "<?php echo $this->patient->getSummaryAddress("^"); ?>";
        var OE_patient_gender = "<?php echo $this->patient->gender; ?>";
        var OE_COMPLog_port = <?php echo Yii::app()->params['COMPLog_port']; ?>;
    </script>
<?php
    Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/lodash.min.js", CClientScript::POS_END);
    Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/CompLog.js", CClientScript::POS_END);
}

list($values, $val_options) = $element->getUnitValuesForForm(null, false);
//Reverse the unit values to ensure bigger value display first.
$values = array_reverse($values, true);
//Get the base value that should be displayed whe popup open.
$unit_id = OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->findByAttributes(array('name'=>'Snellen Metre'))->id;
$default_display_value = OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnitValue::model()->findByAttributes(array('unit_id'=>$unit_id, 'value'=>'6/6'))->base_value;

$methods = CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->findAll(),
  'id', 'name');
$key = 0;
?>
<div class="element-both-eyes">
  <div>
      <?php if ($element->isNewRecord) { ?>
          <span class="data-label">VA Scale &nbsp;&nbsp;</span>
            <?=\CHtml::dropDownList('visualacuity_unit_change', @$element->unit_id,
                CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::
                model()->activeOrPk(@$element->unit_id)->findAllByAttributes(array('is_near' => '0')),
                    'id', 'name'), array('class' => 'inline'));
          if ($element->unit->information) { ?>
            <span class="js-has-tooltip fa oe-i info small"
                  data-tooltip-content="<?php echo $element->unit->information ?>"></span>
          <?php }
      }

      if(isset(Yii::app()->params['COMPLog_port']) && Yii::app()->params['COMPLog_port'] > 0)
      {
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

    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side): ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side"
          data-side="<?= $eye_side ?>"
      >
        <div class="active-form data-group flex-layout"
             style="<?= $element->hasEye($eye_side)? '': 'display: none;'?>"
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
                  <?php echo $form->checkBox($element, $eye_side . '_unable_to_assess',
                      array('text-align' => 'right', 'nowrapper' => true)) ?>
                  <?php echo $form->checkBox($element, $eye_side . '_eye_missing',
                      array('text-align' => 'right', 'nowrapper' => true)) ?>
              </div>
            </div>
          </div>
          <div class="add-data-actions flex-item-bottom" id="<?= $eye_side ?>-add-VisualAcuity-reading"
               style="<?= !$element->eyeAssesable($eye_side)? 'display: none;': '' ?>">
            <button class="button hint green addReading" id="add-reading-btn-<?= $eye_side?>" type="button">
              <i class="oe-i plus pro-theme"></i>
            </button>
            <!-- oe-add-select-search -->
          </div>
          <!--flex bottom-->
        </div>
        <!-- active form-->
        <div class="inactive-form"  style="<?= $element->hasEye($eye_side)? 'display: none;': ''?> ">
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
        openButton:$('#add-reading-btn-<?= $eye_side?>'),
        itemSets:[new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
            array_map(function ($key, $value) use ($default_display_value) {
                return $key==$default_display_value? ['label' => $value, 'id' => $key, 'set-default' => true]: ['label' => $value, 'id' => $key];
            }, array_keys($values), $values)) ?>, {'header':'Value', 'id':'reading_val'}),
          new OpenEyes.UI.AdderDialog.ItemSet(<?= CJSON::encode(
              array_map(function ($key, $method) {
                  return ['label' => $method, 'id' => $key];
              }, array_keys($methods), $methods)) ?>, {'header':'Method', 'id':'method'})
        ],
        onReturn: function(adderDialog, selectedItems){
          var tableSelector = $('.<?= $eye_side ?>-eye .va_readings');
          if(selectedItems.length==2){
            var selected_data = {};
            for (i in selectedItems) {
            	console.log(selectedItems[i]);
            	console.log(selectedItems[i]['id']);
              if(selectedItems[i]['itemSet'].options['id'] == 'reading_val'){
                selected_data.reading_value = selectedItems[i]['id'];
                selected_data.reading_display = selectedItems[i]['label'];
                selected_data.tooltip =  <?= CJSON::encode($val_options)?>[selectedItems[i]['id']]['data-tooltip'];
								console.log(selectedItems[i]['id']);
              }
              if(selectedItems[i]['itemSet'].options['id'] == 'method'){
                selected_data.method_id = selectedItems[i]['id'];
                selected_data.method_display = selectedItems[i]['label'];
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
$assetManager->publish($baseAssetsPath . '/components/chosen/');

Yii::app()->clientScript->registerScriptFile($assetManager->getPublishedUrl($baseAssetsPath . '/components/chosen/') . '/chosen.jquery.min.js');
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
