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
<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side): ?>
      <div class="js-element-eye <?= $eye_side ?>-eye column <?= $page_side ?>" data-side="<?= $eye_side ?>">
        <div class="active-form flex-layout"
             style="<?php if (!$element->hasEye($eye_side)) { ?>display: none;<?php } ?>">
          <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
          <table class="cols-10">
            <tbody>
            <tr>
              <td>
                  <?= $element->getAttributeLabel($eye_side . '_rapd') ?>
              </td>
              <td>
                  <?php
                  echo $form->radioButtons(
                      $element,
                      $eye_side . '_rapd',
                      array(
                          1 => 'Yes',
                          2 => 'No',
                          0 => 'Not Checked',
                      ),
                      ($element->{$eye_side . '_rapd'} !== null) ? $element->{$eye_side . '_rapd'} : 0,
                      false,
                      false,
                      false,
                      false,
                      array(
                          'nowrapper' => true,
                      )
                  );
                  ?>
              </td>
            </tr>
            <tr>
              <td>
                  <?= $element->getAttributeLabel($eye_side . '_abnormality_id') ?>
              </td>
              <td>
                  <?php echo $form->dropDownList($element, $eye_side . '_abnormality_id',
                      $this->getPupilliaryAbnormalitiesList($element->{$eye_side . '_abnormality_id'}),
                      array('empty' => 'Select', 'nowrapper' => true),
                      false, array('nowrapper' => true)); ?>
              </td>
            </tr>
            <tr>
              <td colspan="2" id="pupils-<?= $eye_side ?>-comments" class="js-comment-container"
                  style="display: <?= $element->{$eye_side . '_comments'} ?: 'none' ?>;"
                  data-comment-button="#pupils-<?= $eye_side ?>-comment_button">
                <div class="flex-layout flex-left comment-group">
                  <?php
                  echo $form->textArea(
                      $element,
                      $eye_side . '_comments',
                      array('nowrapper' => true),
                      false,
                      array(
                          'class' => 'js-comment-field',
                          'placeholder' => $element->getAttributeLabel($eye_side . '_comments'),
                      )
                  )
                  ?>
                <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
          <div class="add-data-actions flex-item-bottom">
            <button id="pupils-<?= $eye_side ?>-comment_button"
                    class="button js-add-comments"
                    data-comment-container="#pupils-<?= $eye_side ?>-comments"
                    style="<?php if ($element->{$eye_side . '_comments'}): ?>visibility: hidden;<?php endif; ?>"
                    type="button">
              <i class="oe-i comments small-icon"></i>
            </button>
          </div>

        </div>
        <div class="inactive-form"
             style="<?php if ($element->hasEye($eye_side)) { ?>display: none;<?php } ?>">
          <div class="add-side">
            <a href="#">
              Add <?= $eye_side ?> side <span class="icon-add-side"></span>
            </a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
</div>
