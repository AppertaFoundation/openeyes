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
			<?php if ($element->hasRight()) {
				if (!$element->right_procedures) {?>
					None
				<?php } else {?>
					<ul class="data-value">
						<?php foreach ($element->right_procedures as $proc) {?>
							<li><?php echo $proc->term?></li>
						<?php }?>
					</ul>
				<?php }?>
			<?php }else{?>
				<div class="data-value">Not recorded</div>
			<?php }?>
		</div>
		<div class="element-eye left-eye column">
			<?php if ($element->hasLeft()) {
				if (!$element->left_procedures) {?>
					None
				<?php } else {?>
					<ul class="data-value">
						<?php foreach ($element->left_procedures as $proc) {?>
							<li><?php echo $proc->term?></li>
						<?php }?>
					</ul>
				<?php }?>
			<?php }else{?>
				<div class="data-value">Not recorded</div>
			<?php }?>
		</div>
	</div>
	<?php $this->renderChildDefaultElements($element, $this->action->id, $form, $data)?>
</section>
