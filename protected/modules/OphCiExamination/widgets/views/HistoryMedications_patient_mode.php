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

use OEModule\OphCiExamination\models\HistoryMedicationsEntry;

/**
 * @var HistoryMedicationsEntry[] $current
 * @var HistoryMedicationsEntry[] $stopped
 */

$model_name = CHtml::modelName($element);

$systemic_filter = function ($med) {
    return $med->laterality === null;
};

$eye_filter = function ($e) {
    /** @var EventMedicationUse $e */
    return !is_null($e->route_id) && $e->route->has_laterality;
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
$current_systemic_meds = array_filter($current, $systemic_filter);
$stopped_systemic_meds = array_filter($stopped, $systemic_filter);
$current_eye_meds = array_filter($current, $eye_filter);
$stopped_eye_meds = array_filter($stopped, $eye_filter);

?>
<div class="group" name="group-systemic-medications">
    <div class="label">Systemic Medications</div>
    <div class="data">
        <?php if (!$current_systemic_meds && !$stopped_systemic_meds && !$element->no_systemic_medications_date) { ?>
            <div class="nil-recorded">Nil recorded</div>
        <?php } elseif (!$current_systemic_meds && !$stopped_systemic_meds && $element->no_systemic_medications_date) { ?>
            <div class="nil-recorded">Patient is not taking any systemic medications</div>
        <?php } else { ?>
            <?php if ($current_systemic_meds) { ?>
                <?= $this->render('_history_systemic_medication_table', [
                    'id' => $model_name . '_systemic_current_entry_table',
                    'entries' => $current_systemic_meds,
                    'show_link' => true,
                    'current' => true,
                    'pro_theme' => 'pro-theme'
                ]); ?>
            <?php } else { ?>
                <div class="data-value not-recorded">
                    No current Systemic Medications
                </div>
            <?php } ?>

            <?php if ($stopped_systemic_meds) { ?>
                <div class="collapse-data">
                    <div class="collapse-data-header-icon expand">
                        Stopped
                        <small>(<?= sizeof($stopped_systemic_meds) ?>)</small>
                    </div>
                    <div class="collapse-data-content">
                        <?= $this->render('_history_systemic_medication_table', [
                            'id' => $model_name . '_systemic_stopped_entry_table',
                            'entries' => $stopped_systemic_meds,
                            'show_link' => true,
                            'current' => false,
                            'pro_theme' => 'pro-theme'
                        ]); ?>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>
    </div>
</div>
</div><!-- popup-overflow -->

<!-- oe-popup-overflow handles scrolling if data overflow height -->
<div class="oe-popup-overflow quicklook-data-groups">
    <div class="group" name="group-eye-medications">
        <div class="label">Eye Medications</div>
        <div class="data">
            <?php if (!$current_eye_meds && !$stopped_eye_meds && !$element->no_ophthalmic_medications_date) { ?>
                <div class="nil-recorded">Nil recorded</div>
            <?php } elseif (!$current_eye_meds && !$stopped_eye_meds && $element->no_ophthalmic_medications_date) { ?>
                <div class="nil-recorded">Patient is not taking any eye medications</div>
            <?php } else { ?>
                <?php if ($current_eye_meds) { ?>
                    <?= $this->render('_history_eye_medication_table', [
                        'id' => $model_name . '_eye_current_entry_table',
                        'entries' => $current_eye_meds,
                        'show_link' => true,
                        'current' => true,
                        'pro_theme' => 'pro-theme'
                    ]); ?>
                <?php } ?>
                <?php if ($stopped_eye_meds) { ?>
                    <div class="collapse-data">
                        <div class="collapse-data-header-icon expand">
                            Stopped Medications
                            <small>(<?= sizeof($stopped_eye_meds) ?>)</small>
                        </div>
                        <div class="collapse-data-content">
                            <?= $this->render('_history_eye_medication_table', [
                                'id' => $model_name . '_eye_stopped_entry_table',
                                'entries' => $stopped_eye_meds,
                                'show_link' => false,
                                'current' => false,
                                'pro_theme' => 'pro-theme'
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>