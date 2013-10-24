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
$name_stub = $element_name . '[' . $side;
if ($pastintervention->is_relevant) {
	$inttype_name = '_relevantinterventions';
	$treatmentattribute = 'relevanttreatment_id';
} else {
	$inttype_name = '_previnterventions';
	$treatmentattribute = 'treatment_id';
}
$name_stub .= $inttype_name . ']';

$show_stop_other = false;
$show_treatment_other = false;
if (@$_POST[$element_name] && @$_POST[$element_name][$side . $inttype_name] &&
    @$_POST[$element_name][$side . $inttype_name][$key]) {

	if ($stop_id = $_POST[$element_name][$side . $inttype_name][$key]['stopreason_id']) {
		$stopreason = OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention_StopReason::model()->findByPk((int)$stop_id);
		if ($stopreason->other) {
			$show_stop_other = true;
		}
	}
	if ($pastintervention->is_relevant &&
		$treatment_id = $_POST[$element_name][$side . $inttype_name][$key]['relevanttreatment_id']) {

		$treatment = OphCoTherapyapplication_RelevantTreatment::model()->findByPk((int) $treatment_id);
		if ($treatment->other) {
			$show_treatment_other = true;
		}
	}
} else {
	if ($pastintervention->stopreason && $pastintervention->stopreason->other) {
		$show_stop_other = true;
	}
	if ($pastintervention->is_relevant &&
		$pastintervention->relevanttreatment &&
		$pastintervention->relevanttreatment->other) {

		$show_treatment_other = true;
	}
}

/*
 * Am using a bit of a bastardisation of different form field approaches here as this many to many model form is not something that is supported well
 * by the OpenEyes extensions for forms. Will be worth tidying up as and when feasible (off the back of OE-2522)
 */

?>

<div class="pastintervention" data-key="<?php echo $key ?>">
	<a class="removePastintervention removeElementForm" href="#">Remove</a>
	<?php if ($pastintervention && $pastintervention->id) { ?>
		<input type="hidden"
			name="<?php echo $name_stub; ?>[<?php echo $key ?>][id]"
			value="<?php echo $pastintervention->id?>" />
	<?php } ?>

	<div>
		<div class="label"><?php echo $pastintervention->getAttributeLabel('start_date'); ?></div>
		<div class="data">
			<?php
				$d_name = $name_stub . "[$key][start_date]";
				$d_id = preg_replace('/\[/', '_', substr($name_stub, 0, -1)) . "_". $key ."_start_date";

				// using direct widget call to allow custom name for the field
				$form->widget('application.widgets.DatePicker',array(
					'element' => $pastintervention,
					'name' => $d_name,
					'field' => 'start_date',
					'options' => array('maxDate' => 'today'),
					'htmlOptions' => array('id' => $d_id, 'nowrapper' => true, 'style'=>'width: 90px;')));
			?>
		</div>
	</div>

	<div>
		<div class="label"><?php echo $pastintervention->getAttributeLabel('end_date'); ?></div>
		<div class="data">
			<?php
			$d_name = $name_stub . "[$key][end_date]";
			$d_id = preg_replace('/\[/', '_', substr($name_stub, 0, -1)) . "_". $key ."_end_date";

			// using direct widget call to allow custom name for the field
			$form->widget('application.widgets.DatePicker',array(
					'element' => $pastintervention,
					'name' => $d_name,
					'field' => 'end_date',
					'options' => array('maxDate' => 'today'),
					'htmlOptions' => array('id' => $d_id, 'nowrapper' => true, 'style'=>'width: 90px;')));
			?>
		</div>
	</div>

	<div>
		<div class="label"><?php echo $pastintervention->getAttributeLabel($treatmentattribute);?></div>
		<div class="data">
	<?php
		$all_treatments = $pastintervention->getTreatmentOptions();
		$html_options = array(
			'class' => 'past-treatments',
			'empty' => '- Please select -',
			'name' => $name_stub . "[$key][$treatmentattribute]",
			'options' => array(),
		);

		if ($pastintervention->is_relevant) {
			foreach ($all_treatments as $treatment) {
				$html_options['options'][$treatment->id] = array(
					'data-other' => $treatment->other,
				);
			}
		}

		echo CHtml::activeDropDownList($pastintervention, $treatmentattribute, CHtml::listData($all_treatments,'id','name'),
			$html_options);
	?>
		</div>
	</div>

	<div class="<?php if (!$show_treatment_other) { echo "hidden "; } ?>treatment-other">
		<div class="label"><?php echo $pastintervention->getAttributeLabel('relevanttreatment_other'); ?></div>
		<div class="data">
			<?php echo CHtml::activeTextField($pastintervention, 'relevanttreatment_other',array('name' => $name_stub . "[$key][relevanttreatment_other]")); ?>
		</div>
	</div>

	<div>
		<div class="label"><?php echo $pastintervention->getAttributeLabel('start_va');?></div>
		<div class="data">
			<?php
			echo CHtml::activeDropDownList($pastintervention, 'start_va', $pastintervention->getVaOptions(),
				array('empty'=>'- Please select -', 'name' => $name_stub . "[$key][start_va]", 'nowrapper' => true));
			?>
		</div>
	</div>

	<div>
		<div class="label"><?php echo $pastintervention->getAttributeLabel('end_va');?></div>
		<div class="data">
			<?php
			echo CHtml::activeDropDownList($pastintervention, 'end_va', $pastintervention->getVaOptions(),
				array('empty'=>'- Please select -', 'name' => $name_stub . "[$key][end_va]", 'nowrapper' => true));
			?>
		</div>
	</div>

	<div>
		<div class="label"><?php echo $pastintervention->getAttributeLabel('stopreason_id')?></div>
		<div class="data">
		<?php

		$reasons = OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention_StopReason::model()->findAll();
		$html_options = array(
				'class' => 'stop-reasons',
				'empty' => '- Please select -',
				'name' => $name_stub . "[$key][stopreason_id]",
				'options' => array(),
		);
		// get the previous injection counts for each of the drug options for this eye
		foreach ($reasons as $reason) {
			$html_options['options'][$reason->id] = array(
					'data-other' => $reason->other,
			);
		}

		echo CHtml::activeDropDownList($pastintervention, 'stopreason_id',
			CHtml::listData($reasons,'id','name'),
			$html_options);
		 ?>
		</div>
	</div>

	<div class="<?php if (!$show_stop_other) { echo "hidden "; } ?>stop-reason-other">
		<div class="label"><?php echo $pastintervention->getAttributeLabel('stopreason_other'); ?></div>
		<div class="data">
		<?php echo CHtml::activeTextArea($pastintervention, 'stopreason_other',array('name' => $name_stub . "[$key][stopreason_other]", 'rows' => 2, 'cols' => 25, 'nowrapper' => true))?>
		</div>
	</div>

	<div>
		<div class="label"><?php echo $pastintervention->getAttributeLabel('comments')?></div>
		<div class="data comments">
		<?php echo CHtml::activeTextArea($pastintervention, 'comments',array('placeholder' => 'Please provide pre and post treatment CMT', 'name' => $name_stub . "[$key][comments]", 'rows' => 3, 'cols' => 25, 'nowrapper' => true))?>
		</div>
	</div>
</div>
