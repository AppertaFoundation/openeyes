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
$key = 0;
$method_values = array();
foreach (OEModule\OphCiExamination\models\OphCiExamination_ColourVision_Method::model()->findAll() as $method) {
    $method_values[] = "'".$method->id."' : ".json_encode(CHtml::listData($method->values, 'id', 'name'));
}

?>
<div class="element-fields element-eyes row">
	<script type="text/javascript">
		var colourVisionMethodValues = {
			<?php  echo implode(',', $method_values); ?>
		};
	</script>
	<?php echo $form->hiddenField($element, 'eye_id', array('class' => 'sideField'))?>
	<div class="element-eye right-eye column left side<?php if (!$element->hasRight()) {
    ?> inactive<?php 
}?>" data-side="right">
		<div class="active-form">
			<a href="#" class="icon-remove-side remove-side">Remove side</a>
			<div class="field-row">
				<?php echo $form->dropDownListNoPost('colourvision_method_right', CHtml::listData($element->getUnusedReadingMethods('right'), 'id', 'name'), '', array('class' => 'inline colourvision_method', 'empty' => '--- Please select ---', 'nowrapper' => true))?>
				<button class="small secondary clearCV<?php if (!$element->right_readings) {
    echo ' hidden';
}?>">
					Clear
				</button>
			</div>
			<table class="plain grid colourvision_table"<?php if (!$element->right_readings) {
    ?> style="display: none;"<?php 
}?>>
				<thead>
					<tr>
						<th>Method</th>
						<th>Value</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="plain" id="colourvision_right">
					<?php foreach ($element->right_readings as $reading) {
    $this->renderPartial('form_OphCiExamination_ColourVision_Reading', array(
                                'name_stub' => CHtml::modelName($element).'[right_readings]',
                                'reading' => $reading,
                                'key' => $key,
                                'side' => 'right',
                                'method_name' => $reading->method->name,
                                '',
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
				<?php echo $form->dropDownListNoPost('colourvision_method_left', CHtml::listData($element->getUnusedReadingMethods('left'), 'id', 'name'), '', array('class' => 'inline colourvision_method', 'empty' => '--- Please select ---', 'nowrapper' => true))?>
				<button class="small secondary clearCV<?php if (!$element->left_readings) {
    echo ' hidden';
}?>">
					Clear
				</button>
			</div>
			<table class="plain grid colourvision_table"<?php if (!$element->left_readings) {
    ?> style="display: none;"<?php 
}?>>
				<thead>
					<tr>
						<th>Method</th>
						<th>Value</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody class="plain" id="colourvision_left">
					<?php foreach ($element->left_readings as $reading) {
    $this->renderPartial('form_OphCiExamination_ColourVision_Reading', array(
                                'name_stub' => CHtml::modelName($element).'[left_readings]',
                                'reading' => $reading,
                                'key' => $key,
                                'side' => 'left',
                                'method_name' => $reading->method->name,
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
<script id="colourvision_reading_template" type="text/html">
	<?php
    $this->renderPartial('form_OphCiExamination_ColourVision_Reading', array(
            'name_stub' => CHtml::modelName($element).'[{{side}}_readings]',
            'key' => '{{key}}',
            'side' => '{{side}}',
            'method_name' => '{{method_name}}',
            'method_id' => '{{method_id}}',
            'method_values' => '{{& method_values}}',
    ))?>
</script>
