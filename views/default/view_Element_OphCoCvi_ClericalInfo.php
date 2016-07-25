<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

?>

<section class="element">
	<header class="element-header">
		<h3 class="element-title"><?php echo $element->elementType->name?></h3>
	</header>
	<div class="element-data">
				<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('employment_status_id'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?php echo $element->employment_status ? $element->employment_status->name : 'None'?></div></div>
		</div>
		<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_info_fmt_id'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?php echo $element->preferred_info_fmt ? $element->preferred_info_fmt->name : 'None'?></div></div>
		</div>
		<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('info_email'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?php echo CHtml::encode($element->info_email)?></div></div>
		</div>
		<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('contact_urgency_id'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?php echo $element->contact_urgency ? $element->contact_urgency->name : 'None'?></div></div>
		</div>
		<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('preferred_language_id'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?php echo $element->preferred_language ? $element->preferred_language->name : 'None'?></div></div>
		</div>
		<div class="row data-row">
			<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('social_service_comments'))?></div></div>
			<div class="large-10 column end"><div class="data-value"><?php echo CHtml::encode($element->social_service_comments)?></div></div>
		</div>
	</div>
</section>
