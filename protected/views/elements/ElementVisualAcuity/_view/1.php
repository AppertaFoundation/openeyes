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

?><br />
Visual Acuity:

<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('rva_ua')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'rva_ua')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rva_ph')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'rva_ph')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('rva_cr')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'rva_cr')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('right_aid')); ?>:</b>
	<?php echo CHtml::encode($data->getAidText('right_aid')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lva_ua')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'lva_ua')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lva_ph')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'lva_ph')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('lva_cr')); ?>:</b>
	<?php echo CHtml::encode($data->getVisualAcuityText(ElementVisualAcuity::SNELLEN_METRE, 'lva_cr')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('left_aid')); ?>:</b>
	<?php echo CHtml::encode($data->getAidText('left_aid')); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('distance')); ?>:</b>
	<?php echo CHtml::encode($data->getDistanceText(ElementVisualAcuity::SNELLEN_METRE)); ?> metres
	<br />

</div>