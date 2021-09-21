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
<div class="element-fields">
    <?php
    $subspecialty = Subspecialty::model()->find('ref_spec=:ref_spec', array(':ref_spec' => 'MR'));
    echo $form->dropDownList(
        $element,
        'consultant_id',
        Firm::model()->getList(Yii::app()->session['selected_institution_id'], $subspecialty->id, $element->consultant_id),
        array('empty' => 'Select'),
        false,
        array('field' => 3)
    );
    echo $form->dropDownList(
        $element,
        'site_id',
        Site::model()->getListForCurrentInstitution(),
        array('empty' => 'Select'),
        false,
        array('field' => 3)
    );
    ?>
  <fieldset id="Element_OphCoTherapyapplication_MrServiceInformation_patient_sharedata_consent" class="data-group">
    <legend class="cols-2 column">
      Patient consents to share data:
    </legend>
    <input type="hidden" value=""
           name="Element_OphCoTherapyapplication_MrServiceInformation[patient_sharedata_consent]">
    <div class="cols-10 column end">
      <label class="inline highlight">
            <?php echo $form->radioButton($element, 'patient_sharedata_consent'); ?> Yes
    </div>
  </fieldset>
</div>
