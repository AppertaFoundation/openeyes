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
<?php $methods = CHtml::listData(\OEModule\OphCiExamination\models\OphCiExamination_AnteriorSegment_CCT_Method::model()->activeOrPk(array($element->right_method_id, $element->left_method_id))->findAll(array('order' => 'display_order')), 'id', 'name') ?>
<div class="element-eyes sub-element-fields">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'))?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
      <div class="js-element-eye <?=$eye_side?>-eye column <?=$page_side?>" data-side="<?=$eye_side?>">
      <div class="active-form" style="<?= !$element->hasEye($eye_side) ? "display: none;" : ""?>">
      <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
      <div class="cols-full flex-layout">
        <?php echo $form->textField(
            $element,
            $eye_side . '_value',
            array('autocomplete' =>
            SettingMetadata::model()->getSetting('html_autocomplete'),
            'nowrapper' => true,
            'append-text' => '&nbsp; &micro;m, using',
            'class' => 'cct_value')
        ) ?>
        <?php echo $form->dropDownList(
            $element,
            $eye_side . '_method_id',
            $methods,
            array('nowrapper' => true, 'class' => 'inline')
        ) ?>
        <div class="add-data-actions flex-item-bottom ">

          <button id="cct-<?= $eye_side ?>-comment-button"
              class="button js-add-comments" data-comment-container="#cct-<?= $eye_side ?>-comments"
              type="button" style="<?= $element->{$eye_side . '_notes'} ? 'visibility: hidden;' : '' ?>">
              <i class="oe-i comments small-icon"></i>
          </button>

        </div>
      </div>
      <div id="cct-<?= $eye_side ?>-comments" class="flex-layout flex-left comment-group js-comment-container"
           style="<?= !$element->{$eye_side . '_notes'} ? 'display: none;' : '' ?>" data-comment-button="#cct-<?= $eye_side ?>-comment-button">
            <?=\CHtml::activeTextArea(
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
      <div class="inactive-form side" style="<?= $element->hasEye($eye_side) ? "display: none;" : ""?>">
        <div class="add-side">
          <a href="#">
            Add <?=$eye_side?> eye <span class="icon-add-side"></span>
          </a>
        </div>
      </div>
    </div>
    <?php endforeach;?>
