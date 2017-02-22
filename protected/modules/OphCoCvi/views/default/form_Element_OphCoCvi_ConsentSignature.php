<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<div class="element-fields row">
    <?php echo $form->datePicker($element, 'signature_date', array('maxDate' => 'today'),
        array('style' => 'width: 110px;')) ?>
    <fieldset id="OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_is_patient" class="row field-row">
        <div class="large-2 column">
            <label>Signed By:</label>
        </div>
        <div class="large-10 column end">
            <?php echo $form->radioButtons($element, 'is_patient', array(
                1 => 'Patient',
                0 => "Patient's Representative",
            ),
                $element->is_patient,
                false, false, false, false,
                array('nowrapper' => true)
            ); ?>
            <?php // echo $form->radioBoolean($element, 'is_patient', array('nowrapper' => true)) ?>
        </div>
    </fieldset>
    <?php echo $form->textField($element, 'representative_name', array('hide' => $element->is_patient), null, array('field' => 4)) ?>

</div>
