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

<div class="element <?php echo $element->elementType->class_name?>"
	data-element-type-id="<?php echo $element->elementType->id ?>"
	data-element-type-class="<?php echo $element->elementType->class_name ?>"
	data-element-type-name="<?php echo $element->elementType->name ?>"
	data-element-display-order="<?php echo $element->elementType->display_order ?>">
	<h4 class="elementTypeName"><?php echo $element->elementType->name ?></h4>

	<?php echo $form->hiddenInput($element, 'draft', 1)?>

	<input type="hidden" id="re_default" value="<?php echo $element->calculateRe($element->event->episode->patient)?>" />

	<div class="row">
		<span class="left"></span>
		<span class="right">
			<?php echo $form->dropDownList($element, 'site_id', Site::model()->getLongListForCurrentInstitution(), array('nowrapper'=>true))?>
		</span>
	</div>

	<div class="row">
		<span class="left">
			<?php echo $form->dropDownListNoPost('address_target', $element->address_targets, $element->address_target, array('empty' => '- Recipient -', 'nowrapper' => true))?>
		</span>
		<span class="right">
			<?php echo $form->textArea($element, 'address', array('rows' => 7, 'cols' => 55, 'label' => false, 'nowrapper' => true))?>
		</span>
	</div>

	<div class="row">
		<span class="left">
			<?php echo $form->dropDownListNoPost('macro', $element->letter_macros, '', array('empty' => '- Macro -', 'nowrapper' => true))?>
		</span>
		<span class="right">
			<?php echo $form->datePicker($element, 'date', array('maxDate' => 'today'), array('nowrapper'=>true))?>
		</span>
	</div>

	<div class="eventDetail row">
		<div class="label OphCoCorrespondence_footerLabel">
			<?php echo $element->getAttributeLabel('clinic_date')?>:
		</div>
		<span class="right">
			<?php echo $form->datePicker($element, 'clinic_date', array('maxDate' => 'today'), array('nowrapper'=>true,'null'=>true))?>
		</span>
	</div>

	<?php echo $form->textField($element, 'direct_line')?>

	<div class="row">
		<span class="left"></span>
		<span class="right">
			<?php echo $form->textArea($element, 'introduction', array('rows' => 2, 'cols' => 55, 'label' => false, 'nowrapper' => true))?>
			<?php echo $form->checkBox($element, 'use_nickname', array('nowrapper' => true))?>
			<?php echo $element->getAttributeLabel('use_nickname')?>
		</span>
	</div>

	<div class="row"<?php if ((empty($_POST) && strlen($element->re) <1) || (!empty($_POST) && strlen(@$_POST['ElementLetter']['re']) <1)) {?> style="display: none;"<?php }?>>
		<span class="left"></span>
		<span class="right">
			<?php echo $form->textArea($element, 're', array('rows' => 2, 'cols' => 100, 'label' => false, 'nowrapper' => true), empty($_POST) ? strlen($element->re) == 0 : strlen(@$_POST['ElementLetter']['re']) == 0)?>
		</span>
	</div>

	<div class="row">
		<span class="left">
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
				echo $form->dropDownListNoPost(strtolower($string_group->name), $strings, '', array('empty' => '- '.$string_group->name.' -', 'nowrapper' => true, 'class' => 'stringgroup', 'disabled' => empty($strings)))?>
			<?php }?>
		</span>
		<span class="right">
			<?php echo $form->textArea($element, 'body', array('rows' => 20, 'cols' => 100, 'label' => false, 'nowrapper' => true))?>
		</span>
	</div>

	<div class="eventDetail row">
		<div class="label OphCoCorrespondence_footerLabel">From:</div>
		<span class="right">
			<div>
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
							'style'=>'width: 320px;',
							'placeholder' => 'type to search for users'
						),
					));
				?>
			</div>
			<div id="OphCoCorrespondence_footerDiv">
				<?php echo $form->textArea($element, 'footer', array('rows' => 9, 'cols' => 55, 'label' => false, 'nowrapper' => true))?>
			</div>
		</span>
	</div>

	<div class="row">
		<span class="left">
			<?php echo $form->dropDownListNoPost('cc', $element->address_targets, '', array('empty' => '- Cc -', 'nowrapper' => true))?>
		</span>
		<span class="right">
			<?php echo $form->textArea($element, 'cc', array('rows' => 8, 'cols' => 100, 'label' => false, 'nowrapper' => true))?>
		</span>
		<div id="cc_targets">
		</div>
	</div>

	<div class="eventDetail row enclosures">
		<input type="hidden" name="update_enclosures" value="1" />
		<div class="label OphCoCorrespondence_footerLabel">Enclosures:</div>
		<div class="right">
			<div id="enclosureItems">
				<?php if (is_array(@$_POST['EnclosureItems'])) {?>
					<?php foreach ($_POST['EnclosureItems'] as $key => $value) {?>
						<div class="enclosureItem"><?php echo CHtml::textField("EnclosureItems[$key]",$value,array('size'=>60))?><a href="#" class="removeEnclosure">Remove</a></div>
					<?php }?>
				<?php } else {?>
					<?php foreach ($element->enclosures as $i => $item) {?>
						<div class="enclosureItem"><?php echo CHtml::textField("EnclosureItems[enclosure$i]",$item->content,array('size'=>60))?><a href="#" class="removeEnclosure">Remove</a></div>
					<?php }?>
				<?php }?>
			</div>
			<div>
				<button class="addEnclosure classy green mini" type="button">
					<span class="button-span button-span-green">Add</span>
				</button>
			</div>
		</div>
	</div>
</div>
