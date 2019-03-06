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
    $no_wrapper = false;
    if (@$htmlOptions['nowrapper']) {
        $no_wrapper = true;
        unset($htmlOptions['nowrapper']);
    }
    $htmlOptions['class'] = @$htmlOptions['class'];
    $htmlOptions['class'] .= ' dropDownTextSelection';
    if (@$htmlOptions['delimited']) {
        $htmlOptions['class'] .= ' delimited';
        unset($htmlOptions['delimited']);
    }
    $htmlOptions['id'] = 'dropDownTextSelection_'.CHtml::modelName($element).'_'.$field;
    if (!@$htmlOptions['empty']) {
        $htmlOptions['empty'] = 'Select';
    }
?>
<?php if (!$no_wrapper) { ?>
<div id="div_<?=\CHtml::modelName($element) ?>_<?php echo $field ?>_TextSelection" class="data-group">
	<div class="cols-<?php echo $layoutColumns['label'];?> column">
		<label for="<?php echo $htmlOptions['id'];?>">
			<?=\CHtml::encode($element->getAttributeLabel($field)) ?>:
		</label>
	</div>
	<div class="cols-<?php echo $layoutColumns['field'];?> column end">
		<?php }?>
		<?=\CHtml::dropDownList('', null, $options, $htmlOptions); ?>
		<?php if (!$no_wrapper) { ?>
	</div>
</div>
<?php } ?>
