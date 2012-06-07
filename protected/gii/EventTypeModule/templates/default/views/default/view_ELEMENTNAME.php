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

<div class="view">

<?php
if (isset($element)) {
	foreach ($element['fields'] as $field) {
		if ($field['type'] == 'Textbox') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> <?php echo '?>';?>
				<br />
			</div>
			<?php } elseif ($field['type'] == 'Textarea') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> <?php echo '?>';?>
				<br />
			</div>
			<?php } elseif ($field['type'] == 'Date picker') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo CHtml::encode($this->getNHSDate($element-><?php echo $field['name']; ?>)); <?php echo '?>'; ?>
				<br />
			</div>
			<?php } elseif ($field['type'] == 'Dropdown list') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo $element-><?php echo preg_replace('/_id$/','',$field['name'])?> ? $element-><?php echo preg_replace('/_id$/','',$field['name'])?>->name : 'None'<?php echo '?>';?>
				<br />
			</div>
			<?php } elseif ($field['type'] == 'Checkbox') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> ? 'Yes' : 'No' <?php echo '?>';?>
				<br />
			</div>
			<?php } elseif ($field['type'] == 'Radio buttons') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> <?php echo '?>';?>
				<br />
			</div>
			<?php } elseif ($field['type'] == 'Boolean') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> ? 'Yes' : 'No' <?php echo '?>';?>
				<br />
			</div>
			<?php } elseif ($field['type'] == 'EyeDraw') {?>
			<div class="view">
				<b><?php echo '<?php ';?> echo CHtml::encode($element->getAttributeLabel('<?php echo $field['name']; ?>')); <?php echo '?>';?>:</b>
				<?php echo '<?php ';?> echo $element-><?php echo $field['name']; ?> <?php echo '?>';?>
				<br />
			</div>
			<?php }?>

		<?php
	}
}
?>
</div>

