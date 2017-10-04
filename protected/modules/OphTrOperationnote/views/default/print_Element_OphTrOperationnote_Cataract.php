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

<section class="element <?php echo $element->elementType->class_name?> row">
	<h3 class="element-title"><?php echo $element->elementType->name ?></h3>
	<div class="row">
		<div class="large-6 column">
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('incision_site_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->incision_site->name?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('length'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo CHtml::encode($element->length)?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('meridian'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo CHtml::encode($element->meridian)?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column text-right">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('incision_type_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->incision_type->name?>
					</div>
				</div>
			</div>

			<div class="data-row">
				<h4>Details</h4>
				<div class="details pronounced">
					<ul>
					<?php foreach (explode(chr(10), CHtml::encode($element->report)) as $line) {?>
						<li><?php echo $line?></li>
					<?php }?>
					</ul>
				</div>
			</div>

			<div class="row data-row">
				<div class="large-6 column text-right">
					<div class="data-label">
						Devices Used:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php if (!$element->operative_devices) {?>
							None
						<?php } else {?>
							<?php foreach ($element->operative_devices as $device) {?>
								<?php echo $device->name?><br>
							<?php }?>
						<?php }?>
					</div>
				</div>
			</div>

			<div class="data-row">
				<h4>Per Operative Complications</h4>
				<div class="details">
					<?php if (!$element->complications && !$element->complication_notes) {?>
						<div class="data-value">None</div>
					<?php } else {?>
						<ul>
						<?php foreach ($element->complications as $complication) {?>
							<li><?php echo $complication->name?></li>
						<?php }?>
						</ul>
						<?php echo CHtml::encode($element->complication_notes)?>
					<?php }?>
				</div>
			</div>
		</div>
		<div class="large-6 column">
			<div class="data-row">
				<div class="details">
					<?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                        'idSuffix' => 'Cataract',
                        'side' => $element->eye->getShortName(),
                        'mode' => 'view',
                        'width' => 150,
                        'height' => 150,
                        'model' => $element,
                        'attribute' => 'eyedraw',
                    ));
                    ?>
					<?php
                    $this->widget('application.modules.eyedraw.OEEyeDrawWidget', array(
                        'idSuffix' => 'Position',
                        'side' => $element->eye->getShortName(),
                        'mode' => 'view',
                        'width' => 135,
                        'height' => 135,
                        'model' => $element,
                        'attribute' => 'eyedraw2',
                    ));
                    ?>
					<?php echo CHtml::encode($element->report2)?>
				</div>
			</div>

			<div class="row">
				<div class="large-6 column">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('iol_type_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php
						if(isset(Yii::app()->modules["OphInBiometry"])){
							if($element->iol_type_id){
								$iol_type = OphInBiometry_LensType_Lens::model()->findByPk($element->iol_type_id);
								echo $iol_type->name;
							}else{
								echo 'None';
							}
						}else{
							echo $element->iol_type ? $element->iol_type->name : 'None';
						}
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('iol_power'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo CHtml::encode($element->iol_power)?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('predicted_refraction'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->predicted_refraction?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('iol_position_id'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->iol_position->name?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="large-6 column">
					<div class="data-label">
						<?php echo CHtml::encode($element->getAttributeLabel('pcr_risk'))?>:
					</div>
				</div>
				<div class="large-6 column">
					<div class="data-value">
						<?php echo $element->pcr_risk?>
					</div>
				</div>
			</div>

		</div>
	</div>
</section>
