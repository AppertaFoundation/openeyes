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
	<fieldset class="element-fields">
		<?php echo $form->radioButtons($element, 'schedule_options_id', 'OphTrOperationbooking_ScheduleOperation_Options'); ?>
		<div class="row field-row">
			<legend class="large-2 column">
				<?php echo $element->getAttributeLabel('patient_unavailables'); ?>:
			</legend>
			<div class="large-10 column">
				<table class="blank">
					<thead>
					<tr>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Reason</th>
						<th><div class="hide-offscreen">Actions</div></th>
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
					</tbody>
					<tfoot>
					<tr>
						<td colspan="4"><button class="secondary small addUnavailable">Add</button></td>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</fieldset>
<?php
    $template_unavailable = new OphTrOperationbooking_ScheduleOperation_PatientUnavailable();
?>
<script id="intraocularpressure_reading_template" type="text/html">
	<?php
    $this->renderPartial('form_OphTrOperationbooking_ScheduleOperation_PatientUnavailable', array(
            'key' => '{{key}}',
            'unavailable' => $template_unavailable,
            'form' => $form,
            'element_name' => get_class($element),
            'dateFieldWidget' => 'TextField',
    ));
    ?>
</script>

