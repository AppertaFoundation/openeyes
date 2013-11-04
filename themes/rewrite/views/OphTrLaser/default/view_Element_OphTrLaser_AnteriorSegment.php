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
?>
<section class="element">
	<header class="element-header">
		<h3 class="element-title"><?php echo $element->elementType->name?></h3>
	</header>
	<div class="element-data element-eyes row">
		<div class="element-eye right-eye column">
			<div class="eyedraw-image">
				<?php if ($element->hasRight()) {
					$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
						'idSuffix' => 'right_'.$element->elementType->id,
						'side' => 'R',
						'mode' => 'view',
						'width' => 200,
						'height' => 200,
						'model' => $element,
						'attribute' => 'right_eyedraw',
					));
				} else {?>
					<div class="data-value">Not recorded</div>
				<?php }?>
			</div>
		</div>
		<div class="element-eye left-eye column">
			<div class="eyedraw-image">
				<?php if ($element->hasLeft()) {
					$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
						'idSuffix' => 'left_'.$element->elementType->id,
						'side' => 'R',
						'mode' => 'view',
						'width' => 200,
						'height' => 200,
						'model' => $element,
						'attribute' => 'left_eyedraw',
					));
				} else {?>
					<div class="data-value">Not recorded</div>
				<?php }?>
			</div>
		</div>
	</div>
</section>
