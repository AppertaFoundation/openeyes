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
$usedGlaucomaStatuses = array();
if (isset($element->right_glaucoma_status_id)) {
    $usedGlaucomaStatuses[] = $element->right_glaucoma_status_id;
}
if (isset($element->left_glaucoma_status_id)) {
    $usedGlaucomaStatuses[] = $element->left_glaucoma_status_id;
}

$glaucomaStatus =
    CHtml::listData(\OEModule\OphCiExamination\models\OphCiExamination_GlaucomaStatus::model()
    ->activeOrPk($usedGlaucomaStatuses)->findAll(array('order' => 'display_order asc')), 'id', 'name');

$usedDropRelatProblem = array();
if (isset($element->{'right_drop-related_prob_id'})) {
    $usedDropRelatProblem[] = $element->{'right_drop-related_prob_id'};
}
if (isset($element->{'left_drop-related_prob_id'})) {
    $usedDropRelatProblem[] = $element->{'left_drop-related_prob_id'};
}

$dropRelatProblem = CHtml::listData(\OEModule\OphCiExamination\models\OphCiExamination_DropRelProb::model()
    ->activeOrPk($usedDropRelatProblem)->findAll(array('order' => 'display_order asc')), 'id', 'name');

$usedDrops = array();
if (isset($element->right_drops_id)) {
    $usedDrops[] = $element->right_drops_id;
}
if (isset($element->left_drops_id)) {
    $usedDrops[] = $element->left_drops_id;
}

$dropsIds = CHtml::listData(\OEModule\OphCiExamination\models\OphCiExamination_Drops::model()
    ->activeOrPk($usedDrops)->findAll(array('order' => 'display_order asc')), 'id', 'name');

$usedSurgeryIds = array();
if (isset($element->right_surgery_id)) {
    $usedSurgeryIds[] = $element->right_surgery_id;
}
if (isset($element->left_surgery_id)) {
    $usedSurgeryIds[] = $element->left_surgery_id;
}

$surgeryIds = CHtml::listData(\OEModule\OphCiExamination\models\OphCiExamination_ManagementSurgery::model()
    ->activeOrPk($usedSurgeryIds)->findAll(array('order' => 'display_order asc')), 'id', 'name');

$iop = $element->getLatestIOP($this->patient);
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/CurrentManagement.js", CClientScript::POS_HEAD);

?>
<div class="element-fields element-eyes">
    <script type="text/javascript">
        var previous_iop = <?= json_encode($iop)?>;
    </script>
    <?= $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $side => $eye) :
        $hasEyeFunc = "has".ucfirst($eye);
        ?>
    <div class="js-element-eye <?=$eye?>-eye column <?=$side?> <?= !$element->$hasEyeFunc()? "inactive" : ""?>"
       data-side="<?=$eye?>"
  >
        <div class="active-form" style="<?=$element->$hasEyeFunc() ? "" : "display: none;"?>">
      <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
      <table class="cols-full">
        <tbody>
          <tr >
            <td class="flex-layout"
                id="div_OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_<?=$eye?>_iop_id"
            >
              <label>IOP:</label>
              <div class=""
                   id="OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_<?=$eye?>_iop">
                  <?php echo ($iop == null) ? 'N/A' : $iop['rightIOP'].' mmHg'?>
              </div>
            </td>
          </tr>
          <tr>
            <td>
                <?= $form->dropDownList($element, $eye.'_glaucoma_status_id', $glaucomaStatus,
                    array('empty' => 'Select'), false, array('label' => 4, 'field' => 8, 'stretch' => true))?>
            </td>
          </tr>
          <tr>
            <td>
                <?= $form->dropDownList($element, $eye.'_drop-related_prob_id',
                    $dropRelatProblem, array(), false, array('label' => 4, 'field' => 8, 'stretch' => true))?>
            </td>
          </tr>
          <tr>
            <td>
                <?= $form->dropDownList($element, $eye.'_drops_id',
                    $dropsIds, array('empty' => 'Select'), false, array('label' => 4, 'field' => 8, 'stretch' => true))?>
            </td>
          </tr>
          <tr>
            <td>
                <?= $form->dropDownList($element, $eye.'_surgery_id',
                    $surgeryIds, array('empty' => 'N/A'), false, array('label' => 4, 'field' => 8, 'stretch' => true))?>
            </td>
          </tr>
        </tbody>
      </table>



        </div>
        <div class="inactive-form" style="<?=!$element->$hasEyeFunc() ? "" : "display: none;"?>">
            <div class="add-side">
                <a href="#">
                    Add <?=$eye?> side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
    <?php endforeach;?>
</div>

