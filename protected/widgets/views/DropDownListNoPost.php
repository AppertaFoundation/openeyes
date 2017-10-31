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
	<div id="div_<?php echo $id?>" class="row field-row">
		<div class="large-<?php echo $layoutColumns['label'];?> column">
			<?php if (!@$htmlOptions['nolabel']) {?>
				<label for="<?php echo $id?>"></label>
			<?php }?>
		</div>

		<div class="large-<?php echo $layoutColumns['field'];?> column end">
<?php }?>
		<select id="<?php echo $id?>"<?php if (@$htmlOptions['class']) {?> class="<?php echo $htmlOptions['class']?>"<?php }?><?php if (@$htmlOptions['disabled']) {?> disabled="disabled"<?php }?><?php if (@$htmlOptions['title']) {?> title="<?php echo $htmlOptions['title']?>"<?php }?>>
			<?php if (isset($htmlOptions['empty'])) {?>
				<option value="" data-order="0"><?php echo $htmlOptions['empty']?></option>
			<?php }?>
			<?php $order = 1; foreach ($data as $id => $option) {?>
				<option value="<?php echo $id?>"<?php if(@$htmlOptions['display_order']){echo ' data-order="'.$htmlOptions['display_order'][$id].'" ';}?><?php if ($id == $selected_value) {?> selected="selected"<?php }?>><?php echo CHtml::encode($option)?></option>
			<?php ++$order;}?>
		</select>
		<?php if (!@$htmlOptions['nowrapper']) {?>
		</div>
	</div>
<?php }?>
