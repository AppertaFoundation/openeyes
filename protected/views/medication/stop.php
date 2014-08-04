<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$form = $this->beginWidget('FormLayout', array('layoutColumns' => array('label' => 3, 'field' => 9)));

?>
<fieldset class="field-row">
	<legend><strong>Stop Medication</strong></legend>
	<input type="hidden" name="patient_id" value="<?= $this->patient->id ?>">
	<input type="hidden" name="medication_id">
	<input type="hidden" name="end_date">
	<div class="row field-row">
		<div class="<?= $form->columns('label') ?>"><label>Medication:</label></div>
		<div class="<?= $form->columns('field') ?> data-value drug_name"></div>
	</div>
	<?php

	$this->renderPartial('/patient/_fuzzy_date', array('form' => $form, 'date' => date('Y-m-d'), 'class' => 'medication_end_date', 'label' => 'Date stopped'));
	$this->renderPartial('/medication/stop_reason', array('form' => $form, 'medication' => new Medication));

	?>
	<div class="buttons">
		<button type="button" class="medication_save secondary small">Stop</button>
		<button type="button" class="medication_cancel warning small">Cancel</button>
	</div>
</fieldset>
<?php

$this->endWidget();
