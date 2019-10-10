<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<section class="<?php echo $element->elementType->class_name ?>"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="<?php echo $element->elementType->name ?>"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">
  <div class="element-fields element-eyes data-group">
        <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
            <?php $this->renderPartial("{$element->form_view}_side",
              array('form' => $form, 'element' => $element, 'side' => $eye_side)); ?>
        <?php endforeach; ?>
  </div>
</section>

