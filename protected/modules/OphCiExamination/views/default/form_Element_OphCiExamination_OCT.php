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
    <div class="js-element-eye right-eye column left" data-side="right">
        <div class="active-form" style="display: <?= $element->hasRight()?'':'none'; ?>;">
      <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
            <?php $this->renderPartial($element->form_view.'_fields', array('side' => 'right', 'element' => $element, 'form' => $form))?>
        </div>
        <div class="inactive-form" style="display: <?= $element->hasRight()?'none':''; ?>;">
            <div class="add-side">
                <a href="#">
                    Add right side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
    <div class="js-element-eye left-eye column right" data-side="left">
        <div class="active-form" style="display: <?= $element->hasLeft()?'':'none'; ?>;">
      <a class="remove-side"><i class="oe-i remove-circle small"></i></a>
            <?php $this->renderPartial($element->form_view.'_fields', array('side' => 'left', 'element' => $element, 'form' => $form))?>
        </div>
        <div class="inactive-form" style="display: <?= $element->hasLeft()?'none':''; ?>;">
            <div class="add-side">
                <a href="#">
                    Add left side <span class="icon-add-side"></span>
                </a>
            </div>
        </div>
    </div>
</div>
