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
					<li class="auditlist<?php echo ($i % 2 == 0) ? 'Even' : 'Odd'; echo $log->colour;?>" id="audit<?php echo $log->id?>"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
						<span class="timestamp"><a href="#" id="auditItem<?php echo $log->id?>" class="auditItem"><?php echo $log->NHSDate('created_date').' '.substr($log->created_date,11,8)?></a></span>
						<span class="site"><?php echo $log->site ? ($log->site->short_name ? $log->site->short_name : $log->site->name) : '-'?></span>
						<span class="firm"><?php echo $log->firm ? $log->firm->name : '-'?></span>
						<span class="user"><?php echo $log->user ? $log->user->first_name.' '.$log->user->last_name : '-'?></span>
						<span class="action"><?php echo $log->action->name?></span>
						<span class="target"><?php echo $log->target_type ? $log->target_type->name : ''?></span>
						<span class="event_type">
							<?php if ($log->event) { ?>
								<a href="/<?php echo $log->event->eventType->class_name?>/default/view/<?php echo $log->event_id?>"><?php echo $log->event->eventType->name?></a>
							<?php }else{?>
								-
							<?php }?>
						</span>
						<span class="patient">
							<?php if ($log->patient) {?>
								<?php echo CHtml::link($log->patient->displayName,array('patient/view/'.$log->patient_id))?>
							<?php }else{?>
								-
							<?php }?>
						</span>
						<span class="episode">
							<?php if ($log->episode) {?>
								<?php echo CHtml::link('view',array('patient/episode/'.$log->episode_id))?>
							<?php }else{?>
								-
							<?php }?>
						</span>
					</li>
					<li class="auditlist<?php echo ($i % 2 == 0) ? 'Even' : 'Odd'; echo $log->colour;?> auditextra<?php echo $log->id?>" style="display: none;">
						<div class="auditDetail<?php echo ($i % 2 == 0) ? 'Even' : 'Odd'; echo $log->colour;?> whiteBox">
							<div>
								<span class="auditDetailLabel">IP address:</span>
								<span><?php echo $log->ip_addr ? $log->ip_addr->name : '-'?></span>
							</div>
							<div>
								<span class="auditDetailLabel">Server name:</span>
								<span><?php echo $log->server ? $log->server->name : '-' ?></span>
							</div>
							<div>
								<span class="auditDetailLabel">Request URI:</span>
								<span><?php echo $log->request_uri?></span>
							</div>
							<div>
								<span class="auditDetailLabel">User agent:</span>
								<span><?php echo $log->user_agent ? $log->user_agent->name : '-' ?></span>
							</div>
							<div>
								<span class="auditDetailLabel">Data:</span>
								<span id="dataspan<?php echo $log->id?>">
									<?php
									if (@unserialize($log->data)) {?>
										<a href="#" id="showData<?php echo $log->id?>" class="showData">show data</a>
										<input type="hidden" name="data<?php echo $log->id?>" value="<?php echo htmlentities($log->data)?>" />
									<?php }else{
										echo $log->data ? $log->data : 'None';
									}?>
								</span>
							</div>
						</div>
					</li>
