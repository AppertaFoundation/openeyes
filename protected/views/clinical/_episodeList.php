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
if (empty($episodes)) {
    echo 'No episodes.';
} else {
    foreach ($episodes as $episode) { ?>
        <div class="episode">
            <div class="title">
                <input type="hidden" name="episode-id" value="<?php echo $episode->id; ?>" />
                <span class="date"><?php echo $episode->NHSDate('start_date'); ?></span> - <?php
                echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name); ?></div>
            <ul class="events">
        <?php
        foreach ($episode->events as $event) { ?>
                <li><?php
                $text = '<span class="type">'.ucfirst($event->eventType->name).
                '</span><span class="date"> '.$event->NHSDate('created_date').
                '</span>';
                echo CHtml::link($text, array('clinical/view', 'id' => $event->id));
        } ?>
            </ul>
            <div class="footer"></div>
        </div>
    <?php }
}?>