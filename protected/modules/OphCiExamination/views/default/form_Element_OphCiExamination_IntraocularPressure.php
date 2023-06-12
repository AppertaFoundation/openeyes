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
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/CurrentManagement.js", CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/IntraocularPressure.js", CClientScript::POS_END);

?>

<div class="element-fields element-eyes" data-test="intraocular-pressure-element">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) :?>
      <div class="js-element-eye <?=$eye_side?>-eye <?=$page_side?>" data-side="<?=$eye_side?>">
        <div class="active-form data-group flex-layout" style="<?=!$element->hasEye($eye_side)?"display: none;":""?>">
          <a class="remove-side">
            <i class="oe-i remove-circle small"></i>
          </a>
            <?php $this->renderPartial(
                $element->form_view . '_side',
                array('side' => $eye_side, 'element' => $element, 'form' => $form)
            )?>
        </div>
        <div class="inactive-form" style="<?=$element->hasEye($eye_side) ? "display: none;":""?>">
          <div class="add-side">
            <a href="#">
              Add <?=$eye_side?> eye
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>
