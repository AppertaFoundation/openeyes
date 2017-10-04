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

<section class="element">
	<div class="element-data">
		<div class="row data-row">
			<div class="large-4 column">
				<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('postop_instructions'))?></h4>
				<div class="data-value<?php if (!$element->postop_instructions) {?> none<?php }?>"><?php echo CHtml::encode($element->postop_instructions) ? Yii::app()->format->Ntext($element->postop_instructions) : 'None'?></div>
			</div>
			<div class="large-8 column end">
			<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('comments'))?></h4>
				<div class="data-value<?php if (!$element->comments) {?> none<?php }?>"><?php echo CHtml::encode($element->comments) ? Yii::app()->format->Ntext($element->comments) : 'None'?></div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-4 column">
				<h4 class="data-title">Site</h4>
				<div class="data-value<?php if (!$site = $this->findBookingSite()) {?> none<?php }?>"><?php echo $site ? $site->name : 'N/A (Emergency)'?></div>
			</div>
		</div>
	</div>
</section>
