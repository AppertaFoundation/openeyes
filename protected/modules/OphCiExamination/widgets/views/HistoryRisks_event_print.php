<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2018
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
<?php use OEModule\OphCiExamination\models\HistoryRisksEntry; ?>
<div class="cols-12">
    <?php if ($this->patient->no_risks_date) { ?>
        <p class="data-value flex-layout flex-top">Patient has no known risks.</p>
    <?php } else { ?>
        <?php
            $history_risks_entries = $element->getHistoryRisksEntries();
            $history_risks_entry_keys = $element->getHistoryRisksEntryKeys();
            $not_checked_required_risks = $this->getNotCheckedRequiredRisks($element);
        ?>
        <div class="data-value flex-layout flex-top">
            <div class="cols-12">
                <table class="last-left">
                    <colgroup>
                        <col class="cols-4">
                        <col class="cols-4">
                        <col class="cols-4">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>Present</th>
                            <th>Not present</th>
                            <th>Not checked</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($history_risks_entry_keys as $attribute) { ?>
                                <td>
                                    <span class="large-text">
                                        <?php if (count($history_risks_entries[$attribute]) > 0) { ?>
                                                <?php foreach ($history_risks_entries[$attribute] as $entry) { ?>
                                                    <?= $entry->getDisplayRisk() ?>
                                                    <?php if ($entry['comments'] != '') { ?>
                                                        (Comments: <?= $entry['comments'] ?> )
                                                    <?php } ?>
                                                    <br>
                                                <?php } ?>
                                        <?php } elseif ($attribute === $history_risks_entry_keys[HistoryRisksEntry::$NOT_CHECKED] && count($not_checked_required_risks) > 0) { ?>
                                                <?php foreach ($not_checked_required_risks as $entry) { ?>
                                                    <?= $entry ?>
                                                    <br>
                                                <?php } ?>
                                        <?php } else { ?>
                                            None
                                        <?php } ?>
                                    </span>
                                </td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>
</div>

