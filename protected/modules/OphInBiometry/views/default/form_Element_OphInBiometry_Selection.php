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
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side): ?>
                <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
                <div id="<?php echo $eye_side?>-eye-selection"
                     class="element-eye <?php echo $eye_side?>-eye <?= $page_side ?> side<?php if (!$element->hasEye($eye_side)) {?> inactive<?php } ?>"
                     data-side="<?= $eye_side?>">
                    <div class="active-form">
                        <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
                        <?php $this->renderPartial('form_Element_OphInBiometry_Selection_fields',
                            array('side' => $eye_side, 'element' => $element, 'form' => $form, 'data' => $data)); ?>
                    </div>
                    <div class="inactive-form">
                        <div class="add-side">
                            Set <?php echo $eye_side ?> side lens type
                        </div>
                    </div>
                </div>
    <?php endforeach; ?>


</div>

<script type="text/javascript">
  $(document).ready(function () {
    if ($('section.Element_OphInBiometry_Selection').find('.element-eye.right-eye').hasClass('inactive')) {
      $('section.Element_OphInBiometry_Selection').find('.element-eye.right-eye').find('.active-form').hide();
      $('section.Element_OphInBiometry_Selection').find('.element-eye.right-eye').find('.inactive-form').show();
    } else {
      $('section.Element_OphInBiometry_Selection').find('.element-eye.right-eye').find('.active-form').show();
      $('section.Element_OphInBiometry_Selection').find('.element-eye.right-eye').find('.inactive-form').hide();
    }
    if ($('section.Element_OphInBiometry_Selection').find('.element-eye.left-eye').hasClass('inactive')) {
      $('section.Element_OphInBiometry_Selection').find('.element-eye.left-eye').find('.active-form').hide();
      $('section.Element_OphInBiometry_Selection').find('.element-eye.left-eye').find('.inactive-form').show();
    } else {
      $('section.Element_OphInBiometry_Selection').find('.element-eye.left-eye').find('.active-form').show();
      $('section.Element_OphInBiometry_Selection').find('.element-eye.left-eye').find('.inactive-form').hide();
    }
  });
</script>
