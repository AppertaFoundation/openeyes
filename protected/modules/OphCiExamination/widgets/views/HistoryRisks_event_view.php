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
<!--
  *******  Element Data Type (VIEW): * Risks *
  *******  CSS: "element-data view-risks" (+ any extra css)
  *******  CSS hook used for element specific styling only where required
  *******  Only minimum required DOM and CSS for UI is shown here
  -->
<div class="element-data full-width">
    <div class="data-group">
        <?php if (!$this->patient->hasRiskStatus()) { ?>
            <div class="data-value flex-layout flex-top">Patient has no known risks.</div>
        <?php } else { ?>
            <div class="flex-layout flex-top">
                <div class="cols-11">
                    <div id="js-listview-risks-pro" class="cols-full listview-pro">
                        <!--                Anticoagulants and alpha blockers being mandatory risk items to be displayed, we check if $element contains these in either yes, or no and if it doesn't in either, we display it as unchecked forcefully-->
                        <?php
                        $anticoagulants = false;
                        $alphablockers = false;
                        $entries = array_merge($element->getEntriesDisplay('present'), $element->getEntriesDisplay('not_present'));

                        foreach ($entries as $entry) {
                            if (strpos($entry['risk'] . ' ', 'Anticoagulants') !== false) {
                                $anticoagulants = true;
                            }

                            if (strpos($entry['risk'], 'Alpha blockers') !== false) {
                                $alphablockers = true;
                            }
                        }

                        if ($anticoagulants == false) {
                            $not_checked_required_risks[] = 'Anticoagulants';
                        }
                        if ($alphablockers == false) {
                            $not_checked_required_risks[] = 'Alpha blockers';
                        } ?>
                        <table class="last-left">
                            <tbody>
                            <tr>
                                <td class="nowrap fade">Present</td>
                                <td>
                                    <?php if ($element->present) { ?>
                                        <ul class="dot-list">
                                            <?php foreach ($element->getEntriesDisplay('present') as $entry) { ?>
                                                <li>
                                                <li><?= $entry['risk'] ?></li>
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
                                    <?php if ($element->not_checked) { ?>
                                        <ul class="dot-list">
                                            <?php foreach ($element->getEntriesDisplay('not_checked') as $entry) { ?>
                                                <li>
                                                <li><?= $entry['risk'] ?></li>
                                                <?php if ($entry['comments'] != '') { ?>
                                                    <i class="oe-i comments-added small pad js-has-tooltip"
                                                       data-tooltip-content="<?= $entry['comments'] ?>"
                                                       pro"="" list="" mode"=""></i>
                                                <?php } ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    <?php } else if (isset($not_checked_required_risks)) { ?>
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
                                    <?php if ($element->not_present) { ?>
                                        <ul class="dot-list">
                                            <?php foreach ($element->getEntriesDisplay('not_present') as $entry) { ?>
                                                <li>
                                                <li><?= $entry['risk'] ?></li>
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
                                <?php if (count($element->present) > 0) { ?>
                                    <?php foreach ($element->getEntriesDisplay('present') as $entry) : ?>
                                        <tr>
                                            <td><?= $entry['risk']; ?></td>
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
                                <?php if (count($element->not_checked) > 0) { ?>
                                    <?php foreach ($element->getEntriesDisplay('not_checked') as $entry) : ?>
                                        <tr>
                                            <td><?= $entry['risk']; ?></td>
                                            <td>
                                                <span class="none"><?= ($entry['comments'] !== '' ? $entry['comments'] : 'None') ?></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php } else if (isset($not_checked_required_risks) && is_array($not_checked_required_risks)) { ?>
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
                                <?php if (count($element->not_present) > 0) { ?>
                                    <?php foreach ($element->getEntriesDisplay('not_present') as $entry) : ?>
                                        <tr>
                                            <td><?= $entry['risk']; ?></td>
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

