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

$active_pathway = $this->patient->getClinicPathwayInProgress();
?>

<?php if ((Yii::app()->controller->action->id != 'update' && Yii::app()->controller->action->id != 'create') || Yii::app()->controller->show_element_sidebar == false) { ?>
<nav class="sidebar-header" id="add-event-sidebar">
    <?php if (((!empty($ordered_episodes) || !empty($legacyepisodes) || !empty($supportserviceepisodes)) && $this->checkAccess('OprnCreateEpisode')) && !$this->patient->is_deceased) { ?>
        <button id="add-event" class="button green add-event" type="button" data-test="add-new-event-button">Add Event</button>
    <?php } else { ?>
        <button class="button add-event disabled">You have View Only rights</button>
    <?php } ?>
</nav>

<?php } ?>

<nav class="sidebar subgrid">
    <div class="fixed-actions">
    <?php
    $this->renderPartial('//patient/_single_oescape_sidebar', array(
        'legacyepisodes' => $legacyepisodes,
        'ordered_episodes' => $ordered_episodes,
        'supportserviceepisodes' => $supportserviceepisodes,
        'current_episode' => $current_episode,
    ));

    if ($active_pathway !== null) {
        $this->renderPartial('//patient/_patient_clinic_pathway_sidebar', [
            'pathway' => $active_pathway,
        ]);
    }
    ?>
    </div>
    <div class="sidebar-eventlist">
    <?php
    $this->renderPartial('//patient/_single_episode_sidebar', array(
        'legacyepisodes' => $legacyepisodes,
        'ordered_episodes' => $ordered_episodes,
        'supportserviceepisodes' => $supportserviceepisodes,
        'current_episode' => $current_episode,
    ));
    ?>
    </div>
</nav>

<div class="oe-event-quickview fade-in" id="js-event-quickview" style="display: none;">
    <div class="event-icon"></div>
    <div class="title"></div>
    <div class="quick-view-content img"></div>
    <div id="close-quickview" class="i-btn-close" style="display:none"></div>
    <i class="spinner" title="Loading images" style="display: none;"></i>
</div>
