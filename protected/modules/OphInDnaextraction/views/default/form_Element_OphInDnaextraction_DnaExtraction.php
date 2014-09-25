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
<section class="element <?php echo $element->elementType->class_name?>"
	data-element-type-id="<?php echo $element->elementType->id?>"
	data-element-type-class="<?php echo $element->elementType->class_name?>"
	data-element-type-name="<?php echo $element->elementType->name?>"
	data-element-display-order="<?php echo $element->elementType->display_order?>">
	<fieldset class="element-fields">
		<?php echo $form->dropDownList($element, 'box_id', CHtml::listData(OphInDnaextraction_DnaExtraction_Box::model()->findAll(array('order'=>'display_order')),'id','value'), array('empty' => '- Select -'), false, array('label' => 3, 'field' => 9))?>
		<?php echo $form->dropDownList($element, 'letter_id', CHtml::listData(OphInDnaextraction_DnaExtraction_Letter::model()->findAll(array('order'=>'display_order')),'id','value'), array('empty' => '- Select -'), false, array('label' => 3, 'field' => 9))?>
		<?php echo $form->dropDownList($element, 'number_id', CHtml::listData(OphInDnaextraction_DnaExtraction_Number::model()->findAll(array('order'=>'display_order')),'id','value'), array('empty' => '- Select -'), false, array('label' => 3, 'field' => 9))?>
		<?php echo $form->datePicker($element, 'extracted_date', array('maxDate' => 'today'), array(), array('label' => 3, 'field' => 2))?>
		<?php echo $form->textField($element, 'extracted_by', array(), array(), array('label' => 3, 'field' => 2))?>
		<?php echo $form->textField($element, 'dna_concentration', array(), array(), array('label' => 3, 'field' => 2))?>
		<?php echo $form->textField($element, 'volume', array(), array(), array('label' => 3, 'field' => 2));
		if($this->action->id=="update") {
			$form->widget('Caption',
				array(
					'label'=>'Volume Remaining',
					'value'=>$this->volumeRemaining($element->event_id),
				));
		}
		?>
		<?php echo $form->textArea($element, 'comments', array(), false, array(), array('label' => 3, 'field' => 5))?>
	</fieldset>
</section>
