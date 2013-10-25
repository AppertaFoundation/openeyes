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

	<?php echo $form->hiddenInput($element, 'consultant_id')?>

	<header class="element-header">
		<h3 class="element-title"><?php echo $element->elementType->name; ?></h3>
	</header>
	<div class="element">
		<div class="element-fields">

			<?php
			$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
					'id'=>'OphTrConsent_consultantAutoComplete',
					'name'=>'OphTrConsent_consultantAutoComplete',
					'value'=>'',
					'sourceUrl'=>array('default/users'),
					'options'=>array(
						'minLength'=>'3',
						'select'=>"js:function(event, ui) {
								$('#Element_OphTrConsent_Other_consultant_id').val(ui.item.id);
								$('#consultant').val(ui.item.fullname);
								$('#OphTrConsent_consultantAutoComplete').val('');
								return false;
							}",
					),
					'htmlOptions'=>array(
						'style'=>'width: 320px;',
						'placeholder' => 'type to search for users'
					),
				));
			?>


			<?php echo CHtml::textField('consultant',$element->consultant ? $element->consultant->fullNameAndTitleAndQualifications: '',array('readonly'=>'readonly'))?>


			<?php echo $form->checkBox($element, 'information', array('text-align' => 'right'))?>
			<?php echo $form->checkBox($element, 'anaesthetic_leaflet', array('text-align' => 'right'))?>
			<?php echo $form->checkBox($element, 'witness_required', array('text-align' => 'right'))?>
			<?php echo $form->textField($element, 'witness_name', array('size' => '30','maxLength' => '255', 'hide' => (!@$_POST['Element_OphTrConsent_Other']['witness_required'] && !$element->witness_name)))?>
			<?php echo $form->checkBox($element, 'interpreter_required', array('text-align' => 'right'))?>
			<?php echo $form->textField($element, 'interpreter_name', array('size' => '30','maxLength' => '255', 'hide' => (!@$_POST['Element_OphTrConsent_Other']['interpreter_required'] && !$element->interpreter_name)))?>
			<?php echo $form->textField($element, 'parent_guardian', array('size' => '30','maxlength' => '255', 'hide' => $element->isAdult()))?>
			<?php echo $form->checkBox($element, 'include_supplementary_consent', array('text-align' => 'right'))?>
		</div>
	</div>
</section>
