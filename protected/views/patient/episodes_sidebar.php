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

<?php
extract($this->getEpisodes());
$current_episode = @$this->current_episode;
?>

<nav class="sidebar-header">
    <?php if ((!empty($ordered_episodes) || !empty($legacyepisodes) || !empty($supportserviceepisodes)) && $this->checkAccess('OprnCreateEpisode')) { ?>
      <button id="add-event" class="button green add-event" type="button">Add Event</button>
    <?php } else { ?>
    <nav class="sidebar-header">
        <span class="add-event" title="You do not have permission to create new events">
            <i class="button add-event disabled">Add Event</i>
        </span>
    </nav>
    <?php } ?>
</nav>

<nav class="sidebar">
    <?php
    $this->renderPartial('//patient/_single_oescape_sidebar', array(
        'legacyepisodes' => $legacyepisodes,
        'ordered_episodes' => $ordered_episodes,
        'supportserviceepisodes' => $supportserviceepisodes,
        'current_episode' => $current_episode,
    ));

    $this->renderPartial('//patient/_single_episode_sidebar', array(
        'legacyepisodes' => $legacyepisodes,
        'ordered_episodes' => $ordered_episodes,
        'supportserviceepisodes' => $supportserviceepisodes,
        'current_episode' => $current_episode,
    ));

    ?>
</nav>