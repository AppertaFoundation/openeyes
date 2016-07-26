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


<div class="element-data">
	<div class="row data-row">
		<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('is_patient'))?>:</div></div>
		<div class="large-10 column end"><div class="data-value"><?php echo $element->is_patient ? 'Yes' : 'No'?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('signature_date'))?></div></div>
		<div class="large-10 column end"><div class="data-value"><?php echo CHtml::encode($element->NHSDate('signature_date'))?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('representative_name'))?></div></div>
		<div class="large-10 column end"><div class="data-value"><?php echo CHtml::encode($element->representative_name)?></div></div>
	</div>
	<div class="row data-row">
		<div class="large-2 column"><div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('signature_file_id'))?></div></div>
		<div class="large-10 column end"><div class="data-value"><?php echo $element->signature_file ? $element->signature_file->name : 'None'?></div></div>
	</div>
</div>
