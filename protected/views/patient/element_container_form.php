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
<?php
$section_classes = array('element full edit');
$model_name = CHtml::modelName($element->elementType->class_name);
$section_classes[] = $model_name;
if ($this->isRequired($element)) {
    $section_classes[] = 'required';
}
if ($this->isHiddenInUI($element)) {
    $section_classes[] = 'hidden';
}
if (
    is_subclass_of($element, 'SplitEventTypeElement')
    || is_subclass_of($element, \OEModule\OphCiExamination\models\interfaces\SidedData::class)
) {
    $section_classes[] = 'eye-divider';
}

$element_Type = $element->getElementType();
$set_id = isset($this->set) ? $this->set->id : null;
?>

<?php if (!preg_match('/\[\-(.*)\-\]/', $element->elementType->name)) { ?>
    <section
            class="<?php echo implode(' ', $section_classes); ?>"
            data-element-type-id="<?php echo $element->elementType->id ?>"
            data-element-type-class="<?php echo CHtml::modelName($element->elementType->class_name) ?>"
            data-element-type-name="<?php echo $element->elementType->name ?>"
            data-element-display-order="<?= $element->getDisplayOrder($set_id); ?>"
            data-mandatory="<?= $this->isRequiredInUI($element) ? "true" : "false"?>"
            data-test="<?php echo str_replace(' ', '-', $element->elementType->name) . '-element-section' ?>"
            data-exclude-element-from-empty-discard-check="<?= $element->exclude_element_from_empty_discard_check ?>"
  >
        <?php
        if (isset($_POST['element_dirty'][$model_name])) {
            $element_dirty = $_POST['element_dirty'][$model_name];
        } elseif ($element->isNewRecord) {
            $element_dirty = $element->isDirtyWhenNewRecord() ? 1 : 0;
        } else {
            $element_dirty = 1;
        } ?>

        <input type="hidden" name="[element_dirty]<?=$model_name?>"
               value=<?=$element_dirty ?>>

        <?php if (!property_exists($element, 'hide_form_header') || !$element->hide_form_header) { ?>
            <header class="element-header">
                <!-- Add a element remove flag which is used when saving data -->
                <input type="hidden" name="[element_removed]<?php echo $model_name?>" value="0">
                <!-- Element title -->
                <h3 class="element-title"><?php echo $element->getFormTitle() ?></h3>
                <?php if (isset($this->clips['element-header-additional'])) { ?>
                    <?php
                    $this->renderClip('element-header-additional');
                    // don't want the header clip to repeat in child elements
                    unset($this->clips['element-header-additional']);
                    ?>
                <?php } ?>
            </header>
            <!-- Additional element title information -->
            <?php if (isset($this->clips['element-title-additional'])) { ?>
                <div class="element-title-additional">
                    <?php
                    $this->renderClip('element-title-additional');
                    // don't want the header clip to repeat in child elements
                    unset($this->clips['element-title-additional']);
                    ?>
                </div>
            <?php } ?>
            <!-- Element actions -->
            <div class="element-actions">
                <!-- order is important for layout because of Flex -->
                <?php if ($this->canViewPrevious($element) || $this->canCopy($element)) { ?>
                    <span class="js-duplicate-element" data-test="duplicate-element-<?= str_replace(' ', '-', $element->elementType->name) ?>">
                        <i class="oe-i duplicate"></i>
                    </span>
                <?php }
                // Remove MUST be last element
                if ($this->isRequiredInUI($element)) {
                    if ($this instanceof \OEModule\OphCiExamination\controllers\DefaultController) {
                        ?>
                    <span class="disabled js-has-tooltip" data-tooltip-content="<b>Mandatory Element</b><br/>Cannot be left blank">
                        <i class="oe-i medium-icon <?= $element->hasErrors() ? 'asterisk-red' : 'asterisk' ?>"></i>
                    </span>
                    <?php }
                } else { ?>
                    <span class="js-remove-element">
                        <?php if (!isset($no_bin) || $no_bin == false) { ?>
                            <i class="oe-i trash-blue"></i>
                        <?php } ?>
                    </span>
                <?php } ?>
            </div>
        <?php } ?>

        <?php echo $content; ?>

    </section>
<?php } else { ?>
    <section
            class="<?php echo implode(' ', $section_classes); ?>"
            data-element-type-id="<?php echo $element->elementType->id ?>"
            data-element-type-class="<?php echo CHtml::modelName($element->elementType->class_name) ?>"
            data-element-type-name="<?php echo $element->elementType->name ?>"
            data-element-display-order="<?php echo $element->elementType->display_order ?>">

        <?php echo $content; ?>

    </section>
<?php } ?>
<?php if ($element->elementType->custom_hint_text) { ?>
<div class="alert-box info <?= CHtml::modelName($element->elementType->class_name) ?>">
    <div class="user-tinymce-content">
        <?= $element->elementType->custom_hint_text ?>
    </div>
</div>
<?php } ?>
