<?php /**
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
<div class="element-fields full-width flex-layout">
    <div class="cols-11">
        <table class="cols-6 last-left">
            <tbody>
            <tr>
                <td>
                    Schedule options
                </td>
                <td>
                    <?php echo $form->radioButtons(
                        $element,
                        'schedule_options_id',
                        'OphTrOperationbooking_ScheduleOperation_Options',
                        null,
                        false,
                        false,
                        false,
                        false,
                        array(
                            'nowrapper' => true,
                            'data-test' => 'op-schedule-options'
                        )
                    ); ?>
                </td>
            </tr>
            </tbody>
        </table>
        <hr>
        <?php echo $element->getAttributeLabel('patient_unavailables'); ?>:
        <table class="cols-11">
            <thead>
            <tr>
                <th><i class="oe-i start small pad"></i>Start</th>
                <th><i class="oe-i stop small pad"></i>End</th>
                <th>Reason</th>
                <th></th>
            </tr>

            </thead>
            <tbody class="unavailables">
                <?php
                if ($element->patient_unavailables) {
                    foreach ($element->patient_unavailables as $key => $unavailable) {
                        $this->renderPartial('form_OphTrOperationbooking_ScheduleOperation_PatientUnavailable', array(
                            'key' => $key,
                            'unavailable' => $unavailable,
                            'form' => $form,
                            'element_name' => get_class($element),
                        ));
                        ++$key;
                    }
                }
                ?>
        </table>
    </div>
    <div class="add-data-actions flex-item-bottom">
        <button class="button hint green addUnavailable"><i class="oe-i plus pro-theme "></i></button>
    </div>
</div>
<script id="intraocularpressure_reading_template" type="text/html">
    <?php
    $template_unavailable = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();

    $this->renderPartial('form_OphTrOperationbooking_ScheduleOperation_PatientUnavailable', array(
        'key' => '{{key}}',
        'unavailable' => $template_unavailable,
        'form' => $form,
        'element_name' => get_class($element),
        'dateFieldWidget' => 'TextField'
    ));
    ?>
</script>

