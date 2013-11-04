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

$lasertypes = OphCiExamination_LaserManagement_LaserType::model()->findAll();
$lasertype_options = array();

foreach ($lasertypes as $lt) {
	$lasertype_options[(string)$lt->id] = array('data-other' => $lt->other);
}

$show_fields = false;
if (@$_POST[get_class($element)]) {
	if ($status = OphCiExamination_Management_Status::model()->findByPk((int)@$_POST[get_class($element)]['laser_status_id'])) {
		$show_fields = $status->book || $status->event;
	}
} else {
	if ($element->laser_status) {
		$show_fields = $element->laser_status->book || $element->laser_status->event;
	}
}
?>
<div class="sub-element-fields">
	<div class="row field-row">
		<div class="large-3 column">
			<label for="<?php echo get_class($element).'_laser_status_id';?>">
				<?php echo $element->getAttributeLabel('laser_status_id')?>:
			</label>
		</div>
		<div class="large-9 column">
			<?php
			$html_options = array('empty'=>'- Please select -', 'options' => array());
			foreach (OphCiExamination_Management_Status::model()->findAll(array('order'=>'display_order')) as $opt) {
				$html_options['options'][(string) $opt->id] = array('data-deferred' => $opt->deferred, 'data-book' => $opt->book, 'data-event' => $opt->event);
			}
			echo CHtml::activeDropDownList($element,'laser_status_id', CHtml::listData(OphCiExamination_Management_Status::model()->findAll(array('order'=>'display_order')),'id','name'), $html_options)?>
			<span id="laser_booking_hint" class="hint" style="display:none;"></span>
			<?php if (Yii::app()->hasModule('OphTrLaser')) {
				$event = EventType::model()->find("class_name = 'OphTrLaser'");
				?>
				<span id="laser_event_hint" class="hint" style="display:none;">Ensure a <?php echo $event->name ?> event is added for this patient when procedure is completed</span>
			<?php }?>
		</div>
	</div>
</div>

<div class="sub-element-fields jsLaserDeferral"<?php if (!($element->laser_status && $element->laser_status->deferred)) {?> style="display: none;"<?php }?> id="div_<?php echo get_class($element)?>_laser_deferralreason">
	<div class="row field-row">
		<div class="large-3 column">
			<label for="<?php echo get_class($element).'_laser_deferralreason_id';?>">
				<?php echo $element->getAttributeLabel('laser_deferralreason_id')?>:
			</label>
		</div>
		<div class="large-9 column">
			<?php
			$html_options = array('empty'=>'- Please select -', 'options' => array());
			foreach (OphCiExamination_Management_DeferralReason::model()->findAll(array('order'=>'display_order')) as $opt) {
				$html_options['options'][(string) $opt->id] = array('data-other' => $opt->other);
			}
			echo CHtml::activeDropDownList($element,'laser_deferralreason_id', CHtml::listData(OphCiExamination_Management_DeferralReason::model()->findAll(array('order'=>'display_order')),'id','name'), $html_options)?>
		</div>
	</div>
</div>

<div class="sub-element-fields jsLaserDeferralOther"<?php if (!($element->laser_deferralreason && $element->laser_deferralreason->other)) {?> style="display: none;"<?php }?> id="div_<?php echo get_class($element)?>_laser_deferralreason_other">
	<div class="row field-row">
		<div class="large-9 column large-offset-3">
			<?php echo $form->textArea($element, 'laser_deferralreason_other', array('rows' => "1", 'cols' => "80", 'class' => 'autosize', 'nowrapper' => true) ) ?>
		</div>
	</div>
</div>

<div class="element-eyes sub-element-fields jsTreatmentFields<?php if (!$show_fields) {?> hidden<?php }?>" id="div_<?php echo get_class($element)?>_treatment_fields">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'))?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) { ?> inactive<?php }?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php $this->renderPartial('form_' . get_class($element) . '_fields', array('side' => 'right', 'element' => $element, 'form' => $form, 'lasertypes' => $lasertypes, 'lasertype_options' => $lasertype_options))?>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column right side<?php if (!$element->hasLeft()) { ?> inactive<?php }?>" data-side="left">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php $this->renderPartial('form_' . get_class($element) . '_fields', array('side' => 'left', 'element' => $element, 'form' => $form, 'lasertypes' => $lasertypes, 'lasertype_options' => $lasertype_options)); ?>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add left side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
</div>
