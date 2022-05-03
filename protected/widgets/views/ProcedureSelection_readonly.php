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
<div class="data-group procedure-selection readonly" id="typeProcedure"<?php if ($hidden) {
    ?> style="display: none;"<?php
                                                                       }?>>
    <div class="cols-<?php echo $layoutColumns['label'];?> column">
        <div class="data-label"><?php echo $label?></div>
    </div>
    <div class="cols-4 column">
        <?php $totalDuration = 0; ?>
        <div id="procedureList_<?php echo $identifier?>" class="panel procedures readonly" style=" text-align: left; <?php if (empty($selected_procedures)) {
            ?> display: none;<?php
                               }?>">
            <?php if (!empty($selected_procedures)) {
                foreach ($selected_procedures as $procedure) {?>
                    <div class="data-group procedureItem">
                        <div class="cols-<?php echo (!$durations) ? '12' : '10'; ?> column">
                            <?php $totalDuration += $procedure['default_duration'];
                            echo CHtml::hiddenField('Procedures_' . $identifier . '[]', $procedure['id']);
                            echo '<span>' . $procedure['term'] . '</span>';
                            ?>
                        </div>
                        <?php if ($durations) {?>
                            <div class="cols-2 column">
                                <div class="field-value"><?php echo $procedure['default_duration']?> mins</div>
                            </div>
                        <?php } ?>
                    </div>
                <?php	}
            }?>
        </div>
    </div>
    <div class="cols-6 column">
        <div<?php if (empty($selected_procedures) || !$durations) {
            ?> style="display: none;"<?php
            }?>>
            <table class="plain">
                <tfoot>
                    <tr>
                        <th>Calculated Total Duration:</th>
                        <th id="projected_duration_<?php echo $identifier?>"><?php echo $totalDuration?> mins</th>
                        <th>Estimated Total Duration:</th>
                        <th>
              <input type="text"
                     autocomplete="<?php echo SettingMetadata::model()->getSetting('html_autocomplete')?>"
                     value="<?=\CHtml::encode($total_duration)?>"
                     id="<?php echo $class?>_total_duration_<?php echo $identifier?>"
                     name="<?php echo $class?>[total_duration_<?php echo $identifier?>]"
                     style="width: 60px;" />
            </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
