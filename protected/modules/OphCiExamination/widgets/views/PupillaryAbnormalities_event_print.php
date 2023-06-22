<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php use OEModule\OphCiExamination\models\PupillaryAbnormalityEntry; ?>

<div class="element-data element-eyes">
    <?php foreach (['left' => 'right', 'right' => 'left'] as $page_side => $eye_side) : ?>
        <div class="js-element-eye <?= $eye_side ?>-eye column">
            <?php if ($element->{'no_pupillaryabnormalities_date_' . $eye_side}) : ?>
                <div class="data-value">
                <span class="large-text">
                    Patient has no <?= $eye_side ?> pupillary abnormalites (confirmed)
                </span>
                </div>
            <?php elseif ($element->{'entries_' . $eye_side}) :
                $entries = [];
                foreach ([(string)PupillaryAbnormalityEntry::$NOT_PRESENT, (string)PupillaryAbnormalityEntry::$PRESENT, (string)PupillaryAbnormalityEntry::$NOT_CHECKED] as $key) {
                    $entries[$key] = array_values(array_filter($element->getSortedEntries($eye_side), function ($e) use ($key) {
                        return $e->has_abnormality === $key;
                    }));
                }?>

                <table class="last-left">
                    <tbody>
                    <tr>
                        <td class="nowrap fade">Present</td>
                        <td>
                            <?php if (count($entries[(string)PupillaryAbnormalityEntry::$PRESENT]) > 0) { ?>
                                <ul class="dot-list">
                                    <?php foreach ($entries[(string)PupillaryAbnormalityEntry::$PRESENT] as $entry) : ?>
                                        <li>
                                            <?= $entry->getDisplayAbnormality(); ?>
                                            <?php if ($entry['comments'] !== "") { ?>
                                                <small class="fade">(<?= $entry['comments'] ?>)</small>
                                            <?php } ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php } else { ?>
                                <span class="none">None</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="nowrap fade">Unchecked</td>
                        <td>
                            <?php if (count($entries[(string)PupillaryAbnormalityEntry::$NOT_CHECKED]) > 0) { ?>
                                <ul class="dot-list">
                                    <?php foreach ($entries[(string)PupillaryAbnormalityEntry::$NOT_CHECKED] as $entry) : ?>
                                        <li>
                                            <?= $entry->getDisplayAbnormality(); ?>
                                            <?php if ($entry['comments'] !== "") { ?>
                                                <small class="fade">(<?= $entry['comments'] ?>)</small>
                                            <?php } ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php } else { ?>
                                <span class="none">None</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="nowrap fade">Absent</td>
                        <td>
                            <?php if (count($entries[(string)PupillaryAbnormalityEntry::$NOT_PRESENT]) > 0) { ?>
                                <ul class="dot-list">
                                    <?php foreach ($entries[(string)PupillaryAbnormalityEntry::$NOT_PRESENT] as $entry) : ?>
                                        <li>
                                            <?= $entry->getDisplayAbnormality(); ?>
                                            <?php if ($entry['comments'] !== "") { ?>
                                                <small class="fade">(<?= $entry['comments'] ?>)</small>
                                            <?php } ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php } else { ?>
                                <span class="none">None</span>
                            <?php } ?>
                        </td>
                    </tr>
                    </tbody>
                </table>

            <?php else : ?>
                <div class="data-value not-recorded">
                    <?= ucfirst($eye_side) ?> eye not recorded
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
