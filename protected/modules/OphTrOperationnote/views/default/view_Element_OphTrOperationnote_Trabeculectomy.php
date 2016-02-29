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
		<h3 class="sub-element-title"><?php echo $element->elementType->name ?></h3>
	</header>

	<div class="sub-element-data">
		<div class="row highlight-container">
			<div class="large-6 column data-value highlight">
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('conjunctival_flap_type_id'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->conjunctival_flap_type->name?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('stay_suture'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->stay_suture ? 'Yes' : 'No'?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('site_id'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->site ? $element->site->name : 'None'?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('size_id'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->size ? $element->size->name : 'None'?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('sclerostomy_type_id'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->sclerostomy_type ? $element->sclerostomy_type->name : 'None'?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('viscoelastic_type_id'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->viscoelastic_type ? $element->viscoelastic_type->name : 'None'?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('viscoelastic_removed'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->viscoelastic_removed ? 'Yes' : 'No'?></div>
					</div>
				</div>
				<div class="row data-row">
					<div class="large-4 column">
						<div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('viscoelastic_flow_id'))?>:</div>
					</div>
					<div class="large-8 column">
						<div class="data-value"><?php echo $element->viscoelastic_flow ? $element->viscoelastic_flow->name : 'None'?></div>
					</div>
				</div>
			</div>
			<div class="large-6 column">
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
						'idSuffix'=>'Trabeculectomy',
					));
				?>
			</div>
		</div>
		<div class="row data-row">
			<div class="large-4 column">
				<h3 class="data-title">Trabeculectomy report</h3>
				<div class="data-value highlight">
					<?php foreach (explode(chr(10),CHtml::encode($element->report)) as $line) {?>
						<?php echo $line?><br/>
					<?php }?>
				</div>
			</div>
			<div class="large-4 column">
				<h3 class="data-title">Difficulties</h3>
				<div class="data-value highlight">
					<?php if (!$element->difficulties) {?>
						None
					<?php } else {?>
						<?php foreach ($element->difficulties as $difficulty) {?>
							<?php if ($difficulty->name == 'Other') {?>
								<?php echo str_replace("\n",'<br/>',$element->difficulty_other)?>
							<?php }else{?>
								<?php echo $difficulty->name?><br/>
							<?php }?>
						<?php }?>
					<?php }?>
				</div>
			</div>
			<div class="large-4 column">
				<h3 class="data-title">Complications</h3>
				<div class="data-value highlight">
					<?php if (!$element->complications) {?>
						None
					<?php } else {?>
						<?php foreach ($element->complications as $complication) {?>
							<?php if ($complication->name == 'Other') {?>
								<?php echo str_replace("\n",'<br/>',$element->complication_other)?>
							<?php }else{?>
								<?php echo $complication->name?><br/>
							<?php }?>
						<?php }?>
					<?php }?>
				</div>
			</div>
		</div>
	</div>
</section>
