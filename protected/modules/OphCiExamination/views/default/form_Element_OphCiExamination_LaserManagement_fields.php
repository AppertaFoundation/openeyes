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
// Work out what to show in the form
$show_deferral = false;
$show_deferral_other = false;
$show_treatment = false;
$show_booking_hint = false;
$show_event_hint = false;
$status = null;
$deferralreason = null;

if (@$_POST[$model_name]) {
    $status = \OEModule\OphCiExamination\models\OphCiExamination_Management_Status::model()->findByPk(@$_POST[$model_name][$side . '_laser_status_id']);

    if ($deferral_id = @$_POST[$model_name][$side . '_laser_deferralreason_id']) {
        $deferralreason = \OEModule\OphCiExamination\models\OphCiExamination_Management_DeferralReason::model()->findByPk($deferral_id);
    }
} else {
    $status = $element->{$side . '_laser_status'};
    $deferralreason = $element->{$side . '_laser_deferralreason'};
}
if ($status) {
    if ($status->deferred) {
        $show_deferral = true;
    } elseif ($status->book) {
        $show_treatment = true;
        $show_booking_hint = true;
    } elseif ($status->event) {
        $show_treatment = true;
        $show_event_hint = true;
    }
}
if ($deferralreason && $deferralreason->other) {
    $show_deferral_other = true;
}

$model_name = CHtml::modelName($element);
?>

<div id="div_<?php echo $model_name . "_" . $side; ?>_laser"
	 class="row field-row">
	<div class="large-4 column">
		<label for="<?php echo $model_name.'_'.$side.'_laser_status_id';?>">
			<?php echo $element->getAttributeLabel($side . '_laser_status_id') ?>:
		</label>
	</div>
	<div class="large-8 column">
		<div class="row">
			<div class="large-9 column end">
				<?php echo CHtml::activeDropDownList($element, $side .'_laser_status_id', CHtml::listData($statuses, 'id', 'name'), $status_options)?>
			</div>
		</div>
		<span id="<?php echo $side ?>_laser_booking_hint" class="field-info hint"<?php if (!$show_booking_hint) {
    ?> style="display:none;"<?php 
} ?>></span>
		<?php if (Yii::app()->hasModule('OphTrLaser')) {
    $event = EventType::model()->find("class_name = 'OphTrLaser'");
    ?>
			<span id="<?php echo $side ?>_laser_event_hint" class="field-info hint"<?php if (!$show_event_hint) {
    ?> style="display:none;"<?php 
}
    ?>>Ensure a <?php echo $event->name ?> event is added for this patient when procedure is completed</span>
		<?php 
}?>
	</div>
</div>

<div id="div_<?php echo $model_name . "_" . $side; ?>_laser_deferralreason"
	 class="row field-row"
	<?php if (!$show_deferral) {
    ?>
		style="display: none;"
	<?php 
}?>
	>
	<div class="large-4 column">
		<label for="<?php echo $model_name.'_'.$side.'_laser_deferralreason_id';?>">
			<?php echo $element->getAttributeLabel($side . '_laser_deferralreason_id')?>:
		</label>
	</div>
	<div class="large-6 column end">
		<?php echo CHtml::activeDropDownList($element, $side . '_laser_deferralreason_id', CHtml::listData($deferrals, 'id', 'name'), $deferral_options)?>
	</div>
</div>

<div id="div_<?php echo $model_name . "_" . $side; ?>_laser_deferralreason_other"
	 class="row field-row"
	<?php if (!$show_deferral_other) {
    ?>
		style="display: none;"
	<?php 
} ?>
	>
	<div class="large-4 column">
		&nbsp;
	</div>
	<div class="large-8 column">
		<?php echo $form->textArea($element, $side . '_laser_deferralreason_other', array('rows' => "1", 'cols' => "40", 'class' => 'autosize', 'nowrapper' => true)) ?>
	</div>
</div>

<div class="field-row" id="<?php echo $model_name . '_' . $side;?>_treatment_fields"<?php if (!$show_treatment) {
    echo 'style="display: none;"';
}?>>
	<div class="row field-row lasertype">
		<div class="large-4 column">
			<label for="<?php echo $model_name.'_'.$side.'_lasertype_id';?>">
				<?php echo $element->getAttributeLabel($side . '_lasertype_id'); ?>:
			</label>
		</div>
		<div class="large-6 column end">
			<?php echo CHtml::activeDropDownList($element, $side . '_lasertype_id', CHtml::listData($lasertypes, 'id', 'name'), array('options' => $lasertype_options, 'empty'=>'- Please select -'))?>
		</div>
	</div>

	<?php
        $show_other = false;
        if (@$_POST[$model_name]) {
            if ($lasertype = \OEModule\OphCiExamination\models\OphCiExamination_LaserManagement_LaserType::model()->findByPk((int)@$_POST[$model_name][$side . '_lasertype_id'])) {
                $show_other = $lasertype->other;
            }
        } else {
            if ($lasertype = $element->{$side . '_lasertype'}) {
                $show_other = $lasertype->other;
            }
        }
    ?>

	<div class="row field-row lasertype_other<?php if (!$show_other) {
    echo " hidden";
}?>">
		<div class="large-4 column">
			<label for="<?php echo $model_name.'_'.$side.'_lasertype_other';?>">
				<?php echo $element->getAttributeLabel($side . '_lasertype_other'); ?>:
			</label>
		</div>
		<div class="large-8 column">
			<?php echo $form->textField($element, $side . '_lasertype_other', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'max' => 120, 'nowrapper' => true))?>
		</div>
	</div>

	<div class="row field-row comments">
		<div class="large-4 column">
			<label for="<?php echo $model_name.'_'.$side.'_comments';?>">
				<?php echo $element->getAttributeLabel($side . '_comments'); ?>:
			</label>
		</div>
		<div class="large-8 column">
			<?php echo $form->textArea($element, $side . '_comments', array('rows' => 4, 'nowrapper' => true))?>
		</div>
	</div>
</div>
