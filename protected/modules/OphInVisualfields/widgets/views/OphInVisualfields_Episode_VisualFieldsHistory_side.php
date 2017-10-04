<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
$field = $element->{"{$side}_field"};
?>
<div class="element-eye <?= $side ?>-eye column">
	<?php if ($field): ?>
		<div class="field-row row">
			<div class="large-12 column"><?= ucfirst($side) ?> Eye</div>
		</div>
		<div class="field-row row">
			<div class="large-12 column"><a class="OphInVisualfields_field_image" data-image-id="<?= $field->image_id ?>" href="#"><img
						src="/file/view/<?= $field->cropped_image_id ?>/400/img.gif"></a></div>
		</div>
		<div class="field-row row">
			<div class="large-6 column"><p>Date</p></div>
			<div class="large-6 column"><p><?=date(Helper::NHS_DATE_FORMAT.' H:i:s', strtotime($field->study_datetime)) ?></p></div>
		</div>
		<div class="field-row row">
			<div class="large-6 column"><p>Strategy</p></div>
			<div class="large-6 column"><p><?= CHtml::encode($field->strategy->name) ?></p></div>
		</div>
		<div class="field-row row">
			<div class="large-6 column"><p>Test Name</p></div>
			<div class="large-6 column"><p><?= CHtml::encode($field->pattern->name) ?></p></div>
		</div>
	<?php else: ?>
		<p>No image for <?= $side ?> eye.</p>
	<?php endif ?>
</div>
