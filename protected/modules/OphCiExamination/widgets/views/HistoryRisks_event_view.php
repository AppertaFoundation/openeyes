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
<?php use OEModule\OphCiExamination\models\HistoryRisksEntry; ?>
<!--
  *******  Element Data Type (VIEW): * Risks *
  *******  CSS: "element-data view-risks" (+ any extra css)
  *******  CSS hook used for element specific styling only where required
  *******  Only minimum required DOM and CSS for UI is shown here
  -->
<div class="element-data full-width">
    <div class="data-group">
        <?php if ($this->patient->no_risks_date) { ?>
            <div class="data-value flex-layout flex-top">Patient has no known risks.</div>
        <?php } else { ?>
            <div class="flex-layout flex-top">
                <div class="cols-11">
                    <div id="js-listview-risks-pro" class="cols-full listview-pro">
                        <?php
                        $history_risks_entries = $element->getHistoryRisksEntries();
                        $history_risks_entry_keys = $element->getHistoryRisksEntryKeys();
                        $not_checked_required_risks = $this->getNotCheckedRequiredRisks($element);
                        if (count($not_checked_required_risks) === 0) {
                            unset($not_checked_required_risks);
                        }
                        ?>
                        <table class="last-left">
                            <tbody>
                            <tr>
                                <td class="nowrap fade">Present</td>
                                <td>
                                    <?php if (count($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$PRESENT]]) > 0) { ?>
                                        <ul class="dot-list">
                                            <?php foreach ($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$PRESENT]] as $entry) { ?>
                                                <li>
                                                <li><?= $entry->getDisplayRisk(); ?></li>
                                                <?php if ($entry['comments'] != '') { ?>
                                                    <i class="oe-i comments-added small pad js-has-tooltip"
                                                       data-tooltip-content="<?= $entry['comments'] ?>"
                                                       pro"="" list="" mode"=""></i>
                                                <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } else { ?>
                                        <span class="none">None</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="nowrap fade">Unchecked</td>
                                <td>
                                    <?php if (count($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_CHECKED]]) > 0) { ?>
                                        <ul class="dot-list">
                                            <?php foreach ($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_CHECKED]] as $entry) { ?>
                                                <li>
                                                <li><?= $entry->getDisplayRisk(); ?></li>
                                                <?php if ($entry['comments'] != '') { ?>
                                                    <i class="oe-i comments-added small pad js-has-tooltip"
                                                       data-tooltip-content="<?= $entry['comments'] ?>"
                                                       pro"="" list="" mode"=""></i>
                                                <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } elseif (isset($not_checked_required_risks)) { ?>
                                        <ul class="dot-list">
                                            <?php foreach ($not_checked_required_risks as $entry) { ?>
                                                <li>
                                                <li><?= $entry ?></li>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } else { ?>
                                        <span class="none">None</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="nowrap fade">Absent</td>
                                <td>
                                    <?php if (count($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_PRESENT]]) > 0) { ?>
                                        <ul class="dot-list">
                                            <?php foreach ($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_PRESENT]] as $entry) { ?>
                                                <li>
                                                <li><?= $entry->getDisplayRisk(); ?></li>
                                                <?php if ($entry['comments'] != '') { ?>
                                                    <i class="oe-i comments-added small pad js-has-tooltip"
                                                       data-tooltip-content="<?= $entry['comments'] ?>"
                                                       pro"="" list="" mode"=""></i>
                                                <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } else { ?>
                                        <span class="none">None</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="js-listview-risks-full" class="cols-full listview-full" style="display: none;">

                        <div class="flex-layout">

                            <div class="cols-2">Present</div>

                            <table class="last-left">
                                <colgroup>
                                    <col class="cols-4">
                                </colgroup>
                                <tbody>
                                <?php if (count($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$PRESENT]]) > 0) { ?>
                                    <?php foreach ($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$PRESENT]] as $entry) : ?>
                                        <tr>
                                            <td><?= $entry->getDisplayRisk(); ?></td>
                                            <td>
                                                <span class="none"><?= ($entry['comments'] !== '' ? $entry['comments'] : 'None') ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php } else { ?>
                                    <tr>
                                        <td>None</td>
                                        <td><span class="none">None</span></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div><!-- .flex-layout -->

                        <hr class="divider">


                        <div class="flex-layout">

                            <div class="cols-2">Unchecked</div>

                            <table class="last-left">
                                <colgroup>
                                    <col class="cols-4">
                                </colgroup>
                                <tbody>
                                <?php if (count($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_CHECKED]]) > 0) { ?>
                                    <?php foreach ($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_CHECKED]] as $entry) : ?>
                                        <tr>
                                            <td><?= $entry->getDisplayRisk(); ?></td>
                                            <td>
                                                <span class="none"><?= ($entry['comments'] !== '' ? $entry['comments'] : 'None') ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php } elseif (isset($not_checked_required_risks) && is_array($not_checked_required_risks)) { ?>
                                    <?php foreach ($not_checked_required_risks as $entry) : ?>
                                        <tr>
                                            <td><?= $entry ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php } else { ?>
                                    <tr>
                                        <td>None</td>
                                        <td><span class="none">None</span></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div><!-- .flex-layout -->

                        <hr class="divider">

                        <div class="flex-layout">

                            <div class="cols-2">Absent</div>

                            <table class="last-left">
                                <colgroup>
                                    <col class="cols-4">
                                </colgroup>
                                <tbody>
                                <?php if (count($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_PRESENT]]) > 0) { ?>
                                    <?php foreach ($history_risks_entries[$history_risks_entry_keys[HistoryRisksEntry::$NOT_PRESENT]] as $entry) : ?>
                                        <tr>
                                            <td><?= $entry->getDisplayRisk(); ?></td>
                                            <td>
                                                <span class="none"><?= ($entry['comments'] !== '' ? $entry['comments'] : 'None') ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php } else { ?>
                                    <tr>
                                        <td>None</td>
                                        <td><span class="none">None</span></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div><!-- .flex-layout -->

                    </div>
                </div>
                <?php if (count($element->entries)) : ?>
                    <div>
                        <i class="oe-i small js-listview-expand-btn expand" data-list="risks"></i>
                    </div>
                <?php endif; ?>
            </div>
        <?php } ?>
    </div>
</div>

