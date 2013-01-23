<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
if ($module = $this->getModule()) {
	$module = $module->getName();
	$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.'.$module.'.img'),true).'/';
} else {
	$module = 'OphTrOperation';
}
?>
		<h2>Episodes &amp; Events</h2>
		<div class="fullWidth fullBox clearfix">
			<?php if ($this->patient->isDeceased()) {?>
				<div id="deceased-notice" class="alertBox">
					This patient is deceased (<?php echo $this->patient->NHSDate('date_of_death'); ?>)
				</div>
			<?php }?>
			<div id="episodesBanner">
				<form>
					<button tabindex="2" class="classy venti green" id="addNewEvent" type="submit" style="float: right; margin-right: 1px;"><span class="button-span button-span-green with-plussign">add new Event</span></button>
				</form>
				<p style="margin-bottom: 0px;"><strong>&nbsp;<?php if (count($episodes) <1) {?>No Episodes for this patient<?php }?></strong></p>
			</div>
			<?php $this->renderPartial('episodes_sidebar',array('ordered_episodes' => $ordered_episodes, 'current_episode'=>@$current_episode, 'legacyepisodes'=>$legacyepisodes))?>
			<div id="event_display">
				<?php $this->renderPartial('add_new_event',array('eventTypes'=>$eventTypes))?>
				<?php
				if (count($episodes) <1) {?>
					<div class="alertBox fullWidthEvent">
						There are currently no episodes for this patient, please add a new event to open an episode.
					</div>
				<?php }else if (!@$current_episode) {?>
					<div class="alertBox fullWidthEvent">
						There is no open episode for the current firm's subspecialty.
					</div>
				<?php }?>
				<div class="display_actions"<?php if (!$this->title || count($episodes)<1 || !@$current_episode){?> style="display: none;"<?php }?>>
					<div class="display_mode"><?php echo $this->title?></div>
					<?php $this->renderPartial('edit_controls')?>
				</div>

				<!-- EVENT CONTENT HERE -->
				<?php if (is_object($this->event) || (count($episodes) >0 && @$current_episode)) {?>
					<?php if ($module == 'OphTrOperation') {?>
						<div id="event_content" class="watermarkBox" style="background:#fafafa url(<?php echo Yii::app()->createUrl('img/_elements/icons/event/watermark/treatment_operation.png')?>) top left repeat-y;">
					<?php } else {?>
						<div id="event_content" class="watermarkBox" style="background:#fafafa url(<?php echo $assetpath?>watermark.png) top left repeat-y;">
					<?php }?>
				<?php }?>
					<?php
					if (isset($this->event)) {
						$this->renderPartial(
							"/clinical/".$event_template_name,
							array(
								'elements' => $elements,
								'site' => $site
							), false, true
						);
					} else if ($current_episode) {
						if ($this->editing) {
							$this->renderPartial('/clinical/updateEpisode',
								array('episode' => $current_episode, 'error' => $error)
							);
						} else {
							$this->renderPartial('/clinical/episodeSummary',
								array('episode' => $current_episode)
							);
						}
					}
					?>
				</div>
				<!-- #event_content -->
				<div class="colorband category_treatment"<?php if(!$this->title){ ?> style="display: none;"<?php } ?>></div>
				<div id="display_actions_footer" class="display_actions footer"<?php if (!$this->title){?> style="display: none;"<?php }?>>
					<?php $this->renderPartial('edit_controls')?>
				</div>
			</div><!-- #event_display -->
		</div> <!-- .fullWidth -->
