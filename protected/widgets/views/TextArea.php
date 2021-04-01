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
<?php if (!$nowrapper) :?>
    <div id="div_<?= CHtml::modelName($element)?>_<?= $field?>"
       class="data-group flex-layout"
       style="<?=($hidden) ? 'display: none':''?>" >
      <?php if (!$no_label) : ?>
        <div class="cols-<?= $layoutColumns['label'] ?> column">
          <label for="<?= CHtml::modelName($element) . "_$field" ?>">
              <?= (isset($htmlOptions['label']) ? CHtml::encode($element->getAttributeLabel($htmlOptions['label'])) . ':' : ($label ? CHtml::encode($element->getAttributeLabel($field)) . ':' : '')); ?>
          </label>
        </div>
        <?php endif; ?>
        <div class="cols-<?php echo $layoutColumns['field']?> column">
<?php endif;
$attr = array(
    'id' => (isset($htmlOptions['name']) ? $htmlOptions['name'] : CHtml::modelName($element).'_'.$field),
    'name' => (isset($htmlOptions['name']) ? $htmlOptions['name'] : CHtml::modelName($element).'['.$field.']'),
    'placeholder' => @$htmlOptions['placeholder'],
);
if ($rows) {
    $attr['rows'] = $rows;
}
if ($cols) {
    $attr['cols'] = $cols;
}
?>
<textarea class="<?= isset($htmlOptions['class']) ? $htmlOptions['class'] : 'cols-full column'?>"
    <?= CHtml::renderAttributes(array_merge($htmlOptions, $attr));?>
><?=\CHtml::encode($value)?>
</textarea>
<?php if (!$nowrapper) :
    if ($button) :?>
                <button type="submit" class="<?=$button['colour']?> <?=$button['size']?>"
                id="<?= CHtml::modelName($element)?>_<?= $button['id']?>"
                name="<?= CHtml::modelName($element)?>_<?= $button['id']?>">
                    <?= $button['label']?>
                </button>
    <?php endif;?>
        </div>
    </div>
<?php endif;?>
