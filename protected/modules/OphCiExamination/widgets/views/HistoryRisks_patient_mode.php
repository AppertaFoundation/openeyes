<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version. OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details. You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled
 * COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$model_name = CHtml::modelName($element);
?>

<?php if (!$element) { ?>
    <p class="risk-status-unknown">Patient risk status is unknown</p>
<?php } else { ?>
    <p class="risk-status-none" <?php if (!$element->no_risks_date) { echo 'style="display: none;"'; }?>>Patient has no known risks</p>


    <table class="plain patient-data" id="<?= $model_name ?>_entry_table" <?php if (empty($element->entries)) { echo 'style="display: none;"'; }?>>
        <thead>
        <tr>
            <th>Risk</th>
            <th>Status</th>
            <th>Comments</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($element->entries as $entry) {
            $this->render(
                'HistoryRisksEntry_patient_mode',
                array(
                    'entry' => $entry,
                    'model_name' => $model_name
                )
            );
        }
        ?>
        </tbody>
    </table>
<?php } ?>