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
<div class="whiteBox forClinicians" style="width: 70em;">
	<div class="data_row">
		<table class="subtleWhite">
			<thead>
				<tr>
					<th style="width: 400px;">Diagnosis</th>
					<th>Eye</th>
					<th>Principal</th>
					<th>Edit</th>
				</tr>
			</thead>
			<tbody id="OphCiExamination_diagnoses">
				<?php foreach ($element->getFormDiagnoses() as $i => $diagnosis) {?>
					<tr>
						<td><?php echo $diagnosis['disorder']->term?></td>
						<td>
							<?php foreach (Eye::model()->findAll(array('order'=>'display_order')) as $eye) {?>
								<span class="OphCiExamination_eye_radio"><input type="radio" name="<?php echo get_class($element)?>[eye_id_<?php echo $i?>]" value="<?php echo $eye->id?>" <?php if ($diagnosis['eye_id'] == $eye->id) {?>checked="checked" <?php }?>/> <?php echo $eye->name?></span>
							<?php }?>
						</td>
						<td><input type="radio" name="principal_diagnosis" value="<?php echo $diagnosis['disorder']->id?>" <?php if ($diagnosis['principal']) {?>checked="checked" <?php }?>/></td>
						<td><a href="#" class="small removeDiagnosis" rel="<?php echo $diagnosis['disorder']->id?>"><strong>Remove</strong></a></td>
					</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
</div>

<div id="selected_diagnoses">
	<?php foreach ($element->getFormDiagnoses() as $diagnosis) {?>
		<input type="hidden" name="selected_diagnoses[]" value="<?php echo $diagnosis['disorder']->id?>" />
	<?php }?>
</div>

<?php echo $form->radioButtons($element, 'eye_id', 'eye', ($this->episode && $this->episode->eye_id) ? $this->episode->eye_id : 2)?>

<?php $this->widget('application.widgets.DiagnosisSelection', array(
		'field' => 'disorder_id',
		'options' => $element->getCommonOphthalmicDisorders($this->selectedFirmId),
		'layout' => 'minimal',
		'code' => '130', // Ophthamology
		'callback' => 'OphCiExamination_AddDiagnosis',
))?>
