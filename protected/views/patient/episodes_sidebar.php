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
<div id="episodes_sidebar">
	<?php if ((!empty($ordered_episodes) || !empty($legacyepisodes) || !empty($supportserviceepisodes)) && BaseController::checkUserLevel(4)) {?>
		<div align="center" style="margin-top:5px; margin-bottom: 5px;">
			<button class="classy blue mini addEpisode" type="button"><span class="button-span button-span-blue">Add episode</span></button>
		</div>
	<?php }?>
	<?php $this->renderPartial('//patient/_legacy_events',array('legacyepisodes'=>$legacyepisodes))?>
	<?php $this->renderPartial('//patient/_support_service_events',array('supportserviceepisodes'=>$supportserviceepisodes))?>
	<?php
	if (is_array($ordered_episodes))
		foreach ($ordered_episodes as $specialty_episodes) {?>
			<div class="specialty small"><?php echo $specialty_episodes['specialty']->name ?></div>
			<?php foreach ($specialty_episodes['episodes'] as $i => $episode) { ?>
				<div class="episode <?php echo empty($episode->end_date) ? 'closed' : 'open' ?> clearfix">
					<div class="episode_nav">
						<input type="hidden" name="episode-id" value="<?php echo $episode->id ?>" />
						<div class="start_date small">
							<?php echo $episode->NHSDate('start_date') ?>
							<span class="aBtn">
								<a class="sprite showhide2" href="#">
									<span class="<?php if ((!@$current_episode || $current_episode->id != $episode->id) && $episode->hidden) { ?>show<?php } else { ?>hide<?php } ?>"></span>
								</a>
							</span>
						</div>
						<h4><?php echo CHtml::link(CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name), array('/patient/episode/' . $episode->id), array('class' => 'title_summary' . ((!$this->event && @$current_episode && $current_episode->id == $episode->id) ? ' viewing' : ''))) ?></h4>
						<!-- shows miniicons for the events -->
							<div class = "minievents" <?php if ($episode->hidden) { ?>style = "display : inline" <?php } else { ?> style = "display : none"<?php } ?>>
								<?php foreach ($episode->events as $event) {
									$event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/'; ?>
									<a href="<?php echo $event_path . $event->id ?>" rel="<?php echo $event->id ?>" class="show-event-details">
											<?php
											if (file_exists(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets'))) {
												$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/';
											} else {
												$assetpath = '/assets/';
											}
											?>
											<img src="<?php echo $assetpath . 'img/small.png' ?>" alt="op" width="19" height="19" />
									</a>
								<?php } ?>
							</div>
						<!-- end shows miniicons for the events -->
						<div <?php if ($episode->hidden) { ?>class="events show" style="display: none;"<?php } else { ?>class="events hide"<?php } ?>>
							<?php if (BaseController::checkUserLevel(4)) {?>
								<?php if ($episode->status->name != 'Discharged') {?>
									<div align="center" style="margin-top:5px; margin-bottom: 5px;">
										<button class="classy blue mini addEvent" type="button" data-attr-subspecialty-id="<?php echo $episode->firm->serviceSubspecialtyAssignment->subspecialty_id?>"><span class="button-span button-span-blue">Add event</span></button>
									</div>
								<?php }?>
							<?php }?>
							<ul class="events">
								<?php
								foreach ($episode->events as $event) {
									$highlight = false;

									if (isset($this->event) && $this->event->id == $event->id) {
										$highlight = TRUE;
									}

									$event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/';
									?>
									<li id="eventLi<?php echo $event->id ?>">
										<div class="quicklook" style="display: none; ">
											<span class="event"><?php echo $event->eventType->name ?></span>
											<span class="info"><?php echo str_replace("\n", "<br/>", $event->info) ?></span>
											<?php if ($event->hasIssue()) { ?>
												<span class="issue"><?php echo $event->getIssueText() ?></span>
											<?php } ?>
										</div>
										<?php if ($highlight) { ?>
											<div class="viewing">
											<?php } else { ?>
												<a href="<?php echo $event_path . $event->id ?>" rel="<?php echo $event->id ?>" class="show-event-details">
												<?php } ?>
												<span class="type<?php if ($event->hasIssue()) { ?> statusflag<?php } ?>">
													<?php
													if (file_exists(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets'))) {
														$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/';
													} else {
														$assetpath = '/assets/';
													}
													?>
													<img src="<?php echo $assetpath . 'img/small.png' ?>" alt="op" width="19" height="19" />
												</span>
												<span class="date"> <?php echo $event->NHSDateAsHTML('created_date'); ?></span>
												<?php if (!$highlight) { ?>
												</a>
											<?php } else { ?>
											</div>
										<?php } ?>
									</li>
								<?php } ?>
							</ul>
						</div>
					</div>
					<div class="episode_details hidden" id="episode-details-<?php echo $episode->id ?>">
						<div class="row"><span class="label">Start date:</span><?php echo $episode->NHSDate('start_date'); ?></div>
						<div class="row"><span class="label">End date:</span><?php echo ($episode->end_date ? $episode->NHSDate('end_date') : '-') ?></div>
						<div class="row"><span class="label">Principal eye:</span><?php echo ($episode->diagnosis) ? ($episode->eye ? $episode->eye->name : 'None') : 'No diagnosis' ?></div>
						<div class="row"><span class="label">Principal diagnosis:</span><?php echo ($episode->diagnosis) ? ($episode->diagnosis ? $episode->diagnosis->term : 'none') : 'No diagnosis' ?></div>
						<div class="row"><span class="label">Subspecialty:</span><?php echo CHtml::encode($episode->firm->serviceSubspecialtyAssignment->subspecialty->name) ?></div>
						<div class="row"><span class="label">Consultant firm:</span><?php echo CHtml::encode($episode->firm->name) ?></div>
						<img class="folderIcon" src="<?php echo Yii::app()->createUrl('img/_elements/icons/folder_open.png') ?>" alt="folder open" />
					</div>
				</div>
			<?php } ?>
		<?php } ?>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.quicklook').each(function() {
			var quick = $(this);
			var iconHover = $(this).parent().find('.type');
			iconHover.hover(function(e) {
				quick.fadeIn('fast');
			}, function(e) {
				quick.fadeOut('fast');
			});
		});
	});
</script>
