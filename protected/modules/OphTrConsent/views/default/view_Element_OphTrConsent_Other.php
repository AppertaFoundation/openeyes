<?php
/**
* OpenEyes.
*
* (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
* (C) OpenEyes Foundation, 2011-2013
* This file is part of OpenEyes.
* OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
* OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
* You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
*
* @link http://www.openeyes.org.uk
*
* @author OpenEyes <info@openeyes.org.uk>
* @copyright Copyright (c) 2011-2013, OpenEyes Foundation
* @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
*/
?>
<div class="element-data">
	<div class="row data-row">
		<div class="large-3 column">
			<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('consultant_id'))?>:</div>
		</div>
		<div class="large-9 column">
			<div class="data-value"><?php echo $element->consultant->fullNameAndTitle?></div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-3 column">
			<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('information'))?>:</div>
		</div>
		<div class="large-9 column">
			<div class="data-value"><?php echo $element->information ? 'Yes' : 'No'?></div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-3 column">
			<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_leaflet'))?>:</div>
		</div>
		<div class="large-9 column">
			<div class="data-value"><?php echo $element->anaesthetic_leaflet ? 'Yes' : 'No'?></div>
		</div>
	</div>
	<div class="row data-row">
		<div class="large-3 column">
			<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('witness_required'))?>:</div>
		</div>
		<div class="large-9 column">
			<div class="data-value"><?php echo $element->witness_required ? 'Yes' : 'No'?></div>
		</div>
	</div>

	<?php if ($element->witness_required) {?>
		<div class="row data-row">
			<div class="large-3 column">
				<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('witness_name'))?>:</div>
			</div>
			<div class="large-9 column">
				<div class="data-value"><?php echo CHtml::encode($element->witness_name)?></div>
			</div>
		</div>
	<?php }?>
	<div class="row data-row">
		<div class="large-3 column">
			<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('interpreter_required'))?>:</div>
		</div>
		<div class="large-9 column">
			<div class="data-value"><?php echo $element->interpreter_required ? 'Yes' : 'No'?></div>
		</div>
	</div>
	<?php if ($element->interpreter_required) {?>
		<div class="row data-row">
			<div class="large-3 column">
				<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('interpreter_name'))?>:</div>
			</div>
			<div class="large-9 column">
				<div class="data-value"><?php echo CHtml::encode($element->interpreter_name)?></div>
			</div>
		</div>
	<?php }?>
	<?php if ($element->parent_guardian) {?>
		<div class="row data-row">
			<div class="large-3 column">
				<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('parent_guardian'))?>:</div>
			</div>
			<div class="large-9 column">
				<div class="data-value"><?php echo CHtml::encode($element->parent_guardian)?></div>
			</div>
		</div>
	<?php }?>
	<div class="row data-row">
		<div class="large-3 column">
			<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('include_supplementary_consent'))?>:</div>
		</div>
		<div class="large-9 column">
			<div class="data-value"><?php echo $element->include_supplementary_consent ? 'Yes' : 'No'?></div>
		</div>
	</div>
</div>
