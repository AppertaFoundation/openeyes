<?php echo '<?php '; ?>
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
<?php echo ' ?>'; ?>

<div class="<?php echo '<?php '; ?>echo $element->elementType->class_name<?php echo '?>'; ?>">
	<h4 class="elementTypeName"><?php echo '<?php '; ?> echo $element->elementType->name; <?php echo '?>'; ?></h4>

	<?php
	if (isset($element)) {
		foreach ($element['fields'] as $field) {
	if ($field['type'] == 'Textbox') {?>
		<?php echo '<?php '; ?>echo $form->textField($element, '<?php echo $field['name']; ?>', array('size' => '10')); <?php echo '?>' ;?>

	<?php } elseif ($field['type'] == 'Textarea') {?>

		<?php echo '<?php '; ?>echo $form->textArea($element, '<?php echo $field['name']; ?>', array('rows' => 6, 'cols' => 80)); <?php echo '?>' ;?>

	<?php } elseif ($field['type'] == 'Date picker') {?>

		<?php echo '<?php '; ?>echo $form->datePicker($element, '<?php echo $field['name']; ?>', array('maxDate' => 'today'), array('style'=>'width: 110px;')); <?php echo '?>'; ?>

	<?php } elseif ($field['type'] == 'Dropdown list') {?>

		<?php echo '<?php '; ?>echo $form->dropDownList($element, '<?php echo $field['name']?>', CHtml::listData(<?php echo $field['lookup_class']?>::model()->findAll(),'id','name'),array('empty'=>'- Please select -')); <?php echo '?>'; ?>

	<?php } elseif ($field['type'] == 'Textarea with dropdown') {?>

		<?php echo '<?php '; ?>echo $form->dropDownListNoPost('<?php echo $field['name']?>', CHtml::listData(<?php echo $field['lookup_class']?>::model()->findAll(),'id','name'),'',array('empty'=>'- <?php echo ucfirst($field['label'])?> -','class'=>'populate_textarea')); <?php echo '?>'; ?>
		<?php echo '<?php '; ?>echo $form->textArea($element, '<?php echo $field['name']?>', array('rows' => 6, 'cols' => 80)); <?php echo '?>' ;?>

	<?php } elseif ($field['type'] == 'Checkbox') {?>

		<?php echo '<?php '; ?>echo $form->checkBox($element, '<?php echo $field['name']; ?>'); <?php echo '?>'; ?>

	<?php } elseif ($field['type'] == 'Radio buttons') {?>

		(Radio buttons go here)

	<?php } elseif ($field['type'] == 'Boolean') {?>

		<?php echo '<?php '; ?>echo $form->radioBoolean($element, '<?php echo $field['name']; ?>'); <?php echo '?>' ;?>

	<?php } elseif ($field['type'] == 'EyeDraw') {?>
		<?php echo '<?php '; ?>
			$this->widget('application.extensions.eyedraw.OEEyeDrawWidget', array(
				'identifier'=> 'Buckle',
				'side'=>'R',
				'mode'=>'edit',
				'size'=>300,
				'model'=>$element,
				'attribute'=>'eyedraw',
				'doodleToolBarArray'=>array('CircumferentialBuckle','EncirclingBand','RadialSponge','BuckleSuture','DrainageSite'),
				'onLoadedCommandArray'=>array(
					array('addDoodle', array('BuckleOperation')),
					array('deselectDoodles', array()),
				)));
	<?php echo '?>'; ?>

				<?php
			}
		}
	}
	?>
</div>
