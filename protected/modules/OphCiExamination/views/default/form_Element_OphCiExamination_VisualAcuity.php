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
list($values, $val_options) = $element->getUnitValuesForForm(null, false);
$methods = CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Method::model()->findAll(),
    'id', 'name');
$key = 0;
?>
<div class="element-both-eyes">
  <div class="flex-layout flex-center">
      <?php if ($element->isNewRecord) { ?>
        <span class="data-label">VA Scale &nbsp;&nbsp;</span>
          <?php echo CHtml::dropDownList('visualacuity_unit_change', @$element->unit_id,
              CHtml::listData(OEModule\OphCiExamination\models\OphCiExamination_VisualAcuityUnit::model()->activeOrPk(@$element->unit_id)->findAllByAttributes(array('is_near' => '0')),
                  'id', 'name'), array('class' => 'inline'));
          ?>
      <?php } ?>
      <?php if ($element->unit->information) { ?>
        <div class="info">
          <small><em><?php echo $element->unit->information ?></em></small>
        </div>
      <?php } ?>
  </div>
</div>

<?php
// CVI alert
$cvi_api = Yii::app()->moduleAPI->get('OphCoCvi');
if ($cvi_api) {
    echo $cvi_api->renderAlertForVA($this->patient, $element);
    echo $form->hiddenInput($element, 'cvi_alert_dismissed', false, array('class' => 'cvi_alert_dismissed'));
}
?>
<div class="element-fields element-eyes">
  <input type="hidden" name="visualacuity_readings_valid" value="1"/>
    <?php echo $form->hiddenInput($element, 'id', false, array('class' => 'element_id')); ?>
    <?php echo $form->hiddenInput($element, 'unit_id', false); ?>
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>

    <?php foreach (array('left' => 'right', 'right' => 'left') as $page_side => $eye_side): ?>
      <div class="element-eye <?= $eye_side ?>-eye column <?= $page_side ?> side<?php if (!$element->hasEye($eye_side)) { ?> inactive <?php } ?>"
          data-side="<?= $eye_side ?>">
        <div class="active-form data-group flex-layout">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <div class="cols-9">
            <table
                class="cols-full blank va_readings"<?php if (!$element->{$eye_side . '_readings'}) { ?> style="display: none;" <?php } ?> >
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
            <div class="data-group noReadings"<?php if ($element->{$eye_side . '_readings'}) { ?> style="display: none;" <?php } ?>>
              <div class="cols-4 column">
                <div class="data-value not-recorded">Not recorded</div>
              </div>
              <div class="cols-8 column end">
                  <?php echo $form->checkBox($element, $eye_side . '_unable_to_assess',
                      array('text-align' => 'right', 'nowrapper' => true)) ?>
                  <?php echo $form->checkBox($element, $eye_side . '_eye_missing',
                      array('text-align' => 'right', 'nowrapper' => true)) ?>
              </div>
            </div>
          </div>
          <div class="flex-item-bottom" id="<?= $eye_side ?>-add-reading">
            <button class="button hint green addReading" type="button">
              <i class="oe-i plus pro-theme"></i>
            </button>
            <!-- oe-add-select-search -->

            <div id="<?= $eye_side ?>-add-visual-acuity" class="oe-add-select-search auto-width" style="bottom: 61px; display: none;">
              <div id="<?= $eye_side ?>-close-btn" class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
              <button class="button hint green add-icon-btn" type="button"><i class="oe-i plus pro-theme"></i></button>
              <table class="select-options">
                <tbody>
                <tr>
                  <td>
                    <ul id="visual-acuity-value-option" class="add-options cols-full" data-multi="true" data-clickadd="false">
                        <?php foreach ($values as $id=>$item) { ?>
                              <li data-str="<?php echo $item; ?>" data-id="<?= $id; ?>">
                                <span class="restrict-width"><?php echo $item; ?></span>
                              </li>
                        <?php } ?>
                    </ul>
                  </td>
                  <td>
                    <ul id="visual-acuity-method-option" class="add-options cols-full" data-multi="true" data-clickadd="false">
                        <?php foreach ($methods as $id=>$item) { ?>
                          <li data-id="<?= $id ?>" data-str="<?php echo $item; ?>">
                            <span class="restrict-width"><?php echo $item; ?></span>
                          </li>
                        <?php } ?>
                    </ul>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!--flex bottom-->
        </div>
        <!-- active form-->
        <div class="inactive-form" style="display: none">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
  <script type="text/javascript">
    $(function () {
      var adder = $('#<?= $eye_side ?>-add-reading');
      var popup = $('#<?= $eye_side ?>-add-visual-acuity');
      function addVA() {
        var tableSelector = $('.<?= $eye_side ?>-eye .va_readings');
        var va_value = $(popup).find('#visual-acuity-value-option .selected');
        var va_method = $(popup).find('#visual-acuity-method-option .selected');
        var newRow = tableSelector.find('tbody tr:last');
        newRow.find('.va-selector').val(va_value.data('id'));
        newRow.find('.method_id').val(va_method.data('id'));
      }

      setUpAdder(
        popup,
        'multi',
        addVA,
        adder.find('.addReading'),
        popup.find('.add-icon-btn'),
        adder.find('#<?= $eye_side ?>-close-btn, .add-icon-btn')
      );

    });
  </script>
    <?php endforeach; ?>
</div>
<script id="visualacuity_reading_template" type="text/html">
    <?php
    $default_reading = OEModule\OphCiExamination\models\OphCiExamination_VisualAcuity_Reading::model();
    $default_reading->init();
    $this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
        'name_stub' => CHtml::modelName($element) . '[{{side}}_readings]',
        'key' => '{{key}}',
        'side' => '{{side}}',
        'values' => $values,
        'val_options' => $val_options,
        'methods' => $methods,
        'asset_path' => $this->getAssetPathForElement($element),
        'reading' => $default_reading,
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
