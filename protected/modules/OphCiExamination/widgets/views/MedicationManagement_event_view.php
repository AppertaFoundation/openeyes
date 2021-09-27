<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\MedicationManagement;

?>
<?php /** @var MedicationManagement $element */ ?>
<?php $el_id = CHtml::modelName($element) . '_element';
$form_format = SettingMetadata::model()->getSetting('prescription_form_format');
?>
<div class="element-data full-width">
    <?php $sections = array(
        'New' => ["getEntriesStartedToday", "getEntriesStartingInFuture"],
        'Continued' => ["getContinuedEntries"],
        'Changed' => ["getChangedEntries"],
        'Discontinued' => ["getStoppedEntries"]
    );

   $header_rendered = false; // we only render the header in the first section

foreach ($sections as $section => $methods) :
    $entries = array();
    foreach ($methods as $method) {
        $entries += $element->$method();
    }

    if ($entries) {
        ?>
            <div class="collapse-data">
                <div class="collapse-data-header-icon collapse">
                    <i class="oe-i-e i-DrPrescription pad-right"></i><?= $section ?> Medications
                    <small>(<?= count($entries) ?>)</small>
                </div>
                <div class="collapse-data-content" style="display:block;">
                    <table class="medications" id="Medication_Management_medication_<?= $section ?>_entries">
                    <?php if (!$header_rendered) :
                        $header_rendered = true;
                        ?>
                        <thead>
                    <?php else : ?>
                        <thead style="display:none;" >
                    <?php endif; ?>
                        <tr>
                            <th>Drug</th>
                            <th>Dose/frequency/route</th>

                            <th>Side &emsp;&nbsp; Start / Stop dates</th>
                            <th>Duration/dispense/comments</th>
                            <th><i class="oe-i drug-rx small js-has-tooltip" data-tt-type="basic" data-tooltip-content="Prescribe Medication"></i></th>
                            <th>
                                <!-- icons -->
                            </th>
                        </tr>
                    </thead>

                        <tbody>
                        <?php
                        if (!empty($entries)) :
                            foreach ($entries as $key => $entry) : ?>
                                    <?php echo $this->render(
                                        'MedicationManagementEntry_event_view',
                                        [
                                        'entry' => $entry,
                                        'patient' => $this->patient,
                                        'entry_icon' => null,
                                        'row_count' => $key,
                                        'form_setting' => $form_format
                                        ]
                                    ); ?>
                            <?php endforeach; ?>

                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    <?php } ?>
<?php endforeach; ?>
    <div class="collapse-data">
        <?php
        if ($this->mode === $this::$EVENT_VIEW_MODE) {
            $row = 0;
            if (!empty($signatures = $element->getSignatures(true))) {
                foreach ($signatures as $signature) {
                    $this->widget(
                        $this->getWidgetClassByType($signature->type),
                        [
                            "row_id" => $row++,
                            "element" => $element,
                            "signature" => $signature,
                        ]
                    );
                }
            }
        }

        if ($this->mode === $this::$EVENT_PRINT_MODE) {
            $signatures = $element->getSignatures();
            if (count($signatures) > 0) {
                foreach ($signatures as $signature) { ?>
                <div class="box">
                    <div class="flex">
                        <div class="dotted-area">
                            <div class="label">Signed</div>
                            <?= $signature->getPrintout(); ?>
                        </div>
                        <div class="dotted-area">
                            <div class="label">Date</div>
                            <?= $signature->getSignedTime(); ?>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="dotted-area">
                            <div class="label">Print name</div>
                            <?= $signature->signatory_name ?>
                        </div>
                        <div class="dotted-area">
                            <div class="label">Job title</div>
                            <?= $signature->signatory_role ?>
                        </div>
                    </div>
                </div>
                <?php }
            }
        }
        ?>
    </div>
</div>
<script type="text/javascript">
    sessionStorage.removeItem('mmesign_change');
</script>

