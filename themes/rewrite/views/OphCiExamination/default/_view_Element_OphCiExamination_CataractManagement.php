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
<div class="sub-element-data sub-element-eyes row">
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('eye_id')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->eye ? $element->eye->name : 'Not specified'?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('city_road')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->city_road ? 'Yes' : 'No'?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('satellite')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->satellite ? 'Yes' : 'No'?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('fast_track')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->fast_track ? 'Yes' : 'No'?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('target_postop_refraction')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->target_postop_refraction?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('correction_discussed')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->correction_discussed ? 'Yes' : 'No'?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('suitable_for_surgeon_id')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->suitable_for_surgeon->name?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('supervised')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->supervised ? 'Yes' : 'No'?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('previous_refractive_surgery')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->previous_refractive_surgery ? 'Yes' : 'No'?>
			</div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-4 column">
			<div class="data-label">
				<?php echo $element->getAttributeLabel('vitrectomised_eye')?>:
			</div>
		</div>
		<div class="large-8 column">
			<div class="data-value">
				<?php echo $element->vitrectomised_eye ? 'Yes' : 'No'?>
			</div>
		</div>
	</div>
</div>
