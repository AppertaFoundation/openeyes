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
<tr class="intraocularPressureReading" data-key="<?php echo $key ?>">
    <td>
        <?php if (isset($reading) && $reading->id) { ?>
        <input type="hidden" name="intraocularpressure_reading[<?php echo $key ?>][id]" value="<?php echo $reading->id?>" />
        <?php } ?>
        <input type="hidden" name="intraocularpressure_reading[<?php echo $key ?>][side]" value="<?php echo $side ?>" />
        <?=\CHtml::textField('intraocularpressure_reading[' . $key . '][measurement_timestamp]', isset($reading) ? substr($reading->measurement_timestamp, 0, 5) : date('H:i'), array('autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'), 'class' => 'small'))?>
    </td>
    <td>
        <?=
         \CHtml::textField(
             'intraocularpressure_reading[' . $key . '][value]',
             @$reading->value,
             [
                 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                 'class' => 'small',
                 'data-test' => 'reading-value-input'
             ]
         )
            ?>
    </td>
    <td class="readingActions">
        <?php if (!isset($no_remove) || !$no_remove) {?>
            <a class="removeReading" href="#">Remove</a>
        <?php }?>
    </td>
</tr>
