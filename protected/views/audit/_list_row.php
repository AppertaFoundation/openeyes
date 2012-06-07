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

?>
					<li class="auditlist<?php echo ($i % 2 == 0) ? 'Even' : 'Odd'; echo $log->colour;?>" id="audit<?php echo $log->id?>"<?php if (@$hidden) {?> style="display: none;"<?php }?>>
						<span class="timestamp"><a href="#" id="auditItem<?php echo $log->id?>" class="auditItem"><?php echo $log->NHSDate('created_date').' '.substr($log->created_date,11,8)?></a></span>
						<span class="site"><?php echo $log->site ? $log->site->name : '-'?></span>
						<span class="firm"><?php echo $log->firm ? $log->firm->name : '-'?></span>
						<span class="user"><?php echo $log->user ? $log->user->first_name.' '.$log->user->last_name : '-'?></span>
						<span class="action"><?php echo $log->action?></span>
						<span class="target"><?php echo $log->target_type?></span>
						<span class="patient">
							<?php if ($log->patient) {?>
								<a href="/patient/view/<?php echo $log->patient_id?>">
									<?php echo $log->patient->displayName?>
								</a>
							<?php }else{?>
								-
							<?php }?>
						</span>
						<span class="episode">
							<?php if ($log->episode) {?>
								<a href="/patient/episode/<?php echo $log->episode_id?>">view</a>
							<?php }else{?>
								-
							<?php }?>
						</span>
						<span class="event">
							<?php if ($log->event) {?>
								<a href="/patient/event/<?php echo $log->event_id?>">view</a>
							<?php }else{?>
								-
							<?php }?>
						</span>
					</li>
					<li class="auditlist<?php echo ($i % 2 == 0) ? 'Even' : 'Odd'; echo $log->colour;?> auditextra<?php echo $log->id?>" style="display: none;">
						<div class="auditDetail<?php echo ($i % 2 == 0) ? 'Even' : 'Odd'; echo $log->colour;?> whiteBox">
							<div>
								<span class="auditDetailLabel">IP address:</span>
								<span><?php echo $log->remote_addr?></span>
							</div>
							<div>
								<span class="auditDetailLabel">Server name:</span>
								<span><?php echo $log->server_name?></span>
							</div>
							<div>
								<span class="auditDetailLabel">Request URI:</span>
								<span><?php echo $log->request_uri?></span>
							</div>
							<div>
								<span class="auditDetailLabel">User agent:</span>
								<span><?php echo $log->http_user_agent?></span>
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
