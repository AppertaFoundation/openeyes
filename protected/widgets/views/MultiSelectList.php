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
if (isset($htmlOptions['options'])) {
	$opts = $htmlOptions['options'];
} else {
	$opts = array();
}

if (isset($htmlOptions['div_id'])) {
	$div_id = $htmlOptions['div_id'];
} else {
	// for legacy, this is the original definition of the div id that was created for the multiselect
	// not recommended as it doesn't allow for sided uniqueness
	$div_id = "div_" . get_class($element) . "_" . @$htmlOptions['label'];
}

if (isset($htmlOptions['div_class'])) {
	$div_class = $htmlOptions['div_class'];
} else {
	$div_class = "eventDetail";
}

$found = false;
foreach($selected_ids as $id) {
	if (isset($options[$id])) {
		$found = true;
		break;
	}
}

$widgetOptionsJson = json_encode(array(
	'sorted' => $sorted
));
?>

<?php if (!@$htmlOptions['nowrapper']) {?>
	<div id="<?php echo $div_id ?>" class="<?php echo $div_class ?> row field-row widget"<?php if ($hidden) {?> style="display: none;"<?php }?>>
		<div class="large-<?php echo $layoutColumns['label'];?> column">
			<label for="<?php echo $field?>">
				<?php echo @$htmlOptions['label']?>:
			</label>
		</div>
		<div class="large-<?php echo $layoutColumns['field'];?> column end">
	<?php }?>
		<div class="multi-select<?php if (!$inline) echo ' multi-select-list';?>" data-options='<?php echo $widgetOptionsJson;?>'>
			<input type="hidden" name="<?php echo get_class($element)?>[MultiSelectList_<?php echo $field?>]" class="multi-select-list-name" />
			<div class="multi-select-dropdown-container">
				<select id="<?php echo $field?>" class="MultiSelectList<?php if ($showRemoveAllLink) {?> inline<?php }?>" name="">
					<option value=""><?php echo $htmlOptions['empty']?></option>
					<?php foreach ($filtered_options as $value => $option) {
						$attributes = array('value' => $value);
						if (isset($opts[$value])) {
							$attributes = array_merge($attributes, $opts[$value]);
						}
						echo "<option";
						foreach ($attributes as $att => $att_val) {
							echo " " . $att . "=\"" . $att_val . "\"";
						}
						echo ">" . $option . "</option>";
					}?>
				</select>
				<?php if ($showRemoveAllLink) {?>
					<a href="#" class="remove-all<?php echo !$found ? ' hide': '';?>">Remove all</a>
				<?php }?>
			</div>
			<?php if (!$found && $noSelectionsMessage) {?>
				<div class="no-selections-msg pill"><?php echo $noSelectionsMessage;?></div>
			<?php }?>
			<ul class="MultiSelectList multi-select-selections<?php if (!$found) echo ' hide';?>">
				<?php foreach ($selected_ids as $id) {
					if (isset($options[$id])) {?>
						<li>
							<span class="text">
								<?php echo $options[$id] ?>
							</span>
							<a href="#" data-text="<?php echo $options[$id] ?>" class="MultiSelectRemove remove-one">Remove</a>
							<input type="hidden" name="<?php echo $field?>[]" value="<?php echo $id?>"
							<?php if (isset($opts[$id])) {
								foreach ($opts[$id] as $key => $val) {
									echo " " . $key . "=\"" . $val . "\"";
								}
							}?>
							/>
						</li>
					<?php }?>
				<?php }?>
			</ul>

		</div>
<?php if (!@$htmlOptions['nowrapper']) {?>
	</div>
</div>
<?php }?>
