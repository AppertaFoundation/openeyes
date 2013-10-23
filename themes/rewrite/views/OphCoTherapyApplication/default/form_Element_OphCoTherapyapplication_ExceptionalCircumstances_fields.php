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
//TODO: can drive this purely off the element attributes when we fix form processing
// Getting flags together for determining which elements to show
$need_reason = false;
$previnterventions = array();
$relevantinterventions = array();
if (@$_POST[get_class($element)]) {
	$exists = $_POST[get_class($element)][$side . '_standard_intervention_exists'];
	$intervention_id = $_POST[get_class($element)][$side . '_intervention_id'];
	if ($_POST[get_class($element)][$side . '_standard_previous'] == '0') {
		if ($id = $_POST[get_class($element)][$side . '_intervention_id']) {
			$intervention = Element_OphCoTherapyapplication_ExceptionalCircumstances_Intervention::model()->findByPk((int) $id);
			if ($intervention->is_deviation) {
				$need_reason = true;
			}
		}
	}
	if (isset($_POST[get_class($element)][$side . '_previnterventions'])) {
		foreach ($_POST[get_class($element)][$side . '_previnterventions'] as $attrs) {
			$prev = new OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention();
			$prev->attributes = $attrs;
			$prev->is_relevant = false;
			$previnterventions[] = $prev;
		}
	}
	if (isset($_POST[get_class($element)][$side . '_relevantinterventions'])) {
		foreach ($_POST[get_class($element)][$side . '_relevantinterventions'] as $attrs) {
			$past = new OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention();
			$past->attributes = $attrs;
			$past->is_relevant = true;
			$relevantinterventions[] = $past;
		}
	}
	$patient_factors = $_POST[get_class($element)][$side . '_patient_factors'];
} else {
	$exists = $element->{$side . '_standard_intervention_exists'};
	$intervention_id = $element->{$side . '_intervention_id'};
	$need_reason = $element->needDeviationReasonForSide($side);
	$previnterventions = $element->{$side . '_previnterventions'};
	$relevantinterventions = $element->{$side . '_relevantinterventions'};
	$patient_factors = $element->{$side . '_patient_factors'};
}
?>

<?php
$layoutColumns =array(
	'label' => 4,
	'field' => 8
);
echo $form->radioBoolean($element, $side . '_standard_intervention_exists', array('class' => 'standard_intervention_exists', ))?>



<span id="<?php echo get_class($element) . "_" . $side ?>_standard_intervention_details"
	<?php if ($exists != '1') {
		echo ' class="hidden"';
	}?>
	>

				<?php
				echo $form->dropDownList(
					$element,
					$side . '_standard_intervention_id',
					CHtml::listData($element->getStandardInterventionsForSide($side), 'id', 'name'),
					array('empty'=>'- Please select -')) ?>


		<?php echo $form->radioBoolean($element, $side . '_standard_previous', array('nowrapper' => true))?></div>

	<?php
	$opts = array('nowrapper' => true,
		'options' => array()
	);
	foreach (Element_OphCoTherapyapplication_ExceptionalCircumstances_Intervention::model()->findAll() as $intervention) {
		$opts['options'][$intervention->id] = array('data-description-label' => $intervention->description_label, 'data-is-deviation' => $intervention->is_deviation);
	}
	?>

	<fieldset class="row field-row intervention" id="<?php echo get_class($element) . "_" . $side;?>_intervention">
		<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_intervention_id'); ?></legend>
		<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->radioButtons($element, $side . '_intervention_id', 'et_ophcotherapya_exceptional_intervention', $element->{$side . '_intervention_id'}, 1, false, false, false, $opts)?></div>
	</fieldset>

	<?php echo $form->radioButtons($element, $side . '_intervention_id', 'et_ophcotherapya_exceptional_intervention', $element->{$side . '_intervention_id'}, 1, false, false, false, $opts)?>

		<fieldset class="row field-row" <?php if (!$intervention_id) { echo ' style="display: none;"'; } ?>>
			<legend class="large-<?php echo $layoutColumns['label']?> column end">
				<?php if ($intervention_id) {
					echo Element_OphCoTherapyapplication_ExceptionalCircumstances_Intervention::model()->findByPk((int)$intervention_id)->description_label;
				} else {
					$element->getAttributeLabel($side . '_description');
				}?>
			</legend>
			<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->textArea($element, $side . '_description',array('rows' => 4, 'cols' => 30, 'nowrapper' => true))?></div>
		</fieldset>

		<span id="<?php echo get_class($element) . "_" . $side;?>_deviation_fields"
			<?php if (!$need_reason) {?>
				class="hidden"
			<?php } ?>
			>
			<?php
			$html_options = array(
				'options' => array(),
				'empty' => '- Please select -',
				'div_id' =>  get_class($element) . '_' . $side . '_deviationreasons',
				'div_class' => 'elementField',
				'label' => $element->getAttributeLabel($side . '_deviationreasons'));

			echo $form->multiSelectList(
				$element,
				get_class($element) . '[' . $side . '_deviationreasons]',
				$side . '_deviationreasons', 'id',
				CHtml::listData($element->getDeviationReasonsForSide($side),'id','name'),
				array(),
				$html_options);
			?>
		</span>

	</span>

