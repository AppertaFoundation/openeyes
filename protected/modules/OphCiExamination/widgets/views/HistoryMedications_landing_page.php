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

use OEModule\OphCiExamination\models\HistoryMedications;

/**
 * @var HistoryMedications $element
 * @var EventMedicationUse[] $current
 * @var EventMedicationUse[] $stopped
 */

$model_name = CHtml::modelName($element);

$eye_filter = function ($e) {
    /** @var EventMedicationUse $e */
    return !is_null($e->route_id) && $e->route->has_laterality;
};

$systemic_filter = function ($entry) use ($eye_filter) {
    return !$eye_filter($entry);
};

$stopped_filter = function ($e) {
    /** @var EventMedicationUse $e */
    return !$e->isChangedMedication();
};

$current_filter = function ($e) {
    /** @var EventMedicationUse $e */
    return !$e->isStopped();
};

$current = $element->mergeMedicationEntries($current);
$current = array_filter($current, $current_filter);
$current = $this->sortEntriesByDate($current);
$stopped = array_filter($stopped, $stopped_filter);
$stopped = $this->sortEntriesByDate($stopped, false);
$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);
?>

<?php if (!$current_eye_meds && !$stopped_eye_meds) { ?>
    <div class="nil-recorded">Nil recorded</div>
<?php } else { ?>
    <?php if ($current_eye_meds) { ?>
        <?= $this->render('_history_eye_medication_table', [
            'id' => $model_name . '_current_entry_table',
            'entries' => $current_eye_meds,
            'show_link' => true,
            'current' => true,
            'pro_theme' => ''
        ]); ?>
    <?php } else { ?>
        <div class="data-value none">
            No current Eye Medications
        </div>
    <?php } ?>
    <?php if ($stopped_eye_meds) { ?>
        <div class="collapse-data">
            <div class="collapse-data-header-icon expand">
                Stopped Medications
                <small>(<?= sizeof($stopped_eye_meds) ?>)</small>
            </div>
            <div class="collapse-data-content">
                <div class="restrict-data-shown">
                    <div class="restrict-data-content rows-10">
                        <?= $this->render('_history_eye_medication_table', [
                            'id' => $model_name . '_stopped_entry_table',
                            'entries' => $stopped_eye_meds,
                            'show_link' => false,
                            'current' => false,
                            'pro_theme' => ''
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>