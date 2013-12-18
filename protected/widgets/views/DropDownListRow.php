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
<?php
$labelCols = $layoutColumns['label'];
$fieldCols = floor(12 - ($labelCols * count($fields))) / count($fields);
?>
<div id="div_<?php echo get_class($element); ?>" class="row field-row">
	<?php foreach ($fields as $i => $field) {?>
		<div class="large-<?php echo $labelCols;?> column">
			<label for="<?php echo get_class($element).'_'.$field;?>">
				<?php echo CHtml::encode($element->getAttributeLabel($field)); ?>:
			</label>
		</div>
		<div class="large-<?php echo $fieldCols;?> column end">
			<div class="row">
				<div class="large-<?php echo $layoutColumns['field'];?> column end">
					<?php echo CHtml::activeDropDownList($element,$field,$datas[$i],$htmlOptions[$i])?>
				</div>
			</div>
		</div>
	<?php }?>
</div>
