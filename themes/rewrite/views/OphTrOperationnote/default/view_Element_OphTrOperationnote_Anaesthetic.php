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
	<h3 class="element-title highlight"><?php echo $element->elementType->name ?></h3>
	<div class="element-data">
		<div class="row">
			<div class="large-2 column">
				<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_type_id'))?></h4>
				<div class="data-value"><?php echo $element->anaesthetic_type->name?></div>
			</div>
			<?php if ($element->anaesthetic_type->name != 'GA') {?>
			<div class="large-2 column">
				<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetist_id'))?></h4>
				<div class="data-value"><?php echo $element->anaesthetist->name?></div>
			</div>
			<div class="large-2 column">
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
			<div class="large-3 column">
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
			<div class="large-3 column">
				<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_delivery_id'))?></h4>
				<div class="data-value">
					<?php echo $element->anaesthetic_delivery->name?>
				</div>
			</div>
			<?php if ($element->getSetting('fife')) {?>
				<div class="large-3 column">
					<h4 class="data-title"><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_witness_id'))?></h4>
					<div class="data-value<?php if (!$element->witness) {?> none<?php }?>">
						<?php echo ($element->witness ? $element->witness->fullName : 'None')?>
					</div>
				</div>
			<?php }?>
		</div>
		<?php }?>
	</div>
</section>