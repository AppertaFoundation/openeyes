<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<h4 class="elementTypeName"><?php echo '<?php ';?> echo $element->elementType->name <?php echo '?>'; ?></h4>

<table class="subtleWhite normalText">
	<tbody>
<?php
if (isset($element)) {
	foreach ($element['fields'] as $field) {
		if ($field['type'] == 'Textbox') {?>
			<tr>
				<td width="30%"><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']?>'))<?php echo '?>'?>:</td>
				<td><span class="big"><?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> <?php echo '?>';?></span></td>
			</tr>
			<?php } elseif ($field['type'] == 'Textarea' || $field['type'] == 'Textarea with dropdown') {?>
			<tr>
				<td width="30%"><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']?>'))<?php echo '?>'?>:</td>
				<td><span class="big"><?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> <?php echo '?>';?></span></td>
			</tr>
			<?php } elseif ($field['type'] == 'Date picker') {?>
			<tr>
				<td width="30%"><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']?>'))<?php echo '?>'?>:</td>
				<td><span class="big"><?php echo '<?php ';?> echo CHtml::encode($element->NHSDate('<?php echo $field['name']?>')); <?php echo '?>'; ?></span></td>
			</tr>
			<?php } elseif ($field['type'] == 'Dropdown list') {?>
			<tr>
				<td width="30%"><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']?>'))<?php echo '?>'?>:</td>
				<td><span class="big"><?php echo '<?php ';?> echo $element-><?php echo preg_replace('/_id$/','',$field['name'])?> ? $element-><?php echo preg_replace('/_id$/','',$field['name'])?>-><?php echo $field['lookup_field']?> : 'None'<?php echo '?>';?></span></td>
			</tr>
			<?php } elseif ($field['type'] == 'Checkbox') {?>
			<tr>
				<td width="30%"><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']?>'))<?php echo '?>'?>:</td>
				<td><span class="big"><?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> ? 'Yes' : 'No' <?php echo '?>';?></span></td>
			</tr>
			<?php } elseif ($field['type'] == 'Radio buttons') {?>
			<tr>
				<td width="30%"><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']?>'))<?php echo '?>'?>:</td>
				<td><span class="big"><?php echo '<?php ';?> echo $element-><?php echo preg_replace('/_id$/','',$field['name']); ?> ? $element-><?php echo preg_replace('/_id$/','',$field['name'])?>->name : 'None'<?php echo '?>';?></span></td>
			</tr>
			<?php } elseif ($field['type'] == 'Boolean') {?>
			<tr>
				<td width="30%"><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']?>'))<?php echo '?>'?>:</td>
				<td><span class="big"> <?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> ? 'Yes' : 'No' <?php echo '?>';?></span></td>
			</tr>
			<?php } elseif ($field['type'] == 'EyeDraw') {?>
			<tr>
				<td colspan="2">
					<?php echo '<?php ';?>
					$this->widget('application.modules.eyedraw.OEEyeDrawWidget<?php echo $field['eyedraw_class']?>', array(
						'side'=>$element->eye->getShortName(),
						'mode'=>'view',
						'size'=><?php echo $field['eyedraw_size']?>,
						'model'=>$element,
						'attribute'=>'<?php echo $field['name']?>',
					));
					<?php echo '?>';?>
				</td>
			</tr>
			<?php if (@$field['extra_report']) {?>
				<tr>
					<td width="30%">Report:</td>
					<td><span class="big"><?php echo '<?php ';?>echo $element-><?php echo $field['name']?>2<?php echo '?>';?></span></td>
				</tr>
			<?php }?>
			<?php } elseif ($field['type'] == 'Multi select') {?>
				<tr>
					<td colspan="2">
						<div class="colThird">
							<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
							<div class="eventHighlight medium">
								<?php echo '<?php ';?> if (!$element-><?php echo @$field['multiselect_relation']?>) {<?php echo '?>';?>
									<h4>None</h4>
								<?php echo '<?php ';?> } else {<?php echo '?>';?>
									<h4>
										<?php echo '<?php ';?> foreach ($element-><?php echo @$field['multiselect_relation']?> as $item) {<?php echo '?>';?>
											<?php echo '<?php ';?> echo $item-><?php echo @$field['multiselect_lookup_table']?>->name<?php echo '?>';?><br/>
										<?php echo '<?php ';?> }<?php echo '?>';?>
									</h4>
								<?php echo '<?php ';?> }<?php echo '?>';?>
							</div>
						</div>
					</td>
				</tr>
			<?php }elseif ($field['type'] == 'Slider') {?>
				<tr>
					<td width="30%"><?php echo '<?php '?>echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>'))<?php echo '?>';?></td>
					<td><span class="big"><?php echo '<?php '?>echo $element-><?php echo $field['name']?><?php echo '?>'?></span></td>
				</tr>
			<?php }?>
		<?php
	}
}
?>
	</tbody>
</table>
