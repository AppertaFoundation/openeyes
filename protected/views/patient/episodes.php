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
$current_episode = isset($current_episode) ? $current_episode : @$this->current_episode;
?>

<?php if($noEpisodes) { ?>
  <div class="oe-sem-no-events">
    <h3>No Events</h3>
    <div class="alert-box alert">
      There are currently no events for this patient.<br>Click the "Add Event" button to begin recording events.
    </div>
    <nav class="sidebar-header">
        <?php if ($this->checkAccess('OprnCreateEpisode')) { ?>
            <button id="add-event" class="button green add-event" type="button">Add Event</button>
        <?php } else { ?>
            <button class="button add-event disabled">You have View Only rights and cannot create events</button>
        <?php } ?>
    </nav>
  </div>
    <?php $this->renderPartial('//patient/add_new_event',array(
        'button_selector' => '#add-event',
        'episodes' => array(),
        'context_firm' => $this->firm,
        'patient_id' => $this->patient->id,
        'event_types' => EventType::model()->getEventTypeModules(),
    ));?>
<?php } else {

    $this->beginContent('//patient/episodes_container', array(
        'css_class' => isset($cssClass) ? $cssClass : '',
        'episode' => isset($current_episode)? $current_episode: ''
    ));

    if ($current_episode) {
        if ($this->editing) {
            $this->renderPartial('/clinical/updateEpisode',
                array('episode' => $current_episode, 'error' => $error)
            );
        } else {
            $this->renderPartial('/clinical/episodeSummary',
                array('episode' => $current_episode)
            );
        }
    } elseif (count($legacyepisodes)) {?>
		<h2>No episodes</h2>
		<div class="alert-box alert with-icon">
			There are currently no events for this patient, please click the Add <?= strtolower(Episode::getEpisodeLabel()) ?> button to begin recording events.
		</div>
	<?php }
    $this->endContent();
}?>