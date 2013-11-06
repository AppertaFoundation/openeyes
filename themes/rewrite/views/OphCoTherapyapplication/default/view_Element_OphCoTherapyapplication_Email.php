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

<section class="element">
	<header class="element-header">
		<h3 class="element-title">Application</h3>
	</header>

	<?php if (!$element->sent) {?>
		<div class="alert-box alert"><strong>This application has been prepared but not sent due to a system failure sending the email - please try again.</strong></div>
	<?php }?>

	<div class="element-data element-eyes row">
		<div class="element-eye right-eye column">
			<?php if ($element->hasRight()) {?>
				<div class="row field-row">
					<div class="large-4 column">
						<div class="data-label">Application files:</div>
					</div>
					<div class="large-8 column end">
						<div class="data-value">
							<ul>
								<?php foreach ($element->right_attachments as $att) {?>
									<li><a href="<?php echo $att->getDownloadURL()?>"><?php echo $att->name; ?></a></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="eye-mesage">
					N/A
				</div>
			<?php } ?>
		</div>
		<div class="element-eye left-eye column">
			<?php if ($element->hasLeft()) {?>
				<div class="row field-row">
					<div class="large-4 column">
						<div class="data-label">Application files:</div>
					</div>
					<div class="large-8 column end">
						<div class="data-value">
							<ul>
								<?php foreach ($element->left_attachments as $att) {?>
									<li><a href="<?php echo $att->getDownloadURL()?>"><?php echo $att->name; ?></a></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			<?php } else { ?>
				<div class="eye-mesage">
					N/A
				</div>
			<?php } ?>
		</div>
	</div>
	<div class="metadata">
	Application sent by <span class="user"><?php echo $element->user->fullname ?></span> on <?php echo $element->NHSDate('created_date') ?>
			at <?php echo date('H:i', strtotime($element->created_date)) ?>
	</div>
</section>