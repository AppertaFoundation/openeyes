<?php
/**
 * (C) Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
* @var \OEModule\OphCiExamination\models\PostOpDiplopiaRisk $element
*/
?>
<div class="element-data full-width">
    <div class="pro-data-view">

        <div id="js-listview-postopdiplopiarisk-pro" class="listview-pro">
            <ul class="dot-list large">
                <?php if (strlen($element->comments)) { ?>
                    <li><?= $element->comments ?></li>
                <?php } ?>
                <li>At Risk: <?= $element->at_risk ? 'Yes' : 'No'; ?></li>
            </ul>
        </div>
        <div id="js-listview-postopdiplopiarisk-full" class="listview-full column cols-8" style="display: none;">
                <table class="last-left">
                    <colgroup>
                        <col class="cols-8">
                        <col class="cols-2">
                    </colgroup>
                    <thead>
                        <th><?= $element->getAttributeLabel('comments') ?></th>
                        <th><?= $element->getAttributeLabel('at_risk') ?></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= OELinebreakReplacer::replace(CHtml::encode($element->comments)) ?></td>
                            <td><?= $element->at_risk ? 'Yes' : 'No'; ?></td>
                        </tr>
                    </tbody>
                </table>
        </div>
        <div>
            <i class="oe-i small js-listview-expand-btn expand" data-list="postopdiplopiarisk"></i>
        </div>
    </div>
</div>