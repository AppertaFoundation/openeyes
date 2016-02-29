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
		<div class="large-6 column">
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('conjunctival_flap_type_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->conjunctival_flap_type->name?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('stay_suture'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo CHtml::encode($element->stay_suture ? 'Yes' : 'No')?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('site_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo CHtml::encode($element->site->name)?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('size_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->size->name?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('sclerostomy_type_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->sclerostomy_type->name?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('viscoelastic_type_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->viscoelastic_type->name?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('viscoelastic_removed'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->viscoelastic_removed ? 'Yes' : 'No'?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('viscoelastic_flow_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->viscoelastic_flow->name?>
					</div>
				</div>
			</div>
			<div class="data-row">
				<h4>Details</h4>
				<div class="details pronounced">
					<ul>
					<?php foreach (explode(chr(10),CHtml::encode($element->report)) as $line) {?>
						<li><?php echo $line?></li>
					<?php }?>
					</ul>
				</div>
			</div>

			<div class="row data-row">
				<div class="large-6 column text-right">
					<div class="data-label">
						Difficulties
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php if (!$element->difficulties) {?>
							None
						<?php } else {?>
							<?php foreach ($element->difficulties as $difficulty) {?>
								<?php if ($difficulty->name == 'Other') {?>
									<?php echo str_replace("\n",'<br/>',$element->difficulty_other)?>
								<?php }else{?>
									<?php echo $difficulty->name?><br>
								<?php }?>
							<?php }?>
						<?php }?>
					</div>
				</div>
			</div>

			<div class="data-row">
				<h4>Complications</h4>
				<div class="details">
					<?php if (!$element->complications) {?>
						<div class="data-value">None</div>
					<?php } else {?>
						<ul>
						<?php foreach ($element->complications as $complication) {?>
							<li>
								<?php if ($complication->name == 'Other') {?>
									<?php echo $element->complication_other?>
								<?php }else{?>
									<?php echo $complication->name?>
								<?php }?>
							</li>
						<?php }?>
						</ul>
					<?php }?>
				</div>
			</div>
		</div>
		<div class="large-6 column">
			<div class="data-row">
				<div class="details">
					<?php
					$this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
						'idSuffix'=>'Trabeculectomy',
						'side'=>$element->eye->getShortName(),
						'mode'=>'view',
						'width'=>250,
						'height'=>250,
						'scale'=>0.72,
						'model'=>$element,
						'attribute'=>'eyedraw',
					))?>
				</div>
			</div>
		</div>
	</div>
</section>
