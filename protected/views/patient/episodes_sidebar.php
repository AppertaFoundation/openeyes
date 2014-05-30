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
$current_episode = @$this->current_episode;
?>
<aside class="large-2 column sidebar episodes-and-events">

	<?php if ((!empty($ordered_episodes) || !empty($legacyepisodes) || !empty($supportserviceepisodes)) && $this->checkAccess('OprnCreateEpisode')) {?>
		<button class="secondary small add-episode" type="button" id="add-episode">
			<span class="icon-button-small-plus-sign"></span>
			Add Episode
		</button>
	<?php }?>

	<!-- Legacy events -->
	<?php $this->renderPartial('//patient/_legacy_events',array('legacyepisodes'=>$legacyepisodes))?>

	<?php
	if (is_array($ordered_episodes)) {
		foreach ($ordered_episodes as $specialty_episodes) {?>
			<div class="panel specialty">
				<h3 class="specialty-title"><?php echo $specialty_episodes['specialty'] ?></h3>

				<?php foreach ($specialty_episodes['episodes'] as $i => $episode) { ?>

					<section class="panel episode<?php echo empty($episode->end_date) ? ' closed' : ' open'?>">

						<input type="hidden" name="episode-id" value="<?php echo $episode->id ?>" />

						<!-- Episode date -->
						<div class="episode-date">
							<?php echo $episode->NHSDate('start_date'); ?>
						</div>

						<!-- Show/hide toggle icon -->
						<a href="#" class="toggle-trigger toggle-<?php if ((!$current_episode || $current_episode->id != $episode->id) && $episode->hidden) { ?>show<?php } else { ?>hide<?php } ?>">
							<span class="icon-showhide">
								Show/hide events for this episode
							</span>
						</a>

						<!-- Episode title -->
						<h4 class="episode-title">
							<?php echo CHtml::link(
								$episode->getSubspecialtyText(),
								array('/patient/episode/' . $episode->id),
								array('class' => (!$this->event && $current_episode && $current_episode->id == $episode->id) ? ' selected' : '')
							) ?>
						</h4>

						<!-- Episode event icons -->
						<ol class="events-overview" <?php  if ($episode->hidden) { ?>style = "display:block" <?php } else { ?> style = "display : none"<?php } ?>>
							<?php
								foreach ($episode->events as $event) {
								$event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/'; ?>
								<li>
									<a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
										<?php
										if (file_exists(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets'))) {
											$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/';
										} else {
											$assetpath = '/assets/';
										}
										?>
										<img src="<?php echo $assetpath . 'img/small.png' ?>" alt="op" width="19" height="19" />
									</a>
								</li>
							<?php } ?>
						</ol>

						<!-- Episode events -->
						<div <?php if ($episode->hidden) { ?>class="events-container hide"<?php } else { ?>class="events-container show"<?php } ?>>

							<?php
							if ($this->checkAccess('OprnCreateEvent')) {
								$enabled = $this->checkAccess('OprnCreateEvent', $this->firm, $episode);
								?>

								<button
									<?php echo ($enabled) ? "" : " disabled "; ?>
									class="button secondary tiny add-event addEvent <?php echo ($enabled) ? "enabled" : "disabled "; ?>"
									type="button"
									data-attr-subspecialty-id="<?php echo $episode->firm ? $episode->firm->getSubspecialtyID() : ''; ?>"
									<?php if (!$enabled) echo 'title="Please switch firm to add an event to this episode"'; ?>>
									<span class="icon-button-small-plus-sign"></span>
									Add event
								</button>

								<?php
								$ssa = $episode->firm ? $episode->firm->serviceSubspecialtyAssignment : null;
								$subspecialty_data = $ssa ? array_intersect_key($ssa->subspecialty->attributes, array_flip(array('id','name'))) : array();
								if($enabled) { ?>
									<script type="text/html" id="add-new-event-template" data-specialty='<?php echo json_encode($subspecialty_data);?>'>
										<?php $this->renderPartial('//patient/add_new_event',array(
												'episode' => $episode,
												'subspecialty' => @$ssa->subspecialty,
												'patient' => $this->patient,
												'eventTypes' => EventType::model()->getEventTypeModules(),
											));?>
									</script>
								<?php }?>
							<?php }?>

							<ol class="events">
								<?php	foreach ($episode->events as $event) {
									$highlight = false;

									if (isset($this->event) && $this->event->id == $event->id) {
										$highlight = TRUE;
									}

									$event_path = Yii::app()->createUrl($event->eventType->class_name . '/default/view') . '/';
									?>
									<li id="eventLi<?php echo $event->id ?>"<?php if ($highlight) { ?> class="selected"<?php }?>>

										<!-- Quicklook tooltip -->
										<div class="tooltip quicklook" style="display: none; ">
											<div class="event-name"><?php echo $event->eventType->name ?></div>
											<div class="event-info"><?php echo str_replace("\n", "<br/>", $event->info) ?></div>
											<?php if ($event->hasIssue()) { ?>
												<div class="event-issue"><?php echo $event->getIssueText() ?></div>
											<?php } ?>
										</div>

										<a href="<?php echo $event_path . $event->id ?>" data-id="<?php echo $event->id ?>">
											<span class="event-type<?php if ($event->hasIssue()) { ?> alert<?php } ?>">
												<?php
												if (file_exists(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets'))) {
													$assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $event->eventType->class_name . '.assets')) . '/';
												} else {
													$assetpath = '/assets/';
												}
												?>
												<img src="<?php echo $assetpath . 'img/small.png' ?>" alt="op" width="19" height="19" />
											</span>
											<span class="event-date <?php echo ($event->isEventDateDifferentFromCreated())?' ev_date':'' ?>"> <?php echo $event->event_date? $event->NHSDateAsHTML('event_date') : $event->NHSDateAsHTML('created_date'); ?></span>
										</a>

									</li>
								<?php } ?>
							</ol>
						</div>
					</section>

					<div class="episode_details hide" id="episode-details-<?php echo $episode->id ?>">
						<div class="row"><span class="label">Start date:</span><?php echo $episode->NHSDate('start_date'); ?></div>
						<div class="row"><span class="label">End date:</span><?php echo ($episode->end_date ? $episode->NHSDate('end_date') : '-') ?></div>
						<div class="row"><span class="label">Principal eye:</span><?php echo ($episode->diagnosis) ? ($episode->eye ? $episode->eye->name : 'None') : 'No diagnosis' ?></div>
						<div class="row"><span class="label">Principal diagnosis:</span><?php echo ($episode->diagnosis) ? ($episode->diagnosis ? $episode->diagnosis->term : 'none') : 'No diagnosis' ?></div>
						<div class="row"><span class="label">Subspecialty:</span><?php echo CHtml::encode($episode->getSubspecialtyText()) ?></div>
						<div class="row"><span class="label">Consultant firm:</span><?php echo $episode->firm ? CHtml::encode($episode->firm->name) : 'N/A' ?></div>
						<img class="folderIcon" src="<?php echo Yii::app()->assetManager->createUrl('img/_elements/icons/folder_open.png') ?>" alt="folder open" />
					</div>
				<?php } ?>

			</div>
		<?php } ?>
	<?php } ?>

	<script type="text/javascript">
		$(document).ready(function() {
			$('.sidebar.episodes-and-events .quicklook').each(function() {
				var quick = $(this);
				var iconHover = $(this).parent().find('.event-type');
				iconHover.hover(function(e) {
					quick.fadeIn('fast');
				}, function(e) {
					quick.hide();
				});
			});
		});
	</script>
</aside>
