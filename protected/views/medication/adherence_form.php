<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 *
 * OpenEyes is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenEyes is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with OpenEyes in a file titled COPYING. If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 *
 * This code renders the patient medication adherence dialog which is
 * Ajax-loaded from the "Edit" link in the medication section of the
 * patient summary page. The link is only visible if the patient is on
 * medication, so don't forget to add some if you're looking for the
 * link!
 */
$form = $this->beginWidget('FormLayout', array(
    'layoutColumns' => array('label' => 3, 'field' => 9),
));

$adherence = $patient->adherence;
if ($adherence === null) {
    $adherence = new MedicationAdherence();
    $adherence->patient = $patient;
}

?>
<input type="hidden" name="patient_id" id="medication_id" value="<?= $patient->id ?>"/>
<fieldset class="data-group">
    <legend><strong>Adherence</strong></legend>
    <div class="data-group">
        <div class="<?= $form->columns('label') ?>"><label for="adherence">Adherence:</label></div>
        <div class="<?= $form->columns('field') ?>"><?=
            CHtml::activeDropDownList(
                $adherence, 'level',
                CHtml::listData(MedicationAdherenceLevel::model()->
                    findAll(array('order' => 'display_order')), 'id', 'name')
            )
                    ?></div>
    </div>
    <div class="data-group">
        <div class="<?= $form->columns('label') ?>"><label for="adherence">Comments:</label></div>
        <div class="<?= $form->columns('field') ?>">
            <?= CHtml::activeTextArea($adherence, 'comments') ?>
        </div>
    </div>
</fieldset>
<div class="buttons">
    <button type="button" class="medication_save secondary small">Save</button>
    <button type="button" class="medication_cancel warning small">Cancel</button>
</div>
<?php $this->endWidget(); ?>
