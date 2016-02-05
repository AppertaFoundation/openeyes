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
<section class="sub-element">
	<header class="sub-element-header">
		<h3 class="sub-element-title"><?= $element->elementType->name ?></h3>
	</header>

	<div class="sub-element-data">
		<div class="row highlight-container">
			<div class="large-6 column data-value highlight">
				<div class="row data-row">
					<div class="large-5 column">
						<div class="data-label"><?= CHtml::encode($element->getAttributeLabel('application_type_id'))?>:</div>
					</div>
					<div class="large-7 column">
						<div class="data-value"><?= $element->application_type->name?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-5 column">
						<div class="data-label"><?= CHtml::encode($element->getAttributeLabel('concentration_id')) ?>:</div>
					</div>
					<div class="large-7 column">
						<div class="data-value"><?= $element->concentration->value ?></div>
					</div>
				</div>
				<?php if ($element->application_type_id == OphTrOperationnote_Antimetabolite_Application_Type::SPONGE): ?>
				<div class="row data-row">
					<div class="large-5 column">
						<div class="data-label"><?= CHtml::encode($element->getAttributeLabel('duration')) ?>:</div>
					</div>
					<div class="large-7 column">
						<div class="data-value"><?= $element->duration ?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-5 column">
						<div class="data-label"><?= CHtml::encode($element->getAttributeLabel('number')) ?>:</div>
					</div>
					<div class="large-7 column">
						<div class="data-value"><?= $element->number ?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-5 column">
						<div class="data-label"><?= CHtml::encode($element->getAttributeLabel('washed')) ?>:</div>
					</div>
					<div class="large-7 column">
						<div class="data-value"><?= $element->washed ? "Yes" : "No" ?></div>
					</div>
				</div>
				<?php else: ?>
				<div class="row data-row">
					<div class="large-5 column">
						<div class="data-label"><?= CHtml::encode($element->getAttributeLabel('volume_id')) ?>:</div>
					</div>
					<div class="large-7 column">
						<div class="data-value"><?= $element->volume->value ?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-5 column">
						<div class="data-label"><?= CHtml::encode($element->getAttributeLabel('dose')) ?>:</div>
					</div>
					<div class="large-7 column">
						<div class="data-value"><?= $element->dose ?></div>
					</div>
				</div>
				<?php endif ?>
			</div>
		</div>
	</div>
</section>
