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
	// for legacy, this is the original definition of the div id that was created for the MultiSelectFreeText
	// not recommended as it doesn't allow for sided uniqueness
	$div_id = "div_" . CHtml::modelName($element) . "_" . @$htmlOptions['label'];
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
	'sorted' => $sorted,
	'requires_description_field' => @$htmlOptions['requires_description_field']
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
		<div class="multi-select-free-text<?php if (!$inline) echo ' multi-select-free-text-list';?>" data-options='<?php echo $widgetOptionsJson;?>'>
			<input type="hidden" name="<?php echo CHtml::modelName($element)?>[MultiSelectFreeTextList_<?php echo $field?>]" class="multi-select-free-text-list-name" />
			<div class="multi-select-free-text-dropdown-container">
				<select id="<?php echo CHtml::getIdByName($field)?>" class="MultiSelectFreeTextList<?php if ($showRemoveAllLink) {?> inline<?php }?><?php if (isset($htmlOptions['class'])) {?> <?php echo $htmlOptions['class']?><?php }?>" name=""<?php if (isset($htmlOptions['data-linked-fields'])) {?> data-linked-fields="<?php echo $htmlOptions['data-linked-fields']?>"<?php }?><?php if (isset($htmlOptions['data-linked-values'])) {?> data-linked-values="<?php echo $htmlOptions['data-linked-values']?>"<?php }?>>
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
						echo ">" . strip_tags($option) . "</option>";
					}?>
				</select>
				<?php if ($showRemoveAllLink) {?>
					<a href="#" class="remove-all<?php echo !$found ? ' hide': '';?>">Remove all</a>
				<?php }?>
			</div>
			<?php if ($noSelectionsMessage) {?>
				<div class="no-selections-msg pill<?php if ($found) {?> hide<?php }?>"><?php echo $noSelectionsMessage;?></div>
			<?php }?>
			<ul class="MultiSelectFreeTextList multi-select-free-text-selections<?php if (!$found) echo ' hide';?>">
				<input type="hidden" name="<?php echo $field?>" />
				<?php foreach ($selected_ids as $i => $id) {
					if (isset($options[$id])) {?>
						<li>
							<span class="text">
								<?php echo htmlspecialchars($options[$id],ENT_QUOTES,Yii::app()->charset, false)?>
							</span>
							<a href="#" data-text="<?php echo $options[$id] ?>" class="MultiSelectFreeTextRemove remove-one<?php if (isset($htmlOptions['class'])) {?> <?php echo $htmlOptions['class']?><?php }?>"<?php if (isset($htmlOptions['data-linked-fields'])) {?> data-linked-fields="<?php echo $htmlOptions['data-linked-fields']?>"<?php }?><?php if (isset($htmlOptions['data-linked-values'])) {?> data-linked-values="<?php echo $htmlOptions['data-linked-values']?>"<?php }?>>Remove</a>
							<input type="hidden" name="<?php echo $field?>[<?php echo $i?>][id]" data-i="<?php echo $i?>" value="<?php echo $id?>"
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
			<div class="multi-select-free-text-descriptions">
				<?php foreach ($selected_ids as $i => $id) {
					if (isset($descriptions[$id])) {?>
						<div class="row data-row" data-option="<?php echo $options[$id]?>">
							<div class="large-2 column">
								<div class="data-label">
									<?php echo $options[$id]?>
								</div>
							</div>
							<div class="large-4 column end">
								<div class="data-value">
									<?php echo CHtml::textArea($field."[$i][description]",$descriptions[$id])?>
								</div>
							</div>
						</div>
					<?php }
				}?>
			</div>
		</div>
<?php if (!@$htmlOptions['nowrapper']) {?>
	</div>
</div>
<?php }?>
