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

<div class="cols2">
	<div class="left">
		<h4><?php echo $element->elementType->name ?></h4>
		<div class="eventHighlight">
			<h4><?php echo $element->anaesthetic_type->name?></h4>
		</div>
	</div>
	<?php if ($element->anaesthetic_type->name != 'GA') {?>
		<div class="right">
			<h4><?php echo CHtml::encode($element->getAttributeLabel('anaesthetist_id'))?></h4>
			<div class="eventHighlight">
				<h4><?php echo $element->anaesthetist->name?></h4>
			</div>
		</div>
		<div class="left">
			<h4><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_delivery_id'))?></h4>
			<div class="eventHighlight">
				<h4><?php echo $element->anaesthetic_delivery->name?></h4>
			</div>
		</div>
		<?php if ($element->anaesthetic_comment) {?>
			<div class="right">
				<h4><?php echo CHtml::encode($element->getAttributeLabel('anaesthetic_comment'))?></h4>
				<div class="eventHighlight">
					<h4><?php echo $element->anaesthetic_comment?></h4>
				</div>
			</div>
		<?php }?>
	<?php }?>
</div>
