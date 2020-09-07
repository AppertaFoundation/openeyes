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
$iop = $element->getLatestIOP($this->patient);

$exam_api = Yii::app()->moduleAPI->get('OphCiExamination');
$targetIOP = $exam_api->getTargetIOP($this->patient);
?>
<div class="element-data element-eyes flex-layout">
    <script type="text/javascript">
        var previous_iop = <?php echo json_encode($iop);?>;
    </script>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $side => $eye) :
        $hasEyeFunc = "has".ucfirst($eye);
        ?>
    <section class="js-element-eye cols-6 <?=$side?> <?=$eye?>-eye">
        <?php if ($element->$hasEyeFunc()) :?>
    <div class="data-group">
      <table>
        <tbody>
        <tr id="div_OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_<?=$eye?>_iop_id" >
          <td class="cols-5 column"><label>IOP:</label></td>
          <td class="cols-7 column end"
              id="OEModule_OphCiExamination_models_Element_OphCiExamination_CurrentManagementPlan_<?=$eye?>_iop">
              <?= ($iop == null) ? 'N/A' : $iop[$eye.'IOP'].' mmHg';?>
              <?php if (isset($targetIOP[$eye]) && !is_null($targetIOP[$eye]) && isset($iop[$eye.'IOP']) && $iop[$eye.'IOP'] > $targetIOP[$eye]) :?>
                <span class="iop_notification error">*** IOP above target ***</span>
                <?php endif?>
          </td>
        </tr>
        <tr>
          <td class="cols-5 column">
            <div class="data-label">
                <?=\CHtml::encode($element->getAttributeLabel($eye.'_glaucoma_status_id'))?>
            </div>
          </td>
          <td class="cols-7 column end">
            <div class="data-value">
                <?php
                $eyeGlocStatus = $eye."_glaucoma_status";
                echo $element->$eyeGlocStatus ? $element->$eyeGlocStatus->name : 'None'?>
            </div>
          </td>
        </tr>
        <tr>
          <td class="cols-5 column">
            <div class="data-label">
                <?=\CHtml::encode($element->getAttributeLabel($eye.'_drop-related_prob_id'))?>
            </div>
          </td>
          <td class="cols-7 column end">
            <div class="data-value">
                <?php echo $element->{$eye.'_drop-related_prob'} ? $element->{$eye.'_drop-related_prob'}->name : 'None'?>
            </div>
          </td>
        </tr>
        <tr>
          <td class="cols-5 column">
            <div class="data-label">
                <?=\CHtml::encode($element->getAttributeLabel($eye.'_drops_id'))?>
            </div>
          </td>
          <td class="cols-7 column end">
            <div class="data-value">
                <?php
                $eyeDrops = $eye."_drops";
                echo $element->$eyeDrops ? $element->$eyeDrops->name : 'None'?>
            </div>
          </td>
        </tr>
        <tr>
          <td class="cols-5 column">
            <div class="data-label">
                <?=\CHtml::encode($element->getAttributeLabel($eye.'_surgery_id'))?>
            </div>
          </td>
          <td class="cols-7 column end">
            <div class="data-value">
                <?php
                $eyeSurgery = $eye."_surgery";
                echo $element->$eyeSurgery ? $element->$eyeSurgery->name : 'N/A'?>
            </div>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
        <?php else :?>
      Not recorded
        <?php endif;?>
    </section>
    <?php endforeach; ?>
</div>
