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
	data-element-type-id="<?php echo $element->elementType->id ?>"
	data-element-type-class="<?php echo $element->elementType->class_name ?>"
	data-element-type-name="<?php echo $element->elementType->name ?>"
	data-element-display-order="<?php echo $element->elementType->display_order ?>">
	<?php echo $form->hiddenInput($element, 'draft', 1)?>
	<?php
	$layoutColumns = $form->layoutColumns;
	?>

	<header class="element-header">
		<h3 class="element-title"><?php echo $element->elementType->name ?></h3>
	</header>

	<div class="element-fields">

		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['field'];?> column large-offset-<?php echo $layoutColumns['label'];?> end">
				<?php echo $form->dropDownList($element, 'site_id', Site::model()->getLongListForCurrentInstitution(), array('nowrapper'=>true))?>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['label'];?> column">
				<?php echo $form->dropDownListNoPost('address_target', $element->address_targets, $element->address_target, array('empty' => '- Recipient -', 'nowrapper' => true, 'class' => 'full-width'))?>
			</div>
			<div class="large-6 column end">
				<?php echo $form->textArea($element, 'address', array('rows' => 7, 'label' => false, 'nowrapper' => true), false, array('class' => 'address'))?>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['label'];?> column">
				<?php echo $form->dropDownListNoPost('macro', $element->letter_macros, '', array('empty' => '- Macro -', 'nowrapper' => true, 'class' => 'full-width'))?>
			</div>
			<div class="large-2 column end">
				<?php echo $form->datePicker($element, 'date', array('maxDate' => 'today'), array('nowrapper'=>true))?>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['label'];?> column OphCoCorrespondence_footerLabel">
				<label for="<?php echo get_class($element).'_clinic_date_0';?>">
					<?php echo $element->getAttributeLabel('clinic_date')?>:
				</label>
			</div>
			<div class="large-2 column end">
				<?php echo $form->datePicker($element, 'clinic_date', array('maxDate' => 'today'), array('nowrapper'=>true,'null'=>true))?>
			</div>
		</div>

		<?php echo $form->textField($element, 'direct_line', array(), array(), array_merge($layoutColumns, array(
			'field' => 2
		)))?>

		<div class="row field-row">
			<div class="large-7 column large-offset-<?php echo $layoutColumns['label'];?>">
				<?php echo $form->textArea($element, 'introduction', array('rows' => 2, 'label' => false, 'nowrapper' => true), false, array('class' => 'address'))?>
			</div>
			<div class="large-3 column">
				<label>
					<?php echo $form->checkBox($element, 'use_nickname', array('nowrapper' => true))?>
					<?php echo $element->getAttributeLabel('use_nickname')?>
				</label>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['field'];?> column large-offset-<?php echo $layoutColumns['label'];?> end">
				<?php echo $form->textArea($element, 're', array('rows' => 2, 'label' => false, 'nowrapper' => true), false, array('class' => 'address'))?>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['label'];?> column">
				<?php
				$firm = Firm::model()->with('serviceSubspecialtyAssignment')->findByPk(Yii::app()->session['selected_firm_id']);

				$event_types = array();
				foreach (EventType::model()->with('elementTypes')->findAll() as $event_type) {
					$event_types[$event_type->class_name] = array();

					foreach ($event_type->elementTypes as $elementType) {
						$event_types[$event_type->class_name][] = $elementType->class_name;
					}
				}

				if (isset($_GET['patient_id'])) {
					$patient = Patient::model()->findByPk($_GET['patient_id']);
				} else {
					$patient = Yii::app()->getController()->patient;
				}

				$with = array(
					'firmLetterStrings' => array(
						'condition' => 'firm_id is null or firm_id = :firm_id',
						'params' => array(
							':firm_id' => $firm->id,
						),
						'order' => 'firmLetterStrings.display_order asc',
					),
					'subspecialtyLetterStrings' => array(
						'condition' => 'subspecialty_id is null',
						'order' => 'subspecialtyLetterStrings.display_order asc',
					),
					'siteLetterStrings' => array(
						'condition' => 'site_id is null or site_id = :site_id',
						'params' => array(
							':site_id' => Yii::app()->session['selected_site_id'],
						),
						'order' => 'siteLetterStrings.display_order',
					),
				);
				if ($firm->getSubspecialtyID()) {
					$with['subspecialtyLetterStrings']['condition'] = 'subspecialty_id is null or subspecialty_id = :subspecialty_id';
					$with['subspecialtyLetterStrings']['params'] = array(':subspecialty_id' => $firm->getSubspecialtyID());
				}
				foreach (LetterStringGroup::model()->with($with)->findAll(array('order'=>'t.display_order')) as $string_group) {
					$strings = $string_group->getStrings($patient,$event_types);
					?>
					<div class="field-row">
						<?php echo $form->dropDownListNoPost(strtolower($string_group->name), $strings, '', array(
							'empty' => '- '.$string_group->name.' -',
							'nowrapper' => true,
							'class' => 'stringgroup full-width',
							'disabled' => empty($strings)
						))?>
					</div>
				<?php }?>
			</div>
			<div class="large-<?php echo $layoutColumns['field'];?> column end">
				<?php echo $form->textArea($element, 'body', array('rows' => 20, 'label' => false, 'nowrapper' => true), false, array('class' => 'address'))?>
			</div>
		</div>

		<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['label'];?> column OphCoCorrespondence_footerLabel">
				<label for="OphCoCorrespondence_footerAutoComplete">
					From:
				</label>
			</div>
			<div class="large-<?php echo $layoutColumns['field'];?> column end">
				<div class="row field-row">
					<div class="large-6 column end">
						<?php
							$this->widget('zii.widgets.jui.CJuiAutoComplete', array(
								'id'=>'OphCoCorrespondence_footerAutoComplete',
								'name'=>'OphCoCorrespondence_footerAutoComplete',
								'value'=>'',
								'sourceUrl'=>array('default/users'),
								'options'=>array(
									'minLength'=>'3',
									'select'=>"js:function(event, ui) {
										$('#ElementLetter_footer').val(\"Yours sincerely\\n\\n\\n\\n\\n\"+ui.item.fullname+\"\\n\"+ui.item.role+\"\\n\"+(ui.item.consultant?\"Consultant: \"+ui.item.consultant:''));
										$('#OphCoCorrespondence_footerAutoComplete').val('');
										return false;
									}",
								),
								'htmlOptions'=>array(
									'placeholder' => 'type to search for users'
								),
							));
						?>
					</div>
				</div>
				<div class="row field-row" id="OphCoCorrespondence_footerDiv">
					<div class="large-8 column end">
						<?php echo $form->textArea($element, 'footer', array('rows' => 9, 'label' => false, 'nowrapper' => true), false, array('class' => 'address'))?>
					</div>
				</div>
			</div>
		</div>

	 	<div class="row field-row">
			<div class="large-<?php echo $layoutColumns['label'];?> column">
				<?php echo $form->dropDownListNoPost('cc', $element->address_targets, '', array('empty' => '- Cc -', 'nowrapper' => true))?>
			</div>
			<div class="large-<?php echo $layoutColumns['field'];?> column end">
				<?php echo $form->textArea($element, 'cc', array('rows' => 8, 'label' => false, 'nowrapper' => true), false, array('class' => 'address'))?>
			</div>
			<div id="cc_targets">
				<?php foreach ($element->cc_targets as $cc_target) {?>
					<input type="hidden" name="CC_Targets[]" value="<?php echo $cc_target?>" />
				<?php }?>
			</div>
		</div>

		<fieldset class="row field-row enclosures">
			<legend class="large-<?php echo $layoutColumns['label'];?> column OphCoCorrespondence_footerLabel">
				Enclosures:
			</legend>
			<div class="large-<?php echo $layoutColumns['field'];?> column end">
				<input type="hidden" name="update_enclosures" value="1" />
				<div id="enclosureItems" class="field-row<?php echo !is_array(@$_POST['EnclosureItems']) ? ' hide' : '';?>">
					<?php if (is_array(@$_POST['EnclosureItems'])) {?>
						<?php foreach ($_POST['EnclosureItems'] as $key => $value) {?>
							<div class="field-row row collapse enclosureItem">
								<div class="large-8 column">
									<?php echo CHtml::textField("EnclosureItems[$key]",$value,array('size'=>60))?>
								</div>
								<div class="large-4 column end">
									<div class="postfix align"><a href="#" class="field-info removeEnclosure">Remove</a></div>
								</div>
							</div>
						<?php }?>
					<?php }?>
				</div>
				<div class="field-row">
					<button class="addEnclosure secondary small" type="button">
						Add
					</button>
				</div>
			</div>
		</fieldset>

	</div>
</section>
