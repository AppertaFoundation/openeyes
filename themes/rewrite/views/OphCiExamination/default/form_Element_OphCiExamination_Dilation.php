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
$left_treatments = (isset($_POST['dilation_treatments_valid']) ? $element->convertTreatments(@$_POST['dilation_treatment'], 'left') : $element->left_treatments);
$right_treatments = (isset($_POST['dilation_treatments_valid']) ? $element->convertTreatments(@$_POST['dilation_treatment'], 'right') : $element->right_treatments);
$key = 0;
?>
<div class="element-fields element-eyes row">
	<input type="hidden" name="dilation_treatments_valid" value="1" />
	<?php echo $form->hiddenField($element, 'eye_id', array('class' => 'sideField'))?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {?> inactive<?php }?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="field-row">
				<?php echo $form->dropDownListNoPost('dilation_drug_right',$element->getUnselectedDilationDrugs('right'),'', array('class'=> 'dilation_drug', 'empty'=>'--- Please select ---', 'nowrapper' => true))?>
				<button class="small secondary clearDilation">
					Clear
				</button>
			</div>
			<table class="plain grid dilation_table"<?php if (!$right_treatments) {?> style="display: none;"<?php }?>>
				<thead>
					<tr>
						<th>Time</th>
						<th>Drug</th>
						<th>Drops</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="plain" id="dilation_right">
					<?php foreach ($right_treatments as $treatment) {
							$this->renderPartial('form_Element_OphCiExamination_Dilation_Treatment',array(
								'treatment' => $treatment,
								'key' => $key,
								'side' => $treatment->side,
								'drug_name' => $treatment->drug->name,
								'drug_id' => $treatment->drug_id,
							));
						$key++;
					}?>
				</tbody>
			</table>
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
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="field-row">
				<?php echo $form->dropDownListNoPost('dilation_drug_left',$element->getUnselectedDilationDrugs('left'),'', array('class'=> 'dilation_drug', 'empty'=>'--- Please select ---', 'nowrapper' => true))?>
				<button class="small secondary clearDilation">
					Clear
				</button>
			</div>
			<table class="plain grid dilation_table"<?php if (!$right_treatments) {?> style="display: none;"<?php }?>>
				<thead>
					<tr>
						<th>Time</th>
						<th>Drug</th>
						<th>Drops</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="plain" id="dilation_left">
					<?php foreach ($left_treatments as $treatment) {
							$this->renderPartial('form_Element_OphCiExamination_Dilation_Treatment',array(
								'treatment' => $treatment,
								'key' => $key,
								'side' => $treatment->side,
								'drug_name' => $treatment->drug->name,
								'drug_id' => $treatment->drug_id,
							));
						$key++;
					}?>
				</tbody>
			</table>
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
<script id="dilation_treatment_template" type="text/html">
	<?php
	$this->renderPartial('form_Element_OphCiExamination_Dilation_Treatment', array(
			'key' => '{{key}}',
			'side' => '{{side}}',
			'drug_name' => '{{drug_name}}',
			'drug_id' => '{{drug_id}}',
			'treatment_time' => '{{treatment_time}}',
	))?>
</script>
