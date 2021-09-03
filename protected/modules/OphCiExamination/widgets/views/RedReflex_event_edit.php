<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var RedReflex $element
 * @var \OEModule\OphCiExamination\widgets\RedReflex $this
 */

use OEModule\OphCiExamination\models\RedReflex;

?>
<?php $model_name = CHtml::modelName($element); ?>
<div class="element-fields element-eyes">
    <?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) { ?>
        <div class="js-element-eye <?= $eye_side ?>-eye <?= $page_side ?>
          <?= $element->hasEye($eye_side) ? "" : "inactive" ?>"
             data-side="<?= $eye_side ?>"
             id="<?= $model_name ?>_<?= $eye_side ?>_form"
        >
            <div class="active-form"
                 style="<?= !$element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="remove-side"><i class="oe-i remove-circle small"></i></div>

                <div class="method flex-l">
                    <label><?= $element->getAttributeLabel("{$eye_side}_has_red_reflex") ?></label>
                    <label class="inline highlight">
                        <?=\CHtml::radioButton(
                            "{$model_name}[{$eye_side}_has_red_reflex]",
                            (string) $element->{"{$eye_side}_has_red_reflex"} === (string) RedReflex::HAS_RED_REFLEX,
                            [
                                'value' => RedReflex::HAS_RED_REFLEX,
                                'id' => \CHtml::getIdByName("{$model_name}[{$eye_side}_has_red_reflex]") . RedReflex::HAS_RED_REFLEX
                            ]
                        ); ?> Yes
                    </label>
                    <label class="inline highlight">
                        <?=\CHtml::radioButton(
                            "{$model_name}[{$eye_side}_has_red_reflex]",
                            (string) $element->{"{$eye_side}_has_red_reflex"} === (string) RedReflex::NO_RED_REFLEX,
                            [
                                'value' => RedReflex::NO_RED_REFLEX,
                                'id' => \CHtml::getIdByName("{$model_name}[{$eye_side}_has_red_reflex]") . RedReflex::NO_RED_REFLEX
                            ]
                        ); ?> No
                    </label>
                </div>
            </div>
            <div class="inactive-form" style="<?= $element->hasEye($eye_side) ? 'display: none;' : '' ?>">
                <div class="add-side">
                    <a href="#">
                        Add <?= $eye_side ?> side <span class="icon-add-side"></span>
                    </a>
                </div>
            </div>
        </div>
    <?php }; ?>
</div>

