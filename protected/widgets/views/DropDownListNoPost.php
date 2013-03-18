<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php if (!@$htmlOptions['nowrapper']) {?>
	<div id="div_<?php echo $id?>" class="eventDetail">
		<div class="data">
<?php }?>
		<select id="<?php echo $id?>"<?php if (@$htmlOptions['class']) {?> class="<?php echo $htmlOptions['class']?>"<?php }?><?php if (@$htmlOptions['disabled']) {?> disabled="disabled"<?php }?><?php if (@$htmlOptions['title']) {?> title="<?php echo $htmlOptions['title']?>"<?php }?>>
			<?php if (isset($htmlOptions['empty'])) {?>
				<option value=""><?php echo $htmlOptions['empty']?></option>
			<?php }?>
			<?php foreach ($options as $id => $option) {?>
				<option value="<?php echo $id?>"<?php if ($id == $selected_value) {?> selected="selected"<?php }?>><?php echo $option?></option>
			<?php }?>
		</select>
		<?php if (!@$htmlOptions['nowrapper']) {?>
	</div>
</div>
<?php }?>
