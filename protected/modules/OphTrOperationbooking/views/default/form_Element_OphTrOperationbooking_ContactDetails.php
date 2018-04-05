<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<fieldset class="element-fields">
    <div class="row field-row">
        <div class="large-4 column end">
            <label>Who will collect the patient after surgery?</label>
        </div>
    </div>
    <?php $form->textField($element, 'collector_name', array(), array(),  array_merge($form->layoutColumns, array('field' => 4)))?>
    <?php $form->textField($element, 'collector_contact_number', array(), array(),  array_merge($form->layoutColumns, array('field' => 2)))?>
    <div class="row field-row">
        <div class="large-4 column end">
            <label>Contact number of patient (for bookings questions)</label>
        </div>
    </div>
    <?php
        $element->patient_booking_contact_number = $this->patient->primary_phone;
        $form->textField($element, 'patient_booking_contact_number', array(), array(),  array_merge($form->layoutColumns, array('field' => 2)));?>
</fieldset>