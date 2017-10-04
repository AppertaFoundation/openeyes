<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$statuses = \OEModule\OphCiExamination\models\OphCiExamination_Management_Status::model()->activeOrPk(array($element->left_laser_status_id, $element->right_laser_status_id))->findAll();
$status_options = array('empty' => '- Please select -', 'options' => array());
foreach ($statuses as $opt) {
    $status_options['options'][(string) $opt->id] = array('data-deferred' => $opt->deferred, 'data-book' => $opt->book, 'data-event' => $opt->event);
}

$deferrals = \OEModule\OphCiExamination\models\OphCiExamination_Management_DeferralReason::model()->activeOrPk(array($element->left_laser_deferralreason_id, $element->right_laser_deferralreason_id))->findAll();
$deferral_options = array('empty' => '- Please select -', 'options' => array());
foreach ($deferrals as $opt) {
    $deferral_options['options'][(string) $opt->id] = array('data-other' => $opt->other);
}

$lasertypes = \OEModule\OphCiExamination\models\OphCiExamination_LaserManagement_LaserType::model()->activeOrPk(array($element->right_lasertype_id, $element->left_lasertype_id))->findAll();
$lasertype_options = array();

foreach ($lasertypes as $lt) {
    $lasertype_options[(string) $lt->id] = array('data-other' => $lt->other);
}
?>

<div class="element-eyes sub-element-fields jsTreatmentFields" id="div_<?php echo CHtml::modelName($element)?>_treatment_fields">
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField'))?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {
    ?> inactive<?php 
}?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php $this->renderPartial($element->form_view.'_fields',
                array('side' => 'right', 'element' => $element, 'form' => $form,
                    'statuses' => $statuses, 'status_options' => $status_options,
                    'deferrals' => $deferrals, 'deferral_options' => $deferral_options,
                    'lasertypes' => $lasertypes, 'lasertype_options' => $lasertype_options, )); ?>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column right side<?php if (!$element->hasLeft()) {
    ?> inactive<?php 
}?>" data-side="left">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<?php $this->renderPartial($element->form_view.'_fields',
                array('side' => 'left', 'element' => $element, 'form' => $form,
                    'statuses' => $statuses, 'status_options' => $status_options,
                    'deferrals' => $deferrals, 'deferral_options' => $deferral_options,
                    'lasertypes' => $lasertypes, 'lasertype_options' => $lasertype_options, )); ?>
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
