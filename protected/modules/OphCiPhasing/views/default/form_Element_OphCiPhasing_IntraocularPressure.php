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
$key = 0;
?>
<section class="element <?php echo $element->elementType->class_name ?>"
	data-element-type-id="<?php echo $element->elementType->id ?>"
	data-element-type-class="<?php echo $element->elementType->class_name ?>"
	data-element-type-name="<?php echo $element->elementType->name ?>"
	data-element-display-order="<?php echo $element->elementType->display_order ?>">
	<div class="element-fields element-eyes row">
		<input type="hidden" name="intraocularpressure_readings_valid" value="1" />
		<?php echo $form->hiddenInput($element, 'eye_id', false, array('class' => 'sideField')); ?>
		<div class="element-eye right-eye column side left<?php if (!$element->hasRight()) { ?> inactive<?php } ?>" data-side="right">
			<div class="active-form">
				<a href="#" class="icon-remove-side remove-side">Remove side</a>
				<?php echo $form->dropDownList($element, 'right_instrument_id', 'OphCiPhasing_Instrument', array(), false, array('label' => 2, 'field' => 4))?>
				<?php echo $form->radioBoolean($element, 'right_dilated', array(), array('label' => 2, 'field' => 10))?>
				<fieldset class="row field-row">
					<legend class="large-2 column">
						Readings:
					</legend>
					<div class="large-10 column">
						<table class="blank">
							<thead>
								<tr>
									<th>Time (HH:MM)</th>
									<th>mm Hg</th>
									<th><div class="hide-offscreen">Actions</div></th>
								</tr>
							</thead>
							<tbody class="readings-right">
								<?php
                                    if ($element->right_readings) {
                                        foreach ($element->right_readings as $index => $reading) {
                                            $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                                                'key' => $key,
                                                'reading' => $reading,
                                                'side' => $reading->side,
                                                'no_remove' => ($index == 0),
                                            ));
                                            ++$key;
                                        }
                                    } else {
                                        $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                                            'key' => $key,
                                            'side' => 0,
                                            'no_remove' => true,
                                        ));
                                        ++$key;
                                    }
                                ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3"><button class="secondary small addReading">Add</button></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?php echo $form->textArea($element, 'right_comments', array(), false, array('class' => 'autosize', 'placeholder' => 'Enter comments ...'), array('label' => 2, 'field' => 10))?>
			</div>
			<div class="inactive-form">
				<div class="add-side">
					<a href="#">
						Add right side <span class="icon-add-side"></span>
					</a>
				</div>
			</div>
		</div>
		<div class="element-eye left-eye column side right<?php if (!$element->hasLeft()) { ?> inactive<?php } ?>" data-side="left">
			<div class="active-form">
				<a href="#" class="icon-remove-side remove-side">Remove side</a>
				<?php echo $form->dropDownList($element, 'left_instrument_id', 'OphCiPhasing_Instrument', array(), false, array('label' => 2, 'field' => 4))?>
				<?php echo $form->radioBoolean($element, 'left_dilated', array(), array('label' => 2, 'field' => 10))?>
				<fieldset class="row field-row">
					<legend class="large-2 column">
						Readings:
					</legend>
					<div class="large-10 column">
						<table class="blank">
							<thead>
								<tr>
									<th>Time (HH:MM)</th>
									<th>mm Hg</th>
									<th><div class="hide-offscreen">Actions</div></th>
								</tr>
							</thead>
							<tbody class="readings-left">
								<?php

                                    if ($element->left_readings) {
                                        foreach ($element->left_readings as $index => $reading) {
                                            $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                                                'key' => $key,
                                                'reading' => $reading,
                                                'side' => $reading->side,
                                                'no_remove' => ($index == 0),
                                            ));
                                            ++$key;
                                        }
                                    } else {
                                        $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
                                            'key' => $key,
                                            'side' => 1,
                                            'no_remove' => true,
                                        ));
                                        ++$key;
                                    }
                                ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3"><button class="secondary small addReading">Add</button></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</fieldset>
				<?php echo $form->textArea($element, 'left_comments', array(), false, array('class' => 'autosize', 'placeholder' => 'Enter comments ...'), array('label' => 2, 'field' => 10))?>
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
</section>
<script id="intraocularpressure_reading_template" type="text/html">
	<?php
    $this->renderPartial('form_Element_OphCiPhasing_IntraocularPressure_Reading', array(
            'key' => '{{key}}',
            'side' => '{{side}}',
    ));
    ?>
</script>
