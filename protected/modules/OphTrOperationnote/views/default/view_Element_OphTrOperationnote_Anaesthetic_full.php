<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2018, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
$anaesthetic_agents = implode('<br />', array_map(function ($agent) {
    return $agent->name;
}, $element->anaesthetic_agents));
$anaesthetic_deliveries = implode(', ', array_map(function ($delivery) {
    return $delivery->name;
}, $element->anaesthetic_delivery));
$anaesthetic_complications = implode('<br />', array_map(function ($complication) {
    return $complication->name;
}, $element->anaesthetic_complications));
?>

<div class="cols-11" id="js-listview-anaesthetic-pro" style="<?= $this->action->id === 'view' ? '' : 'display: none;'?>">
    <ul class="dot-list large">
        <li><?= $element->getAnaestheticTypeDisplay() ?></li>
        <li><?php echo $anaesthetic_deliveries ?: 'None' ?></li>
        <li><?= \CHtml::encode($element->getAttributeLabel('agents')) ?>:
            <span <?php if (!$element->anaesthetic_agents){ ?>class="none"<?php } ?>>
                   <?php echo $anaesthetic_agents ?>
                </span>
        </li>
        <li>
            <?php echo $element->anaesthetist ? $element->anaesthetist->name : 'None' ?>
        </li>
        <li>
            <?= \CHtml::encode($element->getAttributeLabel('complications')) ?>:
            <?php echo $anaesthetic_complications ?: 'None' ?>
        </li>
    </ul>
</div>

<div class="col-6" id="js-listview-anaesthetic-full" style="<?= $this->action->id === 'view' ? 'display: none;' : ''?>">
    <table class="last-left large">
        <colgroup>
            <col class="cols-fifth" span="5">
        </colgroup>
        <thead>
        <tr>
            <th>Type</th>
            <th>Delivery</th>
            <th><?= \CHtml::encode($element->getAttributeLabel('agents')) ?></th>
            <th><?= \CHtml::encode($element->getAttributeLabel('anaesthetist_id')) ?></th>
            <th><?= \CHtml::encode($element->getAttributeLabel('complications')) ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?= $element->getAnaestheticTypeDisplay() ?></td>
            <td><?php echo $anaesthetic_deliveries ?: 'None' ?></td>
            <td>
                  <span <?php if (!$element->anaesthetic_agents){ ?>class="none"<?php } ?>>
                   <?php echo $anaesthetic_agents ?: 'None' ?>
                  </span>
            </td>
            <td><?php echo $element->anaesthetist ? $element->anaesthetist->name : 'None' ?>
            </td>
            <td>
                <?php echo $anaesthetic_complications ?: 'None' ?>
            </td>
        </tr>
        </tbody>
    </table>

    <h4 class="data-label"><?= \CHtml::encode($element->getAttributeLabel('anaesthetic_comment')) ?></h4>
    <div
            class="data-value<?php if (!$element->anaesthetic_comment) { ?> none<?php } ?>"><?= \CHtml::encode($element->anaesthetic_comment) ? Yii::app()->format->Ntext($element->anaesthetic_comment) : 'None' ?>
    </div>
</div>
</div>
<?php if ($element->getSetting('fife')) { ?>
    <div class="cols-3 column">
        <h4 class="data-label"><?= \CHtml::encode($element->getAttributeLabel('anaesthetic_witness_id')) ?></h4>
        <div class="data-value<?php if (!$element->witness) { ?> none<?php } ?>">
            <?php echo $element->witness ? $element->witness->fullName : 'None' ?>
        </div>
    </div>
<?php } ?>
<div>
    <i class="oe-i small js-listview-expand-btn <?= $this->action->id === 'view' ? 'expand' : 'collapse'?>" data-list="anaesthetic"></i>
</div>