<?php
/**
 * (C) OpenEyes Foundation, 2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */

$base_name = CHtml::modelName($value) . "[{$side}_values][{$index}]";
?>
<tr data-index="<?= $index ?>" data-side="<?php echo $side?>" data-index="<?php echo $index?>">
	<td><?= CHtml::textField("{$base_name}[reading_time]", $time, array('autocomplete' => Yii::app()->params['html_autocomplete'])) ?>
	<td<?php if ($value->instrument && $value->instrument->scale) {
    ?> style="display: none"<?php 
}?>>
		<?= $form->dropDownList($value, 'reading_id', 'OEModule\OphCiExamination\models\OphCiExamination_IntraocularPressure_Reading', array("nowrapper" => true, 'data-base-name' => $base_name, "name" => "{$base_name}[reading_id]", 'prompt' => '--')) ?>
	</td>
	<td class="scale_values"<?php if (!$value->instrument || !$value->instrument->scale) {
    ?> style="display: none"<?php 
}?>>
		<?php if ($value->instrument && $value->instrument->scale) {
    echo $this->renderPartial('_qualitative_scale', array('value' => $value, 'side' => $side, 'index' => $index, 'scale' => $value->instrument->scale));
}?>
	</td>
	<?php if ($element->getSetting('show_instruments')): ?>
		<td><?= $form->dropDownList($value, 'instrument_id', 'OEModule\OphCiExamination\models\OphCiExamination_Instrument', array("nowrapper" => true, 'class' => 'IOPinstrument', "name" => "{$base_name}[instrument_id]")) ?></td>
	<?php endif ?>
	<td><?= CHtml::hiddenField("{$base_name}[eye_id]", ($side == 'left') ? Eye::LEFT : Eye::RIGHT) ?><a class="delete" href="#">Remove</a></td>
</tr>
