<?php
/**
 * OpenEyes
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>

<div class="box admin">
	<header class="box-header">
		<h2 class="box-title"><?php echo $title ?></h2>
		<div class="box-actions">

		</div>
	</header>

	<div class="alert-box info" style="display: none;" id="message-box">
	</div>
	<div class="row">
		<div class="column large-4">

			<div class="row">
			<ul class="queueset-list" id="queue-nav">
			<?php
			foreach ($queuesets as $qs) {
				$this->renderPartial("queue_nav_item", array('queueset' => $qs));
			}
			?>
			</ul>
			</div>
			<div class="row right"><button id="add-queueset" class="secondary small">Add Queue Set</button></div>

		</div>

		<div id="chart" class="column large-8 end orgChart"></div>
	</div>
</div>