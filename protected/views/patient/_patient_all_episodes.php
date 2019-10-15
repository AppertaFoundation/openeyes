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

<section class="element view full patient-info episodes">
    <header class="element-header">
        <h3 class="element-title">All <?= Episode::getEpisodeLabelPlural() ?></h3>
    </header>
    <div class="box-info" style="position: relative; float: right; ">
        <strong>open <?= $episodes_open?> &nbsp;|&nbsp;closed <?= $episodes_closed?></strong>
    </div>
    <?php if (empty($episodes)) {?>
        <div class="summary">No events</div>
    <?php } else {?>
        <div class="element-data full-width">
            <table class="patient-episodes standard">
                <thead>
                <tr>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Firm</th>
                    <th>Subspecialty</th>
                    <th>Eye</th>
                    <th>Diagnosis</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($ordered_episodes as $specialty_episodes) { ?>
                    <tr class="speciality">
                        <td colspan="6"><?= $specialty_episodes['specialty'] ?></td>
                    </tr>
                    <?php foreach ($specialty_episodes['episodes'] as $i => $episode) { ?>
                        <tr id="<?= $episode->id ?>"class="clickable all-episode <?php if ($episode->end_date !== null) {
                            ?> closed<?php
                                } ?>">
                            <td><?= $episode->NHSDate('start_date'); ?></td>
                            <td><?= $episode->NHSDate('end_date'); ?></td>
                            <td><?= $episode->firm ? CHtml::encode($episode->firm->name) : 'N/A'; ?></td>
                            <td><?= \CHtml::encode($episode->getSubspecialtyText()) ?></td>
                            <td><?= ($episode->diagnosis) ? $episode->eye->name : 'No diagnosis' ?></td>
                            <td><?= ($episode->diagnosis) ? $episode->diagnosis->term : 'No diagnosis' ?></td>
                        </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>