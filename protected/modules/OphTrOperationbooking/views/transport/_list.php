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
<div id="no_gp_warning" class="alert-box alert with-icon hide">
    One or more patients has no <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> practice, please correct in PAS before printing <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> letter.
</div>
<div id="transportList">
    <table class="standard transport">
        <thead>
            <tr>
                <th>Hospital number</th>
                <th>Patient</th>
                <th>TCI date</th>
                <th>Admission time</th>
                <th>Site</th>
                <th>Ward</th>
                <th>Method</th>
                <th>Firm</th>
                <th>Subspecialty</th>
                <th>DTA</th>
                <th>Priority</th>
                <th><input type="checkbox" id="transport_checkall" value="" /></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($operations)) {?>
                <tr>
                    <td colspan="12">
                        No items matched your search criteria.
                    </td>
                </tr>
            <?php } else {?>
                <?php foreach ($operations as $operation) {?>
                    <tr class="status <?php echo $operation->transportColour?>">
                        <td><?php echo $operation->event->episode->patient->hos_num?></td>
                        <td class="patient">
                            <?=\CHtml::link('<strong>'.trim(strtoupper($operation->event->episode->patient->last_name)).'</strong>, '.$operation->event->episode->patient->first_name, Yii::app()->createUrl('OphTrOperationbooking/default/view/'.$operation->event_id))?>
                        </td>
                        <td><?php echo date('j-M-Y', strtotime($operation->latestBooking->session_date))?></td>
                        <td><?php echo $operation->latestBooking->session_start_time?></td>
                        <td><?php echo $operation->latestBooking->theatre->site->shortName?></td>
                        <td><?php echo $operation->latestBooking->ward ? $operation->latestBooking->ward->name : 'None'?></td>
                        <td><?php echo $operation->transportStatus?></td>
                        <td><?php echo $operation->event->episode->firm ? $operation->event->episode->firm->pas_code : 'Support service'?></td>
                        <td><?php echo $operation->event->episode->firm ? $operation->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->ref_spec : ''?></td>
                        <td><?php echo $operation->NHSDate('decision_date')?></td>
                        <td><?php echo $operation->priority->name?></td>
                        <td><input type="checkbox" name="operations[]" value="<?php echo $operation->id?>" /></td>
                    </tr>
                <?php }?>
            <?php }?>
        </tbody>
        <tfoot class="pagination-container">
            <tr>
                <td colspan="12">
                    <?php echo $this->renderPartial('_pagination')?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>
