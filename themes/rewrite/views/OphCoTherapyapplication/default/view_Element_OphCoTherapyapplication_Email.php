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
<h4 class="elementTypeName">Application</h4>

<?php if (!$element->sent) {?>
	<p><strong>This application has been prepared but not sent due to a system failure sending the email - please try again.</strong></p>
<?php }?>

<div class="cols2 clearfix">
	<div class="left eventDetail">
		<?php if ($element->hasRight()) {?>
			<div class="eventDetail aligned">
				<div class="label">Application files</div>
				<div class="data">
					<ul style="margin: 0px;">
						<?php foreach ($element->right_attachments as $att) {?>
							<li><a href="<?php echo $att->getDownloadURL()?>"><?php echo $att->name; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php } else { ?>
		N/A
		<?php } ?>
	</div>
	<div class="right eventDetail">
		<?php if ($element->hasLeft()) {?>
			<div class="eventDetail aligned">
				<div class="label">Application files</div>
				<div class="data">
					<ul style="margin: 0px;">
						<?php foreach ($element->left_attachments as $att) {?>
							<li><a href="<?php echo $att->getDownloadURL()?>"><?php echo $att->name; ?></a></li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php } else { ?>
		N/A
		<?php } ?>
	</div>
</div>
<div class="metaData">
Application sent by <span class="user"><?php echo $element->user->fullname ?></span> on <?php echo $element->NHSDate('created_date') ?>
		at <?php echo date('H:i', strtotime($element->created_date)) ?>
</div>