<span id="<?php echo get_class($element) . "_" . $side; ?>_standard_intervention_not_exists"
	<?php
	if ($exists != '0') {
		echo ' class="hidden"';
	}?>
	>
		<fieldset class="row field-row">
			<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_condition_rare'); ?></legend>
			<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->radioBoolean($element, $side . '_condition_rare', array('nowrapper' => true))?></div>
		</fieldset>

		<fieldset class="row field-row">
			<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_incidence'); ?></legend>
			<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->textArea($element, $side . '_incidence', array('rows' => 4, 'cols' => 30, 'nowrapper' => true))?></div>
		</fieldset>
	</span>

<fieldset class="row field-row">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_patient_different'); ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->textArea($element, $side . '_patient_different', array('rows' => 4, 'cols' => 30, 'nowrapper' => true))?></div>
</fieldset>

<fieldset class="row field-row">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_patient_gain'); ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->textArea($element, $side . '_patient_gain', array('rows' => 4, 'cols' => 30, 'nowrapper' => true))?></div>
</fieldset>

<fieldset class="row field-row" id="div_<?php echo get_class($element) . "_" . $side; ?>_previnterventions">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_previnterventions') ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column">
		<div class="previntervention-container">
			<?php
			$key = 0;
			foreach ($previnterventions as $prev) {
				$this->renderPartial('form_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
						'key' => $key,
						'pastintervention' => $prev,
						'side' => $side,
						'element_name' => get_class($element),
						'form' => $form,
					));
				$key++;
			}
			?>
		</div>
		<button class="addPrevintervention button small" type="button">
			Add
		</button>
	</div>
</fieldset>

<fieldset class="row field-row" id="div_<?php echo get_class($element) . "_" . $side; ?>_relevantinterventions">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_relevantinterventions') ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column">
		<div class="relevantintervention-container">
			<?php
			$key = 0;
			foreach ($relevantinterventions as $relevant) {
				$this->renderPartial('form_OphCoTherapyapplication_ExceptionalCircumstances_PastIntervention', array(
						'key' => $key,
						'pastintervention' => $relevant,
						'side' => $side,
						'element_name' => get_class($element),
						'form' => $form,
					));
				$key++;
			}
			?>
		</div>
		<button class="addRelevantintervention button small" type="button">
			Add
		</button>
	</div>
</fieldset>

<fieldset class="row field-row patient_factors">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_patient_factors'); ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->radioBoolean($element, $side . '_patient_factors', array('nowrapper' => true))?></div>
</fieldset>

<fieldset id="div_<?php echo get_class($element) . "_" . $side; ?>_patient_factor_details" class="elementField <?php if (!$patient_factors) { echo ' hidden'; } ?>">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_patient_factor_details'); ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->textArea($element, $side . '_patient_factor_details', array('rows' => 4, 'cols' => 30, 'nowrapper' => true))?></div>
</fieldset>

<fieldset id="div_<?php echo get_class($element) . "_" . $side; ?>_patient_expectations" class="elementField">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_patient_expectations'); ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->textArea($element, $side . '_patient_expectations', array('rows' => 4, 'cols' => 30, 'nowrapper' => true))?></div>
</fieldset>

<?php
$posted_sp = null;
$urgent = false;
if (@$_POST[get_class($element)]) {
	$posted_sp = $_POST[get_class($element)][$side . "_start_period_id"];
} else {
	$urgent = ($element->{$side . '_start_period'} && $element->{$side . '_start_period'}->urgent);
}
// get all the start periods and get data attribute for urgency requirements
$start_periods = $element->getStartPeriodsForSide($side);
$html_options = array('empty'=>'- Please select -', 'nowrapper' => true, 'options' => array());
foreach ($start_periods as $sp) {
	$html_options['options'][$sp->id] = array('data-urgent' => $sp->urgent);
	if ($posted_sp == $sp->id && $sp->urgent) {
		$urgent = true;
	}
}

?>
<fieldset class="row field-row start_period">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_start_period_id'); ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column">
		<?php
		echo $form->dropDownList(
			$element,
			$side . '_start_period_id',
			CHtml::listData($start_periods, 'id', 'name'),
			$html_options
		);
		?>
	</div>
</fieldset>

<fieldset id="<?php echo get_class($element) . '_' . $side ?>_urgency_reason"
	 class="elementField<?php if (!$urgent) {
		 echo ' hidden';} ?>">
	<legend class="large-<?php echo $layoutColumns['label']?> column end"><?php echo $element->getAttributeLabel($side . '_urgency_reason'); ?></legend>
	<div class="large-<?php echo $layoutColumns['field']?> column"><?php echo $form->textArea($element, $side . '_urgency_reason', array('rows' => 4, 'cols' => 30, 'nowrapper' => true))?></div>
</fieldset>

<?php
$html_options = array(
	'options' => array(),
	'empty' => '- Please select -',
	'div_id' =>  get_class($element) . '_' . $side . '_filecollections',
	'div_class' => 'elementField',
	'label' => 'File Attachments');
$collections = OphCoTherapyapplication_FileCollection::model()->findAll();
//TODO: have sorting with display_order when implemented
/*
$collections = OphCoTherapyapplication_FileCollection::::model()->findAll(array('order'=>'display_order asc'));
foreach ($collections as $collection) {
	$html_options['options'][(string) $collection->id] = array('data-order' => $collection->display_order);
}
*/
echo $form->multiSelectList($element, get_class($element) . '[' . $side . '_filecollections]', $side . '_filecollections', 'id', CHtml::listData($collections,'id','name'), array(), $html_options)
?>
