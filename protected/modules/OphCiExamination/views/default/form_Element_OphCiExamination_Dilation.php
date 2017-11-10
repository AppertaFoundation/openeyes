<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/Dilation.js", CClientScript::POS_END);

$key = 0;
$dilation_drugs = \OEModule\OphCiExamination\models\OphCiExamination_Dilation_Drugs::model()->findAll();

$dilation_drugs_order = array();
foreach ($dilation_drugs as $d_drug) {
    $dilation_drugs_order[$d_drug['id']] = $d_drug['display_order'];
}
?>
<div class="element-fields element-eyes row">
	<input type="hidden" name="dilation_treatments_valid" value="1" />
	<?php echo $form->hiddenField($element, 'eye_id', array('class' => 'sideField'))?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {
    ?> inactive<?php 
}?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="field-row">
				<?php echo $form->dropDownListNoPost('dilation_drug_right', $element->getUnselectedDilationDrugs('right'), '', array('class' => 'inline dilation_drug', 'empty' => '--- Please select ---', 'nowrapper' => true, 'display_order' => $dilation_drugs_order))?>
				<button class="small secondary clearDilation">
					Clear
				</button>
			</div>
			<table class="plain grid dilation_table"<?php if (!$element->right_treatments) {
    ?> style="display: none;"<?php 
}?>>
				<thead>
					<tr>
						<th>Time</th>
						<th>Drug</th>
						<th>Drops</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="plain" id="dilation_right">
					<?php foreach ($element->right_treatments as $treatment) {
    $this->renderPartial('form_Element_OphCiExamination_Dilation_Treatment', array(
                                'name_stub' => CHtml::modelName($element).'[right_treatments]',
                                'treatment' => $treatment,
                                'key' => $key,
                                'side' => $treatment->side,
                                'drug_name' => $treatment->drug->name,
                                'drug_id' => $treatment->drug_id,
                                'data_order' => $treatment->drug->display_order,
                            ));
    ++$key;
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
	<div class="element-eye left-eye column right side<?php if (!$element->hasLeft()) {
    ?> inactive<?php 
}?>" data-side="left">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="field-row">
				<?php echo $form->dropDownListNoPost('dilation_drug_left', $element->getUnselectedDilationDrugs('left'), '', array('class' => 'inline dilation_drug', 'empty' => '--- Please select ---', 'nowrapper' => true, 'display_order' => $dilation_drugs_order))?>
				<button class="small secondary clearDilation">
					Clear
				</button>
			</div>
			<table class="plain grid dilation_table"<?php if (!$element->left_treatments) {
    ?> style="display: none;"<?php 
}?>>
				<thead>
					<tr>
						<th>Time</th>
						<th>Drug</th>
						<th>Drops</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="plain" id="dilation_left">
					<?php foreach ($element->left_treatments as $treatment) {
    $this->renderPartial('form_Element_OphCiExamination_Dilation_Treatment', array(
                                'name_stub' => CHtml::modelName($element).'[left_treatments]',
                                'treatment' => $treatment,
                                'key' => $key,
                                'side' => $treatment->side,
                                'drug_name' => $treatment->drug->name,
                                'drug_id' => $treatment->drug_id,
                                'data_order' => $treatment->drug->display_order,
                            ));
    ++$key;
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
            'name_stub' => CHtml::modelName($element).'[{{side}}_treatments]',
            'key' => '{{key}}',
            'side' => '{{side}}',
            'drug_name' => '{{drug_name}}',
            'drug_id' => '{{drug_id}}',
            'treatment_time' => '{{treatment_time}}',
            'data_order' => '{{data_order}}',
    ))?>
</script>
