<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?php
$layoutColumns = $form->layoutColumns;
$form->layoutColumns = array('label' => 3, 'field' => 9);
?>
<section class="edit element full on-demand sub-element
        <?= $element->elementType->class_name ?>
        <?= $this->action->id == 'update' && !$element->event_id ? "missing" : "" ?>"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="<?php echo $element->elementType->name ?>"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">

  <header class="element-header">
    <h4 class="element-title"><?php echo $element->elementType->name; ?></h4>
  </header>

    <?php if ($this->action->id == 'update' && !$element->event_id) { ?>
      <div class="alert-box alert">This element is missing and needs to be completed</div>
    <?php } ?>

  <div class="element-fields js-element-eye full-width" data-side="<?=$element->eye?>">
    <div class="eyedraw-row cataract cols-11 flex-layout col-gap"
         data-is-new="<?= $element->isNewRecord && empty($template_data) ? 'true' : 'false' ?>">
      <div class="cols-6">
            <?php $this->renderPartial($element->form_view . '_OEEyeDraw', array(
              'element' => $element,
              'form' => $form,
          )); ?>
      </div>
      <div class="cols-6">
            <?php $this->renderPartial($element->form_view . '_OEEyeDraw_fields', array(
              'form' => $form,
              'element' => $element,
              'template_data' => $template_data
          )); ?>
      </div>
    </div>
    <span id="ophCiExaminationPCRRiskEyeLabel">
        <a href="javascript:showhidePCR('ophTrOperationnotePCRRiskDiv')">PCR Risk
        <span class="pcr-span1"></span>%</a>
    </span>
  </div>
</section>

<section id="ophTrOperationnotePCRRiskDiv">
  <div id="ophCiExaminationPCRRiskLeftEye" class="pcr-exam-link-opnote js-pcr-left">
        <?php
        $this->renderPartial(
            'application.views.default._pcr_risk_form',
            array('form' => $form, 'element' => $element, 'side' => 'left')
        );
        ?>
  </div>
  <div id="ophCiExaminationPCRRiskRightEye" class="pcr-exam-link-opnote js-pcr-right">
        <?php
        $this->renderPartial(
            'application.views.default._pcr_risk_form',
            array('form' => $form, 'element' => $element, 'side' => 'right')
        );
        ?>
  </div>
</section>

<?php $form->layoutColumns = $layoutColumns; ?>
