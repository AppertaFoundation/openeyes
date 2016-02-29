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

<section class="element <?php echo $element->elementType->class_name?> row">
	<h3 class="element-title"><?php echo $element->elementType->name ?></h3>
	<div class="row">
		<div class="large-6 column end">
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('gas_type_id')); ?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->gas_type->name ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('gas_percentage_id')); ?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->gas_percentage->value ?>%
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('gas_volume_id')); ?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->gas_volume->value ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
