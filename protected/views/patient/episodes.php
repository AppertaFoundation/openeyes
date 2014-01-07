<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<?php
extract($this->getEpisodes());
$current_episode = isset($current_episode) ? $current_episode : @$this->current_episode;
$noEpisodes = (count($episodes) <1 && count($supportserviceepisodes) <1 && count($legacyepisodes) <1);
?>

<h1 class="badge">Episodes and events</h1>

<?php if($noEpisodes && $this->checkAccess('OprnCreateEpisode')) { ?>
	<div class="row">
		<div class="large-8 large-centered column">
			<div class="box content">
				<div class="panel">
					<div class="alert-box alert with-icon">
						There are currently no episodes for this patient, please click the Add episode button to open a new episode.
					</div>
					<button class="small add-episode" id="add-episode">
						Add episode
					</button>
				</div>
			</div>
		</div>
	</div>
<?php } else {

		$this->beginContent('//patient/episodes_container');

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
		} else if (count($legacyepisodes)) {?>
			<h2>No episodes</h2>
			<div class="alert-box alert with-icon">
				There are currently no episodes for this patient, please click the Add episode button to open a new episode.
			</div>
		<?php }
		$this->endContent();
}?>