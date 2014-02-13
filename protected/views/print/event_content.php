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

<div class="container content event">
	<?php $this->renderOpenElements('print'); ?>
	<div class="metadata">
		<span class="info">Examination created by <span class="user"><?php echo $this->event->user->fullname ?></span>
			on <?php echo $this->event->NHSDate('created_date') ?>
			at <?php echo date('H:i', strtotime($this->event->created_date)) ?></span>
		<span class="info">Examination last modified by <span class="user"><?php echo $this->event->usermodified->fullname ?></span>
			on <?php echo $this->event->NHSDate('last_modified_date') ?>
			at <?php echo date('H:i', strtotime($this->event->last_modified_date)) ?></span>
	</div>
</div>