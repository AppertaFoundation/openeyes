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
<div class="eyedraw-image column fixed">
	<?php
	$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
		'idSuffix' => $side.'_'.$element->elementType->id.'_'.$element->id,
		'side' => ($side == 'right') ? 'R' : 'L',
		'mode' => 'view',
		'width' => 200,
		'height' => 200,
		'model' => $element,
		'attribute' => $side.'_eyedraw',
	))?>
</div>
<div class="eyedraw-value column fluid">
	<?php if ($description = $element->{$side.'_description'}) { ?>
		<div class="data-row description">
			<div class="data-value">
				<?php echo $description ?>
			</div>
		</div>
	<?php } ?>
	<div class="row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_pupil_id')?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_pupil'}->name?></div>
		</div>
	</div>
	<div class="row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_nuclear_id')?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_nuclear'}->name?></div>
		</div>
	</div>
	<div class="row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_cortical_id')?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_cortical'}->name?></div>
		</div>
	</div>
	<div class="row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_pxe')?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_pxe'} ? 'Yes' : 'No'?></div>
		</div>
	</div>
	<div class="row">
		<div class="large-4 column">
			<div class="data-label"><?php echo $element->getAttributeLabel($side.'_phako')?>:</div>
		</div>
		<div class="large-8 column">
			<div class="data-value"><?php echo $element->{$side.'_phako'} ? 'Yes' : 'No'?></div>
		</div>
	</div>
</div>