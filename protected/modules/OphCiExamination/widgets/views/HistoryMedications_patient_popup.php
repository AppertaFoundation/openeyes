<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
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
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<script type="text/javascript" src="<?= $this->getJsPublishedPath('HistoryMedications.js') ?>"></script>
<?php $el_id =  CHtml::modelName($element) . '_popup'; ?>

<?php if ($element && ($current || $stopped)) { ?>
    <div id="<?= $el_id ?>">
        <div class="data-group">
            <div class="cols-2 column label">
                Medications
            </div>
            <div class="cols-10 column data">
                <i>Current:</i> <?php if ($stopped) {?><a href="#" class="kind-toggle show" data-kind="stopped"><i class="oe-i history small" aria-hidden="true"></i></a><?php } ?>
                <?php if (!$current) {?>No current medications.<?php } ?>
            </div>

            <table class="plain valign-top summary-data-table">
                <?php if ($current) { ?>
                    <?php foreach ($current as $entry) { ?>
                        <tr>
                            <td><strong><?= $entry->getMedicationDisplay() ?></strong>
                                <?php if ($entry->prescription_item) { ?>
                                    <a href="<?= $this->getPrescriptionLink($entry) ?>"><span class="js-has-tooltip fa oe-i eye small" data-tooltip-content="View prescription"></span></a>
                                <?php } ?>
                            </td>
                            <td><span class="laterality <?= $entry->getLateralityDisplay() ?>"><?= $entry->getLateralityDisplay() ?></span></td>
                          <td><span class="oe-date"><?= Helper::convertDate2HTML($entry->getDatesDisplay()) ?></span></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                <?php if ($stopped) { ?>
                    <tr class="stopped-kind" style="display: none;">
                        <td colspan="3" style="padding-left: 88px"><i>Stopped:</i> <a href="#" class="kind-toggle remove" data-kind="stopped"><i class="oe-i remove small" aria-hidden="true"></i></a></td>
                    </tr>
                    <?php foreach ($stopped as $entry) { ?>
                        <tr class="stopped-kind" style="display: none;">
                            <td><strong><?= $entry->getMedicationDisplay() ?></strong>
                                <?php if ($entry->prescription_item) { ?>
                                    <a href="<?= $this->getPrescriptionLink($entry) ?>"><span class="js-has-tooltip fa oe-i eye small" data-tooltip-content="View prescription"></span></a>
                                <?php } ?>
                            </td>
                            <td><span class="laterality <?= $entry->getLateralityDisplay() ?>"><?= $entry->getLateralityDisplay() ?></span></td>
                            <td><span class="oe-date"><?= Helper::convertDate2HTML($entry->getDatesDisplay()) ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            new OpenEyes.OphCiExamination.HistoryMedicationsViewController({
                element: $('#<?= $el_id ?>')
            });
        });
    </script>
<?php } ?>
