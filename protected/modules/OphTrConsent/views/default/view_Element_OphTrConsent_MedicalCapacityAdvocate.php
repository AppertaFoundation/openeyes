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
    $model_name = CHtml::modelName($element);
?>
<div class="element-data flex-layout flex-top col-gap">
    <div class="cols-6">
        <table class="cols-full last-left">
            <colgroup><col class="cols-8"><col class="cols-4"></colgroup">              <tbody>
                <tr>
                    <td>
                    <?= CHtml::encode($element->getAttributeLabel("instructed_id"))?>
                    </td>
                    <td>
                        <span class="highlighter"><?php echo $element->instructed ? $element->instructed->name : 'None'?></span>
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>

    <div class="cols-5">
        <?php if ($element->outcome_decision) : ?>
            <div class="fade"><?= CHtml::encode($element->getAttributeLabel("outcome_decision"))?></div>
            <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $element->lastModifiedUser->first_name . ' ' . $element->lastModifiedUser->last_name ?>"}'></i>
            <span class="user-comment"><?php echo nl2br(CHtml::encode($element->outcome_decision ?: "-"))?></span>
        <?php endif; ?>
    </div>
</div>
