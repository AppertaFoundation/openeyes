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


<?php if (!@$htmlOptions['nowrapper']) {?>
    <div id="div_<?=\CHtml::modelName($element)?>_<?php echo $field?>" class="data-group flex-layout "<?php if (@$htmlOptions['hide']) {
        ?> style="display: none;"<?php
                 }?>>
        <div class="cols-<?php echo $layoutColumns['label'];?> column">
            <?php if (!@$htmlOptions['no-label']) {?>
                <label for="<?=\CHtml::modelName($element).'_'.$field;?>">
                    <?php if (!@$htmlOptions['text-align']) {?>
                        <?=\CHtml::encode($element->getAttributeLabel($field))?>:
                    <?php }?>
                </label>
            <?php }?>
        </div>
        <div class="cols-<?php echo $layoutColumns['field'];?> column end">
            <?=\CHtml::hiddenField(CHtml::modelName($element)."[$field]", '0', array('id' => CHtml::modelName($element).'_'.$field.'_hidden'))?>
            <?=\CHtml::checkBox(CHtml::modelName($element)."[$field]", $checked[$field], $htmlOptions)?>
            <?php if (@$htmlOptions['text-align'] == 'right') {?>
                <label for="<?=\CHtml::modelName($element).'_'.$field;?>" class="inline">
                    <?=\CHtml::encode($element->getAttributeLabel($field))?>
                </label>
            <?php }?>
        </div>
    </div>
<?php } else { ?>
    <?=\CHtml::hiddenField(CHtml::modelName($element)."[$field]", '0', array('id' => CHtml::modelName($element).'_'.$field.'_hidden'))?>
    <?php if (!@$htmlOptions['no-label']) {?>
    <label class="inline highlight">
    <?php }?>
        <?=\CHtml::checkBox(CHtml::modelName($element)."[$field]", $checked[$field], $htmlOptions)?>
    <?php if (!@$htmlOptions['no-label']) {?>
        <?=\CHtml::encode($element->getAttributeLabel($field))?>
    </label>
    <?php }?>
<?php }?>
