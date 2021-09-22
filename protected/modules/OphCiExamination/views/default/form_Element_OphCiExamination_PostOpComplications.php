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

use OEModule\OphCiExamination\models\OphCiExamination_PostOpComplications;

?>
<?php

$model_name = CHtml::modelName($element);
$operationNoteList = $element->getOperationNoteList();
$operation_note_id = \Yii::app()->request->getParam(
    'OphCiExamination_postop_complication_operation_note_id',
    (is_array($operationNoteList) ? key($operationNoteList) : null)
);

$firm = \Firm::model()->findByPk(\Yii::app()->session['selected_firm_id']);
$subspecialty_id = $firm->serviceSubspecialtyAssignment ? $firm->serviceSubspecialtyAssignment->subspecialty_id : null;

$right_eye = OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList(
    $element->id,
    $operation_note_id,
    $subspecialty_id,
    \Eye::RIGHT
);

$right_eye_data = \CHtml::listData($right_eye, 'id', 'name');

$left_eye = OphCiExamination_PostOpComplications::model()->getPostOpComplicationsList(
    $element->id,
    $operation_note_id,
    $subspecialty_id,
    \Eye::LEFT
);
$left_eye_data = \CHtml::listData($left_eye, 'id', 'name');

$defaultURL = '/' . Yii::app()->getModule('OphCiExamination')->id . '/' . Yii::app()->getModule('OphCiExamination')->defaultController;

$left_values = $element->getRecordedComplications(\Eye::LEFT, $operation_note_id);
$right_values = $element->getRecordedComplications(\Eye::RIGHT, $operation_note_id);

$left_other_values = array_filter($left_values, function ($val) {
    return $val['name'] == 'other';
});

$right_other_values = array_filter($right_values, function ($val) {
    return $val['name'] == 'other';
});

$left_other = !empty($left_other_values) ? array_values($left_other_values)[0]['other'] : '';

$right_other = !empty($right_other_values) ? array_values($right_other_values)[0]['other'] : '';
?>

<?php if ($operationNoteList) : ?>
    <div id="div_Element_OphTrOperationnote_ProcedureList_id">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <div class="cols-5 column end">
            <?= \CHtml::dropDownList(
                'OphCiExamination_postop_complication_operation_note_id',
                $operation_note_id,
                $operationNoteList,
                array(
                    'id' => 'OphCiExamination_postop_complication_operation_note_id-select',
                    'name' => 'OphCiExamination_postop_complication_operation_note_id',
                    'nolabel' => true,
                )
            ); ?>
        </div>
    </div>
  <div class="element-fields element-eyes">
      <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
      <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) :
            $eye_abbr = $eye_side === 'right' ? 'R' : 'L'; ?>
        <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?>" data-side="<?= $eye_side ?>">
          <hr/>
          <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
            <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
            <div>
                <?php echo $form->dropDownList(
                    OphCiExamination_PostOpComplications::model(),
                    'name',
                    ${$eye_side . '_eye_data'},
                    array(
                        'empty' => array('-1' => 'Select Common Complication'),
                        'id' => $eye_side . '-complication-select',
                        'nolabel' => true,
                    ),
                                   false,
                                   array()
                );
                $eye_macro = $eye_side == 'right' ? \Eye::RIGHT : \Eye::LEFT;
                $this->widget('application.widgets.AutoCompleteSearch', ['field_name' => $eye_side . '_complication_autocomplete_id']);
                ?>
            </div>
          </div>
          <div class="active-form" style="<?= !$element->hasEye($eye_side) ? "display: none;" : "" ?>">
            <heading class="<?= $eye_side ?>-no-recorded-complication-text no-recorded"
                style="display: <?php echo ${$eye_side . '_values'} ? 'none' : '' ?>">
              No Recorded Complications</heading>
              <?php echo $form->hiddenInput($element, 'id', false); ?>

            <table id="<?= $eye_side ?>-complication-list"
                   class="recorded-postop-complications cols-8"
                   style="display: <?php echo ${$eye_side . '_values'} ? '' : 'none' ?>"
                   data-sideletter="<?= $eye_abbr ?>">
              <tbody>
              <?php foreach (${$eye_side . '_values'} as $key => $value) : ?>
                <tr>
                  <td class="postop-complication-name"  data-complication-name="<?=$value['name']?>">
                      <?php if ($value['name'] == 'other') {
                            if ($element->hasErrors("complication_other[$eye_abbr]")) {
                                echo $value['name'] . ' ';
                                echo Chtml::textField("complication_other[$eye_abbr]", ${$eye_side . '_other'}, ['class' => 'error']);
                            } else {
                                echo $value['name'] . ' ' . $value['other'];
                                echo CHtml::hiddenField("complication_other[$eye_abbr]", ${$eye_side . '_other'});
                            }
                      } else {
                          echo $value['name'];
                      }?>
                  </td>
                  <td class='<?= $eye_side ?>'>
                    <a class="postop-complication-remove-btn" href="javascript:void(0)">
                      <i class="oe-i trash"></i>
                    </a>
                      <?php echo \CHtml::hiddenField("complication_items[$eye_abbr][$key]", $value['id'],
                          array('id' => "complication_items_" . $eye_abbr . "_$key")); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? "display: none;" : "" ?>">
            <div class="add-side">
              <a href="#">
                Add <?= $eye_side ?> eye <span class="icon-add-side"></span>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
  </div>

<?php else : ?>
    <div id="div_Element_OphTrOperationnote_ProcedureList_id">
        <input type="hidden" name="<?= $model_name ?>[present]" value="1"/>
        <div class="cols-12 column text-center">
            There are no recorded operations for this patient
        </div>
    </div>

<?php endif; ?>
<script type="text/javascript" src="<?= Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.js') . '/AutoCompleteSearch.js', true, -1); ?>"></script>
<script>
  OpenEyes.UI.AutoCompleteSearch.init({
          input: $('#right_complication_autocomplete_id'),
          url: 'getPostOpComplicationAutocopleteList',
          onSelect: function(){
              let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
              addPostOpComplicationTr(AutoCompleteResponse.label,'right-complication-list', AutoCompleteResponse.value, 0 );
              setPostOpComplicationTableText();
          }
      });

  OpenEyes.UI.AutoCompleteSearch.init({
          input: $('#left_complication_autocomplete_id'),
          url: 'getPostOpComplicationAutocopleteList',
          onSelect: function(){
              let AutoCompleteResponse = OpenEyes.UI.AutoCompleteSearch.getResponse();
              addPostOpComplicationTr(AutoCompleteResponse.label,'left-complication-list', AutoCompleteResponse.value, 0 );
              setPostOpComplicationTableText();
          }
      });
</script>