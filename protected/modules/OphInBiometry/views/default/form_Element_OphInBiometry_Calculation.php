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
  .readonly-div {
    height: auto;
    border: 1px solid #6b8aaa;
    background-color: #dddddd;
  }
</style>

<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side): ?>
      <div id="<?php echo $eye_side ?>-eye-calculation"
           class="js-element-eye <?php echo $eye_side ?>-eye <?php echo $page_side ?> column <?php if (!$element->hasEye($eye_side)) { ?> inactive<?php } ?>"
           data-side="<?php echo $eye_side ?>" style="display: <?=$this->action->id === "create" ? "none" : "" ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
            <?php $this->renderPartial('form_Element_OphInBiometry_Calculation_fields',
                array('side' => $eye_side, 'element' => $element, 'form' => $form, 'data' => $data)); ?>
        </div>
        <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <div class="add-side">
            Set <?php echo $eye_side ?> side lens type
          </div>
        </div>
      </div>
      <?php if ($this->action->id === "create") {?>
        <div class="js-element-eye <?= $eye_side ?>-eye column">
            Calculation editing is not available.
        </div>
      <?php } ?>
    <?php endforeach; ?>
</div>
<div class="element-fields element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side): ?>
      <div id="<?php echo $eye_side ?>-eye-comments"
           class="js-element-eye <?php echo $eye_side ?>-eye <?php echo $page_side ?> disabled"
           data-side="<?php echo $eye_side ?>" style="display: <?=$this->action->id === "create" ? "none" : "" ?>">
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <div class="element-fields">
            <span class="field-info">General Comments:</span>
          </div>
        </div>
        <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
          <div class="element-fields">
              <?php echo $form->textArea($element, 'comments_' . $eye_side,
                  array('rows' => 3, 'label' => false, 'nowrapper' => true), false,
                  array('class' => 'comments_' . $eye_side)) ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>

</div>

<div id="comments" style="background-color: inherit">
    <span class="field-info large-12" style="display: <?=$this->action->id === "create" ? "none" : "" ?>">
        <?php
        if ($this->is_auto) {
            if (!$this->getAutoBiometryEventData($this->event->id)[0]->is700() || $element->{'comments'}) {
                echo 'Device Comments:';
                echo '<div class="readonly-box">' . $element->{'comments'} . '<br></div>';
            }
        } else {
            ?>
          <span class="field-info">Comments:</span>
            <?php
            echo $form->textField($element, 'comments', array('style' => 'width:1027px;', 'nowrapper' => true), null);
        }
        ?>
    </span>
</div>
