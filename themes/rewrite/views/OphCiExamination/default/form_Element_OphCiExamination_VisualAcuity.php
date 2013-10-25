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
list($values, $val_options) = $element->getUnitValuesForForm();
$methods = CHtml::listData(OphCiExamination_VisualAcuity_Method::model()->findAll(array('order'=>'display_order')),'id','name');
$key = 0;
$right_readings = (isset($_POST['visualacuity_readings_valid']) ? $element->convertReadings(@$_POST['visualacuity_reading'], 'right') : $element->getFormReadings('right'));
$left_readings = (isset($_POST['visualacuity_readings_valid']) ? $element->convertReadings(@$_POST['visualacuity_reading'], 'left') : $element->getFormReadings('left'));
?>
<div class="element-fields element-eyes row">
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {?> inactive<?php }?>" data-side="right">
		<a href="#" class="icon-remove-side removeSide">Remove side</a>
		<div class="active-form">
			<table class="blank">
				<thead>
					<?php if ($element->isNewRecord) {?>
						<tr>
							<th colspan="3">
								<?php echo CHtml::dropDownList('visualacuity_unit_change', @$element->unit_id, CHtml::listData($element->getUsableUnits(), 'id', 'name'))?>
							</th>
						</tr>
					<?php }elseif ($element->unit->information) {?>
						<tr>
							<th colspan="3"><?php echo $element->unit->information?></th>
						</tr>
					<?php }?>
				</thead>
				<tbody>
					<?php foreach ($right_readings as $reading) {
						// Adjust currently element readings to match unit steps
						$reading->loadClosest($element->unit->id);
						$this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
							'key' => $key,
							'reading' => $reading,
							'side' => $reading->side,
							'values' => $values,
							'val_options' => $val_options,
							'methods' => $methods,
					));
					$key++;
					}?>
				</tbody>
			</table>
			<div class="field-row">
				<button class="button small secondary addReading">
					Add
				</button>
			</div>
			<div class="field-row">
				<textarea rows="1"></textarea>
			</div>
		</div>
		<div class="inactive-form">
			<div class="add-side">
				<a href="#">
					Add right side <span class="icon-add-side"></span>
				</a>
			</div>
		</div>
	</div>
	<div class="element-eye left-eye column right side<?php if (!$element->hasLeft()) {?> inactive<?php }?>" data-side="left">
		<a href="#" class="icon-remove-side removeSide">Remove side</a>
		<div class="active-form">
			<table class="blank">
				<thead>
					<?php if ($element->isNewRecord) {?>
						<tr>
							<th colspan="3">
								<?php echo CHtml::dropDownList('visualacuity_unit_change', @$element->unit_id, CHtml::listData($element->getUsableUnits(), 'id', 'name'))?>
							</th>
						</tr>
					<?php }elseif ($element->unit->information) {?>
						<tr>
							<th colspan="3"><?php echo $element->unit->information?></th>
						</tr>
					<?php }?>
				</thead>
				<tbody>
					<?php foreach ($left_readings as $reading) {
						// Adjust currently element readings to match unit steps
						$reading->loadClosest($element->unit->id);
						$this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
							'key' => $key,
							'reading' => $reading,
							'side' => $reading->side,
							'values' => $values,
							'val_options' => $val_options,
							'methods' => $methods,
					));
					$key++;
					}?>
				</tbody>
			</table>
			<div class="field-row">
				<button class="button small secondary addReading">
					Add
				</button>
			</div>
			<div class="field-row">
				<textarea rows="1"></textarea>
			</div>
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
<script id="visualacuity_reading_template" type="text/html">
	<?php
	$this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
			'key' => '{{key}}',
			'side' => '{{side}}',
			'values' => $values,
			'val_options' => $val_options,
			'methods' => $methods,
	));
	?>
</script>
<script type="text/javascript">
	$(document).ready(function() {
		OphCiExamination_VisualAcuity_method_ids = [ <?php
		$first = true;
		foreach ($methods as $index => $method) {
			if (!$first) {
				echo ', ';
			}
			$first = false;
			echo $index;
		} ?> ];
	});
