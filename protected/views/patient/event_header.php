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

$this->renderPartial('//layouts/patientMode/event_header');
?>
		<h2>Episodes &amp; Events</h2>
		<div class="fullWidth fullBox clearfix">
			<div id="episodesBanner">
				<form>
					<button tabindex="2" class="classy green" id="addNewEvent" type="submit" style="float: right; margin-right: 1px;"><span class="button-span button-span-green with-plussign">add new Event</span></button>
				</form>
				<p style="margin-bottom: 0px;">&nbsp;</p>
			</div>
			<?php $this->renderPartial('//patient/episodes_sidebar',array('episodes'=>$episodes))?>
			<div id="event_display">
				<?php $this->renderPartial('//patient/add_new_event',array('eventTypes'=>$eventTypes))?>
				<div class="display_actions">
					<div class="display_mode"><?php echo $this->title?></div>
					<?php $this->renderPartial('//patient/edit_controls')?>
				</div>
				<div class="colorband category_treatement"></div>
				<!-- EVENT CONTENT HERE -->
				<div id="event_content" class="watermarkBox fullWidthEvent" style="background:#fafafa;">
