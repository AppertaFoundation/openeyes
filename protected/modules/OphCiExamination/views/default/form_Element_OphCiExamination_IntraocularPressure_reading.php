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

$base_name = CHtml::modelName($value) . "[{$side}_values][{$index}]";
?>
<tr data-index="<?= $index ?>" data-side="<?= $side ?>" data-index="<?= $index ?>">
  <td>
        <?= CHtml::textField(
            "{$base_name}[reading_time]",
            $time,
            [
                'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                'class' => 'cols-11 js-reading-time'
              ]
        ) ?>
  </td>
  <td style="<?=(!$value_reading_id) ? "display: none" : "" ?>">
        <?php if ($value_reading_id) { ?>
            <?= $value_reading_name ?>
            <?= CHtml::hiddenField("{$base_name}[reading_id]", $value_reading_id) ?>
        <?php } ?>
  </td>
  <td class="scale_values" style="<?= (!$value_qualitative_reading_id) ? "display: none" : ""?>">
        <?php if ($value_qualitative_reading_id) { ?>
            <?= $value_qualitative_reading_name ?>
            <?= CHtml::hiddenField("{$base_name}[qualitative_reading_id]", $value_qualitative_reading_id) ?>
        <?php } ?>
  </td>
    <td>
    <input type="hidden" name="<?= $base_name ?>[instrument_id]"
           id="<?= $base_name ?>[instrument_id]" value="<?= $instrumentId ?>"/>
    <div><?= $instrumentName ?></div>
      </td>
  <td class="cols-2"><?= CHtml::hiddenField("{$base_name}[eye_id]", ($side == 'left') ? Eye::LEFT : Eye::RIGHT) ?><i
        class="oe-i trash"></i></td>
</tr>