</script>
<?php
/*
list($values, $val_options) = $element->getUnitValuesForForm();
$methods = CHtml::listData(OphCiExamination_VisualAcuity_Method::model()->findAll(array('order'=>'display_order')),'id','name');
$key = 0;
?>
<?php if ($element->isNewRecord) { ?>
<div class="clearfix">
	<div style="margin-top:-25px; margin-left:120px;">
		<?php echo CHtml::dropDownList('visualacuity_unit_change', @$element->unit_id, CHtml::listData($element->getUsableUnits(), 'id', 'name')); ?>
	</div>
</div>
<?php } ?>
<?php if ($element->unit->information) {?>
<div class="clearfix">
	<div style="margin-top:-22px; margin-left:240px; font-size: 10px;">
		<i><?php echo $element->unit->information ?></i>
	</div>
</div>
<?php } ?>
<div class="cols2 clearfix">
	<input type="hidden" name="visualacuity_readings_valid" value="1" />
	<?php echo $form->hiddenInput($element, 'unit_id', false); ?>
	<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
	<div
		class="side left eventDetail<?php if (!$element->hasRight()) { ?> inactive<?php } ?>"
		data-side="right">
		<div class="activeForm">
			<a href="#" class="removeSide">-</a>
			<div class="data">
				<?php $right_readings = (isset($_POST['visualacuity_readings_valid']) ? $element->convertReadings(@$_POST['visualacuity_reading'], 'right') : $element->getFormReadings('right')); ?>
				<table <?php if (!$right_readings) { ?> style="display: none;"
<?php } ?>>
					<thead>
						<tr>
							<th><?php echo $element->unit->name ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($right_readings as $reading) {
							// Adjust currently element readings to match unit steps
							$reading->loadClosest($element->unit->id);
							$this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
								'key' => $key,
								'reading' => $reading,
								'side' => $reading->side,
								'values' => $values,
								'val_options' => $val_options,
								'methods' => $methods,
						));
						$key++;
						}?>
					</tbody>
				</table>
				<div class="data noReadings" <?php if ($right_readings) { ?>
					style="display: none;" <?php } ?>>Not recorded</div>
				<button class="addReading classy green mini" type="button">
					<span class="button-span button-span-green">Add</span>
				</button>
			</div>
			<?php if ($element->right_comments || $element->getSetting('notes')) { ?>
			<div class="data">
				<?php echo $form->textArea($element, 'right_comments', array('class' => 'autosize', 'rows' => 1, 'cols' => 62, 'nowrapper'=>true)) ?>
			</div>
			<?php } ?>
		</div>
		<div class="inactiveForm">
			<a href="#">Add right side</a>
		</div>
	</div>
	<div
		class="side right eventDetail<?php if (!$element->hasLeft()) { ?> inactive<?php } ?>"
		data-side="left">
		<div class="activeForm">
			<a href="#" class="removeSide">-</a>
			<div class="data">
				<?php $left_readings = (isset($_POST['visualacuity_readings_valid']) ? $element->convertReadings(@$_POST['visualacuity_reading'], 'left') : $element->getFormReadings('left')); ?>
				<table <?php if (!$left_readings) { ?> style="display: none;"
						<?php } ?>>
					<thead>
						<tr>
							<th><?php echo $element->unit->name ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($left_readings as $reading) {
							// Adjust currently element readings to match unit steps
							$reading->loadClosest($element->unit->id);
							$this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
								'key' => $key,
								'reading' => $reading,
								'side' => $reading->side,
								'values' => $values,
								'val_options' => $val_options,
								'methods' => $methods,
						));
						$key++;
						}?>
					</tbody>
				</table>
				<div class="data noReadings" <?php if ($left_readings) { ?>
					style="display: none;" <?php } ?>>Not recorded</div>
				<button class="addReading classy green mini" type="button">
					<span class="button-span button-span-green">Add</span>
				</button>
			</div>
			<?php if ($element->left_comments || $element->getSetting('notes')) { ?>
			<div class="data">
				<?php echo $form->textArea($element, 'left_comments', array('class' => 'autosize', 'rows' => 1, 'cols' => 62, 'nowrapper'=>true)) ?>
			</div>
			<?php } ?>
		</div>
		<div class="inactiveForm">
			<a href="#">Add left side</a>
		</div>
	</div>
</div>

<script id="visualacuity_reading_template" type="text/html">
	<?php
	$this->renderPartial('form_Element_OphCiExamination_VisualAcuity_Reading', array(
			'key' => '{{key}}',
			'side' => '{{side}}',
			'values' => $values,
			'val_options' => $val_options,
			'methods' => $methods,
	));
	?>
</script>

<script type="text/javascript">
	$(document).ready(function() {
		OphCiExamination_VisualAcuity_method_ids = [ <?php
		$first = true;
		foreach ($methods as $index => $method) {
			if (!$first) {
				echo ', ';
			}
			$first = false;
			echo $index;
		} ?> ];
	});
</script>
*/
