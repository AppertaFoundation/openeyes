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
        <?= $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
        <div id="<?= $eye_side ?>-eye-selection"
             class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?> <?php if (!$element->hasEye($eye_side)) { ?>inactive<?php } ?>"
             data-side="<?= $eye_side ?>" style="display: <?= $this->action->id === "create" ? "none" : "" ?>">
            <div class="active-form" style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <?= $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
                <?php if ($this->is_auto) { ?>
                    <label class="inline highlight"
                           for="<?= Chtml::modelName($element) . '_manually_overriden_' . $eye_side ?>">
                        <?= \CHtml::activeCheckBox($element, 'manually_overriden_' . $eye_side,
                            array('class' => 'js-manually-override-lens-selection ' . Chtml::modelName($element) . '_manually_overriden_' . $eye_side)); ?>
                        Manually override lens choice
                    </label>
                <?php } ?>
                <?php $manually_overriden = $element->{'manually_overriden_' . $eye_side}; ?>

                <?php $this->renderPartial('form_Element_OphInBiometry_Selection_fields',
                    array('side' => $eye_side, 'element' => $element, 'form' => $form, 'data' => $data,
                        'manual_override' => false,
                        'disable' => $manually_overriden
                    )); ?>
                <?php if ($this->is_auto) {
                    $this->renderPartial('form_Element_OphInBiometry_Selection_fields',

                        array('side' => $eye_side, 'element' => $element, 'form' => $form, 'data' => $data,
                            'manual_override' => true,
                            'disable' => !$manually_overriden));
                } ?>
            </div>
            <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="add-side">
                    Set <?= $eye_side ?> side lens type
                </div>
            </div>
        </div>
        <?php if ($this->action->id === "create") { ?>
            <div class="js-element-eye <?= $eye_side ?>-eye column">
                Selection editing is not available.
            </div>
        <?php } ?>
    <?php endforeach; ?>
</div>
