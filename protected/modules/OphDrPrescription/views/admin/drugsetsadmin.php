<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2015
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2015, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<?php
$element = Element_OphDrPrescription_Details::model();
$form = $this->beginWidget('BaseEventTypeCActiveForm', array(
    'id' => 'prescription-create',
    'action' => '/OphDrPrescription/PrescriptionCommon/SaveDrugSetAdmin',
    'enableAjaxValidation' => false,
));

?>

<div class="box admin">
	<h2>Edit Drug Sets</h2>
	<?php if (Yii::app()->user->hasFlash('info.save_message')) { ?>
		<div class="alert-box with-icon warning">
			<?php echo Yii::app()->user->getFlash('info.save_message'); ?>
		</div>
	<?php } ?>
	<div class="row field-row">
		<div class="large-4 column"><h3>Select a set:</h3></div>
	</div>
	<div class="row field-row">
		<div class="large-2 column"><label for="set_name">Saved sets:</label></div>
		<div class="large-4 column">
			<table class="grid">
				<tr>
					<th>Name</th>
					<th>Subspecialty</th>
					<th>Active</th>
					<th>Action</th>
				</tr>
			<?php
            $currentDrugSets = $element->drugSetsAll();
            foreach ($currentDrugSets as $drugSet) {
                echo '<tr></tr>';
            }
            echo CHtml::dropDownList('drug_set_id', null, CHtml::listData($element->drugSetsAll(), 'id', 'name'),
                array('empty' => '-- Select this to add new --')); ?>
			</table>
		</div>
		<div class="large-6 column end"></div>
	</div>

	<div class="row field-row">
		<div class="large-4 column"><h3>OR Add new:</h3></div>
	</div>
	<div class="row field-row">
		<div class="large-2 column"><label for="set_name">Set name:</label></div>
		<div class="large-4 column">
			<?php echo CHtml::textField('set_name'); ?>
		</div>
		<div class="large-2 column"><label for="site_id">Subspeciality:</label></div>
		<div class="large-4 column end">
			<?php
            // $selectedsubspecialty
            echo CHtml::dropDownList('subspecialty_id', '',
                CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'), array('empty' => '-- Select --'));
            ?>
		</div>
	</div>
	<section class="element" id="drugsetdata">
		<?php

        $this->renderPartial('/default/form_Element_OphDrPrescription_Details',
            array('form' => $form, 'element' => $element));

        //$this->displayErrors($errors, true);
        ?>
		<div class="box-header">
			<div class="box-actions">
				<button class="small" type="submit" id="save_set_data" name="save_set_data">
					Save this set
				</button>
			</div>
		</div>
	</section>
	<?php $this->endWidget(); ?>
</div>
