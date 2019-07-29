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
<form id="current_report" action="<?php echo Yii::app()->createUrl('OphTrIntravitrealinjection/report/downloadReport')?>" method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
    <input type="hidden" name="report-name" value="Injections" />
    <input type="hidden" name="date_from" value="<?php echo $report->date_from?>" />
    <input type="hidden" name="date_to" value="<?php echo $report->date_to?>" />
    <input type="hidden" name="given_by_id" value="<?php echo $report->given_by_id?>" />
    <input type="hidden" name="summary" value="<?php echo $report->summary?>" />
    <input type="hidden" name="pre_va" value="<?php echo $report->pre_va?>" />
    <input type="hidden" name="post_va" value="<?php echo $report->post_va?>" />
</form>
<table>
    <thead>
        <tr>
            <th><?php echo Patient::model()->getAttributeLabel('hos_num')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('first_name')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('last_name')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('gender')?></th>
            <th><?php echo Patient::model()->getAttributeLabel('dob')?></th>
            <th>Eye</th>
            <th>Drug</th>
            <th>Site</th>
            <th>First injection date</th>
            <th>Last injection date</th>
            <th>Injection no</th>
        </tr>
    <tbody>
        <?php if (empty($report->injections)) {?>
            <tr>
                <td colspan="6">
                    No patients were found with the selected search criteria.
                </td>
            </tr>
        <?php } else {?>
            <?php foreach ($report->injections as $ts => $injection) {?>
                <tr>
                    <td><?php echo $injection['patient_hosnum']?></td>
                    <td><?php echo $injection['patient_firstname']?></td>
                    <td><?php echo $injection['patient_surname']?></td>
                    <td><?php echo $injection['patient_gender']?></td>
                    <td><?php echo $injection['patient_dob']?></td>
                    <td><?php echo $injection['eye']?></td>
                    <td><?php echo $injection['drug']?></td>
                    <td><?php echo $injection['site']?></td>
                    <td><?php echo $injection['first_injection_date']?></td>
                    <td><?php echo $injection['last_injection_date']?></td>
                    <td><?php echo $injection['injection_number']?></td>
                </tr>
            <?php }?>
        <?php }?>
    </tbody>
</table>
<div>
    <button type="submit" class="classy blue mini" id="download-report" name="run"><span class="button-span button-span-blue">Download report</span></button>
</div>
