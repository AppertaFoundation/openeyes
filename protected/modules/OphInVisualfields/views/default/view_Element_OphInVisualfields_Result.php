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
<div class="element-data">
	<div class="row data-row">
		<div class="large-2 column data-label"><?= CHtml::encode($element->getAttributeLabel('assessment_id')) ?></div>
		<div class="large-10 column end"><div class="data-value"><?php if (!$element->assessment) {?>
					None
				<?php } else {?>
					<?php foreach ($element->assessment as $item) {
						echo $item->ophinvisualfields_result_assessment->name?><br/>
					<?php }?>
				<?php }?>
			</div></div>
	</div>
	<?php if ($element->hasMultiSelectValue('assessment','Other')) { ?>
		<div class="row data-row">
			<div class="large-2 column data-label"><?= CHtml::encode($element->getAttributeLabel('other')) ?></div>
			<div class="large-10 column data-value"><?= $element->textWithLineBreaks('other') ?></div>
		</div>
	<?php } ?>
</div>
