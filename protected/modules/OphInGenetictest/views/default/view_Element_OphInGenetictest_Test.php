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

<section class="element">
	<header class="element-header">
		<h3 class="element-title"><?php echo $element->elementType->name?></h3>
	</header>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('gene_id'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->gene ? $element->gene->name : 'None')?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('method_id'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->method ? $element->method->name : 'None')?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('effect_id'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->effect ? $element->effect->name : 'None')?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('exon'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->exon)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('prime_rf'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->prime_rf)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('prime_rr'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->prime_rr)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('base_change'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->base_change)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('amino_acid_change'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->amino_acid_change)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('assay'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->assay)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('homo'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->homo)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('result'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->result)?>
				</div>
			</div>
		</div>
	</div>
	<div class="element-data">
		<div class="row data-row">
			<div class="large-2 column">
				<div class="data-label">
					<?php echo CHtml::encode($element->getAttributeLabel('result_date'))?>
				</div>
			</div>
			<div class="large-10 column end">
				<div class="data-value">
					<?php echo CHtml::encode($element->NHSDate('result_date'))?>
				</div>
			</div>
		</div>
	</div>
</section>
