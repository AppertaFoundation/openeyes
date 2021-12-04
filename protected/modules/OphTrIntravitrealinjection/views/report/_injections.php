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
<form id="current_report" action="<?= Yii::app()->createUrl('OphTrIntravitrealinjection/report/downloadReport')?>" method="post">
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken?>" />
    <input type="hidden" name="report-name" value="Injections" />
    <input type="hidden" name="date_from" value="<?= $report->date_from?>" />
    <input type="hidden" name="date_to" value="<?= $report->date_to?>" />
    <input type="hidden" name="given_by_id" value="<?= $report->given_by_id?>" />
    <input type="hidden" name="summary" value="<?= $report->summary?>" />
    <input type="hidden" name="pre_va" value="<?= $report->pre_va?>" />
    <input type="hidden" name="post_va" value="<?= $report->post_va?>" />
</form>
<table class="standard">
    <thead>
        <tr>
            <th><?= $report->getPatientIdentifierPrompt() ?></th>
            <th><?= Patient::model()->getAttributeLabel('first_name')?></th>
            <th><?= Patient::model()->getAttributeLabel('last_name')?></th>
            <th><?= Patient::model()->getAttributeLabel('gender')?></th>
            <th><?= Patient::model()->getAttributeLabel('dob')?></th>
            <th>Eye</th>
            <th>Drug</th>
            <th>Site</th>
            <th>First injection date</th>
            <th>Last injection date</th>
            <th>Injection no</th>
            <th><?= $report->getAttributeLabel('all_ids') ?></th>
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
                    <td><?= $injection['patient_identifier']?></td>
                    <td><?= $injection['patient_firstname']?></td>
                    <td><?= $injection['patient_surname']?></td>
                    <td><?= $injection['patient_gender']?></td>
                    <td><?= $injection['patient_dob']?></td>
                    <td><?= ucfirst($injection['eye'])?></td>
                    <td><?= $injection['drug']?></td>
                    <td><?= $injection['site']?></td>
                    <td><?= $injection['first_injection_date']?></td>
                    <td><?= $injection['last_injection_date']?></td>
                    <td><?= $injection['injection_number']?></td>
                    <td><?= $injection['all_ids'] ?></td>
                </tr>
            <?php }?>
        <?php }?>
    </tbody>
</table>
