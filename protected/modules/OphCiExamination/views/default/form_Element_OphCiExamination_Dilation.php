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
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/Dilation.js");

$key = 0;
$dilation_drugs = \OEModule\OphCiExamination\models\OphCiExamination_Dilation_Drugs::model()->findAll();

$dilation_drugs_order = array();
$dilation_drugs_status = array();
foreach ($dilation_drugs as $d_drug) {
    $dilation_drugs_order[$d_drug['id']] = $d_drug['display_order'];
    $dilation_drugs_status[$d_drug['id']] = $d_drug['is_active'];
}
?>
<div class="element-fields element-eyes edit-Dilation">
  <input type="hidden" name="dilation_treatments_valid" value="1"/>
    <?php echo $form->hiddenField($element, 'eye_id', array('class' => 'sideField')) ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?>" data-side="<?= $eye_side ?>">
        <div class="active-form data-group flex-layout"
             style="<?= !$element->hasEye($eye_side) ? "display: none;" : "" ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <div class="cols-9">
            <table class="cols-full dilation_table"
                   style="<?= (!$element->{$eye_side . '_treatments'}) ? 'display: none;' : '' ?>">
              <tbody class="plain" id="dilation_<?= $eye_side ?>">
              <?php foreach ($element->{$eye_side . '_treatments'} as $treatment) {
                    $this->renderPartial(
                      'form_Element_OphCiExamination_Dilation_Treatment',
                      array(
                          'name_stub' => CHtml::modelName($element) . '[' . $eye_side . '_treatments]',
                          'treatment' => $treatment,
                          'key' => $key,
                          'side' => $treatment->side,
                          'drug_name' => $treatment->drug->name,
                          'drug_id' => $treatment->drug_id,
                          'data_order' => $treatment->drug->display_order,
                      )
                    );
                    ++$key;
              } ?>
              </tbody>
            </table>
          </div>
          <div class="add-data-actions  flex-item-bottom">
            <button class="button hint green js-add-select-search" type="button">
              <i class="oe-i plus pro-theme"></i>
            </button>
            <div id="add-to-dilation" class="oe-add-select-search" style="display: none;" type="button">
              <div class="close-icon-btn">
                <i class="oe-i remove-circle medium"></i>
              </div>
              <button class="button hint green add-icon-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
              </button>
              <table class="select-options">
                <tbody>
                <tr>
                  <td>
                    <div class="flex-layout flex-top flex-left">
                      <ul class="add-options" data-multi="false" data-clickadd="false">
                          <?php foreach ($element->getAllDilationDrugs($eye_side) as $id => $drug) : ?>
                                <?php if ($dilation_drugs_status[$id]) : ?>
                              <li data-str="<?= $id ?>"
                                  data-order="<?= $dilation_drugs_order[$id] ?>"><?= $drug ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                      </ul>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? "display: none;" : "" ?>">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
      <script type="text/javascript">
        $(function () {
          var side = $('section[data-element-type-class=\'OEModule_OphCiExamination_models_Element_OphCiExamination_Dilation\'] ' +
            '.<?=$eye_side?>-eye');
          var popup = side.find('#add-to-dilation');

          var controller = null;
          $(document).ready(function () {
            controller = new OpenEyes.OphCiExamination.DilationController(null, side.find('.dilation_table'), popup);
          });

          popup.find('.add-icon-btn').click(function () {
            popup.find('li.selected').each(function () {
              controller.OphCiExamination_Dilation_addTreatment($(this), '<?= $eye_side ?>');
              $(this).removeClass('selected');
            });
          });

          setUpAdder(
            popup,
            'multi',
            null,
            side.find('.js-add-select-search'),
            popup.find('.add-icon-btn'),
            popup.find('.close-icon-btn')
          );
        })
      </script>
    <?php endforeach; ?>
</div>
<script id="dilation_treatment_template" type="text/html">
    <?php
    $this->renderPartial(
        'form_Element_OphCiExamination_Dilation_Treatment',
        array(
            'name_stub' => CHtml::modelName($element) . '[{{side}}_treatments]',
            'key' => '{{key}}',
            'side' => '{{side}}',
            'drug_name' => '{{drug_name}}',
            'drug_id' => '{{drug_id}}',
            'treatment_time' => '{{treatment_time}}',
            'data_order' => '{{data_order}}',
        )
    ); ?>
</script>
