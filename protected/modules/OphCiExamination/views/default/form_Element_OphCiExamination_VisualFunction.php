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

<div class="element-fields flex-layout full-width element-eyes ">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'));
	foreach(['left' => 'right', 'right' => 'left'] as $page_side => $eye_side){
	  $hasEyeFunc = 'has'.ucfirst($eye_side);
	?>
    <div class="element-eye <?=$eye_side?>-eye <?=$page_side?> side <?php if (!$element->$hasEyeFunc()) { ?> inactive <?php } ?> " data-side="<?= $eye_side?>">
      <div class="active-form field-row flex-layout">
        <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
        <div class="cols-full">
          <div class="cols-full">
            <table class="cols-full">
              <tbody>
              <tr>
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
                        ($element->{$eye_side .'_rapd'} !== null) ? $element->{$eye_side . '_rapd'} : 0,
                        false,
                        false,
                        false,
                        false,
                        array(
                            'text-align' => 'right',
                            'nowrapper' => false,
                        ),
                        array('label' => 2)
                    );
                    ?>
                </td>
                <td class="top">
                  <button id="visual-function-<?=$eye_side?>-comment_button"
                          class="button js-add-comments"
                          data-comment-container="#visual-function-<?=$eye_side?>-comments"
                          style="display: <?= !$element->{$eye_side.'_comments'}?:'none' ?>;"
                          type="button">
                    <i class="oe-i comments small-icon"></i>
                  </button>
                </td>
              </tr>
              </tbody>
            </table>
            <div id="visual-function-<?= $eye_side ?>-comments" class="field-row-pad-top cols-full js-comment-container"
                 style="display: <?= $element->{$eye_side . '_comments'} ?: 'none' ?>;">
                <?php
                echo $form->textArea(
                    $element,
                    $eye_side . '_comments',
                    array('rows' => 2, 'nowrapper' => true),
                    false,
                    array(
                        'class' => 'js-comment-field',
                        'placeholder' => $element->getAttributeLabel($eye_side . '_comments'),
                        'data-comment-button' => '#visual-function-' . $eye_side . '-comment_button',
                    ),
                    array('field' => 12)
                )
                ?>
            </div>
          </div>
        </div>
      </div>
      <!-- active form-->
      <div class="inactive-form" style="display: none">
        <div class="add-side">
          <a href="#">
            Add <?=$eye_side?> side <span class="icon-add-side"></span>
          </a>
        </div>
      </div>
    </div>
  <?php
	}
	?>
</div>
