<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="element-fields ">
<table class="cols-full last-left">
  <colgroup>
    <col class="cols-6">
    <col class="cols-6">
  </colgroup>
  <tbody>
  <tr class="col-gap">
    <td>
      <fieldset id="OEModule_OphCoCvi_models_Element_OphCoCvi_ConsentSignature_is_patient" class="data-group">
        <div class="cols-2 column">
          <label>Signed By:</label>
        </div>
        <div class="cols-10 column end">
            <?php echo $form->radioButtons(
                $element,
                'is_patient',
                array(
                1 => 'Patient',
                0 => "Patient's Representative",
                ),
                $element->is_patient,
                false,
                false,
                false,
                false,
                array('nowrapper' => true)
            ); ?>
            <?php // echo $form->radioBoolean($element, 'is_patient', array('nowrapper' => true)) ?>
        </div>
      </fieldset>
        <?php echo $form->textField($element, 'representative_name', array('hide' => $element->is_patient), null, array('field' => 4)) ?>
    </td>
    <td>
        <?php echo $form->datePicker(
            $element,
            'signature_date',
            array('maxDate' => 'today'),
            array('style' => 'width: 110px;')
        ) ?>
    </td>
  </tr>
  </tbody>
</table>

</div>
