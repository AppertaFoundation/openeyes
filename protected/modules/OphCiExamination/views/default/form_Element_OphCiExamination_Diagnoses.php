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
<div class="element-fields">
	<input type="hidden" name="<?php echo CHtml::modelName($element);?>[force_validation]" />
	<?php echo $form->radioButtons(new \OEModule\OphCiExamination\models\OphCiExamination_Diagnosis, 'eye_id', 'Eye',
        ($this->episode && $this->episode->eye_id) ? $this->episode->eye_id : 2, false, false, false, false, array(),
        array('label' => 2, 'field' => 10)) ?>
	<?php
    $conditions = $element->getCommonOphthalmicDisorders($this->selectedFirmId);
    $this->widget('application.widgets.DiagnosisSelection', array(
        'field' => 'condition',
        'options' => $conditions,
        'code' => '130', // Ophthamology
        'callback' => 'OphCiExamination_AddDisorderOrFinding',
        'filterCallback' => 'OphCiExamination_GetCurrentConditions',
        'layout' => 'includefindings',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
        'label' => true,
    ))?>

	<table class="plain grid">
		<thead>
			<tr>
				<th>Diagnosis</th>
				<th>Eye</th>
				<th>Principal</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody class="js-diagnoses" id="OphCiExamination_diagnoses">
			<?php foreach ($element->diagnoses as $i => $diagnosis) {
    ?>
				<tr>
					<td>
						<input type="hidden" name="selected_diagnoses[]" value="<?php echo $diagnosis->disorder->id?>" />
						<?php echo $diagnosis->disorder->term?>
					</td>
					<td class="eye">
						<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $eye) {
    ?>
							<label class="inline">
								<input type="radio" name="<?php echo CHtml::modelName($element)?>[eye_id_<?php echo $i?>]" value="<?php echo $eye->id?>" <?php if ($diagnosis->eye_id == $eye->id) {
    ?>checked="checked" <?php 
}
    ?>/> <?php echo $eye->name?>
							</label>
						<?php 
}
    ?>
					</td>
					<td>
						<input type="radio" name="principal_diagnosis" value="<?php echo $diagnosis['disorder']->id?>" <?php if ($diagnosis->principal) {
    ?>checked="checked" <?php 
}
    ?>/>
					</td>
					<td>
						<a href="#" class="removeDiagnosis" rel="<?php echo $diagnosis->disorder->id?>">
							Remove
						</a>
					</td>
				</tr>
			<?php 
}?>
		</tbody>
	</table>
</div>
