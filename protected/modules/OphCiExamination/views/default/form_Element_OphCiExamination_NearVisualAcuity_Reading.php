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
<?php
if (!isset($selected_data) && isset($reading) && isset($reading->value) && isset($reading->method_id)) {
    $selected_data = array(
        'reading_unit_id' => $reading->unit_id,
        'reading_value' => (int)$reading->value,
        'reading_display' => $values[$reading->value],
        'method_id' => (int)$reading->method_id,
        'method_display' => $methods[$reading->method_id],
        'tooltip' => $val_options[$reading->value]['data-tooltip']
    );
}

?>
<?php if (isset($selected_data['reading_display'])) { ?>
<tr class="nearvisualAcuityReading near-visual-acuity-reading js-reading-record" data-key="<?php echo $key?>" data-test="near-visual-acuity-reading">
    <td class="cols-3">
        <?php if (isset($reading) && $reading->id) { ?>
            <?=\CHtml::hiddenField($name_stub .'['. $key.'][id]', @$reading->id, ['data-test' => 'near-visual-acuity-reading-id'])?>
        <?php } ?>
      <?=\CHtml::hiddenField($name_stub .'['. $key.'][unit_id]', @$selected_data['reading_unit_id'])?>
      <?=\CHtml::hiddenField($name_stub .'['. $key.'][value]', @$selected_data['reading_value'], array('class' => 'va-selector'))?>
      <?= @$selected_data['reading_display']?>
    </td>
  <td class="cols-1">
    <i class="oe-i info small pad js-has-tooltip va-info-icon" data-tooltip='<?= @$selected_data['tooltip'] ?>' data-tooltip-content="Please select a VA value"></i>
  </td>
    <td>
      <?=\CHtml::hiddenField($name_stub .'['. $key.'][method_id]', @$selected_data['method_id'], array('class' => 'method_id'))?>
      <?= @$selected_data['method_display'] ?>
    </td>
    <td class="cols-2 readingActions">
    <i class="oe-i trash removeReading"></i>
  </td>
</tr>
<?php } ?>
