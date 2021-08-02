<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/** @var \OEModule\OphTrConsent\models\Element_OphTrConsent_CapacityAssessment $element */
?>
<div class="element-data flex-layout flex-top col-gap">
    <div class="cols-6">
        <div class="row large-text">
            <div class="data-label"><?php echo CHtml::encode($element->getAttributeLabel('lackOfCapacityReasons'))?>:</div>
        </div>
        <div class="row">
            <ul class="row-list">
                <?php foreach ($element->lackOfCapacityReasons as $reason) : ?>
                <li><?=CHtml::encode($reason->label)?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
    <div class="cols-5">
        <div class="row">
            <div class="fade">Further details</div>
            <ul class="row-list">
                <?php if ($element->how_judgement_was_made) { ?>
                <li>
                    <div class="small-row"><small class="fade"><?php echo CHtml::encode($element->getAttributeLabel('how_judgement_was_made'))?>:</small></div>
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->usermodified->first_name . ' ' . $element->usermodified->last_name ?>" data-tip='{"type":"basic","tip":"<small>User comment by </small><br />Michael Morgan"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->how_judgement_was_made ?: "-")) ?></span>
                </li>
                <?php } ?>
                <?php if ($element->evidence) { ?>
                <li>
                    <div class="small-row"><small class="fade"><?php echo CHtml::encode($element->getAttributeLabel('evidence'))?>:</small></div>
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->usermodified->first_name . ' ' . $element->usermodified->last_name ?>" data-tooltip-content="<small>User comment by </small><br />Michael Morgan" data-tip='{"type":"basic","tip":"<small>User comment by </small><br />Michael Morgan"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->evidence ?: "-"))?></span>
                </li>
                <?php } ?>
                <?php if ($element->attempts_to_assist) { ?>
                <li>
                    <div class="small-row"><small class="fade"><?php echo CHtml::encode($element->getAttributeLabel('attempts_to_assist'))?>:</small></div>
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->usermodified->first_name . ' ' . $element->usermodified->last_name ?>" data-tooltip-content="<small>User comment by </small><br />Michael Morgan" data-tip='{"type":"basic","tip":"<small>User comment by </small><br />Michael Morgan"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->attempts_to_assist ?: "-"))?></span>
                </li>
                <?php } ?>
                <?php if ($element->basis_of_decision) { ?>
                <li>
                    <div class="small-row"><small class="fade"><?php echo CHtml::encode($element->getAttributeLabel('basis_of_decision'))?>:</small></div>
                    <i class="oe-i comments-who small pad-right js-has-tooltip" data-tooltip-content="<small>User comment by </small><br /><?php echo $element->usermodified->first_name . ' ' . $element->usermodified->last_name ?>" data-tooltip-content="<small>User comment by </small><br />Michael Morgan" data-tip='{"type":"basic","tip":"<small>User comment by </small><br />Michael Morgan"}'></i>
                    <span class="user-comment"><?php echo nl2br(CHtml::encode($element->basis_of_decision ?: "-"))?></span>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>
</div>