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
<section class="element edit full
        <?= is_subclass_of($element, 'SplitEventTypeElement') ? 'eye-divider' : '' ?>
        <?= $element->elementType->class_name ?>"
         data-element-type-id="<?php echo $element->elementType->id ?>"
         data-element-type-class="<?php echo $element->elementType->class_name ?>"
         data-element-type-name="<?php echo $element->elementType->name ?>"
         data-element-display-order="<?php echo $element->elementType->display_order ?>">

    <header class="element-header">
        <h4 class="element-title"><?php echo $element->elementType->name; ?></h4>
    </header>

    <?php if ($this->action->id === 'update' && !$element->event_id) { ?>
        <div class="alert-box alert">This element is missing and needs to be completed</div>
    <?php } ?>

    <?php echo $content; ?>

</section>
<?php if ($element->elementType->custom_hint_text) { ?>
    <div class="alert-box info <?= isset($ondemand) && $ondemand ? "hidden" : "" ?>
    <?= CHtml::modelName($element->elementType->class_name) ?>"
         data-element-type-class="<?= CHtml::modelName($element->elementType->class_name) ?>">
        <div class="user-tinymce-content">
            <?= $element->elementType->custom_hint_text ?>
        </div>
    </div>
<?php } ?>
