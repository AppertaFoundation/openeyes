		<h2>Episodes &amp; Events</h2>
		<div class="fullWidth fullBox clearfix">
			<div id="episodesBanner whiteBox">
				<form><button type="submit" value="submit" class="btn_newEvent ir" id="addNewEvent">Add New Event</button></form>
				<p><img style="position:relative; top:-2px;"src="/img/_elements/icons/event_blank.png" alt="event_blank" width="16" height="16" /> <strong>&nbsp;Event: HPC</strong></p>
			</div>
			<div id="episodes_sidebar">
				<div class="episode open clearfix">
					<div class="episode_nav">
						<input type="hidden" name="episode-id" value="1" />
						<div class="small">27/09/11<span style="float:right;"><a href="#">Details</a><span></div>
						<h4>Medical Retina Service</h4>
						<ul class="events">
							<li><a href="#"><span class="type"><img src="/img/_elements/icons/event_op_unscheduled.png" alt="op" width="16" height="16" /></span><span class="date"> 27/09/2011</span></a></li>
							<li><a href="/clinical/2"><span class="type"><img src="/img/_elements/icons/event_op_unscheduled.png" alt="op" width="16" height="16" /></span><span class="date"> 08/09/2011</span></a></li>
						</ul>
					</div>

					<div class="episode_details hidden">
						<div class="row"><span class="label">Start date:</span>27th September, 2011</div>
						<div class="row"><span class="label">End date:</span>-</div>
						<div class="row"><span class="label">Principal eye:</span>Right</div>
						<div class="row"><span class="label">Principal diagnosis:</span>Choroidal haemorrhage</div>
						<div class="row"><span class="label">Specialty:</span>Adnexal</div>
						<div class="row"><span class="label">Consultant firm:</span>Abou-Rayyah Yassir</span></div>
						<img class="folderIcon"src="/img/_elements/icons/folder_open.png" alt="folder open" />
					</div>
				</div> <!-- .episode -->
				<?php foreach ($episodes as $episode) {?>
					<div class="episode open clearfix">
						<div class="episode_nav">
							<input type="hidden" name="episode-id" value="1" />
							<div class="small"><?php echo date('d.m.Y',strtotime($episode->start_date))?><span style="float:right;"><a href="#">Details</a><span></div>
							<h4><?php echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name)?></h4>
							<ul class="events">
								<?php foreach ($episode->events as $event) {?>
									<?php
									//die("<pre>".print_r($event,true))?>
									<li><a href="#"><span class="type"><img src="/img/_elements/icons/event_op_unscheduled.png" alt="op" width="16" height="16" /></span><span class="date"> <?php echo date('d/m/Y',strtotime($event->datetime))?></span></a></li>
									<?php /*
									<li><a href="#"><span class="type"><img src="/img/_elements/icons/event_op_scheduled.png" alt="op" width="16" height="16" /></span><span class="date"> 19/09/2011</span></a></li>
									*/ ?>
								<?php }?>
							</ul>
						</div>

						<div class="episode_details hidden">
							<div class="row"><span class="label">Start date:</span><?php echo date('jS F, Y',strtotime($episode->start_date))?></div>
							<div class="row"><span class="label">End date:</span><?php echo ($episode->end_date ? date('jS F, Y',strtotime($episode->end_date)) : '-')?></div>
							<div class="row"><span class="label">Principal eye:</span><?php echo $episode->getPrincipalDiagnosis()->getEyeText()?></div>
							<div class="row"><span class="label">Principal diagnosis:</span><?php echo ($episode->getPrincipalDiagnosis() ? $episode->getPrincipalDiagnosis()->disorder->term : 'No diagnosis')?></div>
							<div class="row"><span class="label">Specialty:</span><?php echo CHtml::encode($episode->firm->serviceSpecialtyAssignment->specialty->name)?></div>
							<div class="row"><span class="label">Consultant firm:</span><?php echo CHtml::encode($episode->firm->name)?></span></div>
							<img class="folderIcon"src="/img/_elements/icons/folder_open.png" alt="folder open" />
						</div>
					</div> <!-- .episode -->
				<?php }?>
			</div> <!-- #episodes_sidebar -->
			<div id="event_display">
					<div class="display_actions">
						<div class="display_mode">View mode</div>
						<div class="action_options"><span class="aLabel">you can: </span><span class="aBtn_inactive">View</span><span class="aBtn"><a href="#">Edit</a></span><span class="aBtn"><a href="#">Save</a></span><span class="aBtn"><a href="#">Delete</a></span></div>
					</div>
					<!-- EVENT CONTENT HERE -->
					<div id="event_content">
						<img src="/img/_test_event.png" alt="_test_event" width="1040" height="654" />
					</div>
					<!-- #event_content -->
					<div class="display_actions footer">
						<div class="action_options"><span class="aLabel">you can: </span><span class="aBtn_inactive">View</span><span class="aBtn"><a href="#">Edit</a></span><span class="aBtn"><a href="#">Save</a></span><span class="aBtn"><a href="#">Delete</a></span></div>
					</div>
			</div><!-- #event_display -->

		</div> <!-- .fullWidth -->
