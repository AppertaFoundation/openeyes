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
<section class="element <?php echo $element->elementType->class_name?>">
	<h3 class="element-title"><?php echo $element->elementType->name ?></h3>
	<div class="details">
		<div class="element-data">
			<?php
                $columns = 6;
                if ($element->hasAnaestheticType('GA') && count($element->anaesthetic_type) == 1) {
                    $columns -= 2;
                }
                if (!$element->getSetting('fife')) {
                    $columns -= 1;
                }
            ?>
			<div class="row data-row columns-<?php echo $columns;?>">
				<div class="column">
					<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_type_id'))?></h4>
					<div class="data-value"><?= $element->getAnaestheticTypeDisplay() ?></div>
				</div>
                <?php if ( count($element->anaesthetic_type) > 1 || ( count($element->anaesthetic_type) == 1 && !$element->hasAnaestheticType("GA") && !$element->hasAnaestheticType("NoA")) ) {?>
                    <div class="column">
                        <h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_delivery_id'))?></h4>
                        <div class="data-value <?php if (!$element->anaesthetic_delivery) {?> none<?php }?>">
                            <?php
                            $text = '';
                            foreach($element->anaesthetic_delivery as $anaesthetic_delivery){
                                if(!empty($text)){ $text .= ', '; }
                                $text .= $anaesthetic_delivery->name;
                            }

                            echo $text ? $text : 'None';
                            ?>
                        </div>
                    </div>
                    <div class="column">
						<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetist_id'))?></h4>
                        <div class="data-value <?php if (!$element->anaesthetist) {?> none<?php }?>"><?php echo $element->anaesthetist ? $element->anaesthetist->name : 'None'?></div>
					</div>
					<div class="column">
						<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('agents'))?></h4>
						<div class="data-value <?php if (!$element->anaesthetic_agents) {?> none<?php }?>">
							<?php if (!$element->anaesthetic_agents) {?>
								None
							<?php } else {?>
								<?php foreach ($element->anaesthetic_agents as $agent) {?>
									<?php echo $agent->name?><br/>
								<?php }?>
							<?php }?>
						</div>
					</div>
					<div class="column">
						<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('complications'))?></h4>
						<div class="data-value">
							<?php if (!$element->anaesthetic_complications) {?>
								None
							<?php } else {?>
								<?php foreach ($element->anaesthetic_complications as $complication) {?>
									<?php echo $complication->name?><br/>
								<?php }?>
							<?php }?>
						</div>
					</div>
					<?php if ($element->getSetting('fife')) {?>
						<div class="column">
							<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_witness_id'))?></h4>
							<div class="data-value<?php if (!$element->witness) {?> none<?php }?>">
								<?php echo $element->witness ? $element->witness->fullName : 'None'?>
							</div>
						</div>
					<?php }?>
				<?php }?>
			</div>
			<div class="row data-row">
				<div class="large-12 column">
					<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_comment'))?></h4>
					<div class="data-value"><?php echo  Yii::app()->format->Ntext($element->anaesthetic_comment)?></div>
				</div>
			</div>
		</div>
	</div>
</section>