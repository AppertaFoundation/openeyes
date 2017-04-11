<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2008-2011, Moorfields Eye Hospital NHS Foundation Trust
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/gpl-3.0.html The GNU General Public License V3.0
 */
?>
<table class="oe-create-event-step-through">
    <tbody><tr>
        <td class="step-subspecialties">
            <h3>Subspecialties</h3>

            <ul class="subspecialties-list" id="js-subspecialties-list">
                {{#currentSubspecialties}}
                <li class="step-1 oe-specialty-service" data-id="{{id}}">{{name}}<div class="tag">{{shortName}}</div><span class="service">{{serviceName}}</span></li>
                {{/currentSubspecialties}}
            </ul>

            <div class="change-subspecialty">
                <h6>Add New Subspecialty</h6>
                <select class="new-subspecialty">
                    <option value="">- Please Select -</option>
                    {{#selectableSubspecialties}}
                    <option value="{{id}}">{{name}} ({{shortName}})</option>
                    {{/selectableSubspecialties}}
                </select>

                <h6 style="margin-top:5px">Service</h6>
                <div class="no-subspecialty">Select Subspecialty</div>
                <div class="fixed-service" style="display: none;"></div>
                <select class="select-service" style="display: none;">
                </select>

                <button class="add-subspecialty-btn tiny" style="padding:3px 6px;" id="js-add-subspecialty-btn">+</button>
            </div>

        </td>
        <td class="step-context">
            <h3>Context</h3>
            <ul class="context-list">
                <li class="step-2" id="mirco">Microsoft</li><li class="step-2" id="apple">Apple</li><li class="step-2" id="next">Next</li><li class="step-2" id="xerox">Xerox</li>
            </ul>
        </td>
        <td class="step-event-types" style="visibility: hidden;">
            <h3 class="no-arrow">Select event to add to <?= $subspecialty ?>:</h3>
            <ul class="event-type-list">
                <?php foreach ($eventTypes as $eventType) {
                    if ($subspecialty || $eventType->support_services) {
                        if (file_exists(Yii::getPathOfAlias('application.modules.' . $eventType->class_name . '.assets.img'))) {
                            $assetpath = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.modules.' . $eventType->class_name . '.assets.img') . '/') . '/';
                        } else {
                            $assetpath = '/assets/';
                        }

                        $args = $this->getCreateArgsForEventTypeOprn($eventType);
                        if (call_user_func_array(array($this, 'checkAccess'), $args)) {
                            ?>
                            <li id="<?php echo $eventType->class_name ?>-link" class="oe-event-type step-3">
                                <?php echo CHtml::link('<img src="' . $eventType->getEventIcon() . '" title="' . $eventType->name . ' icon" /> - <strong>' . $eventType->name . '</strong>',
                                    Yii::app()->createUrl($eventType->class_name . '/Default/create') . '?patient_id=' . $patient->id) ?>
                            </li>
                        <?php } else { ?>
                            <li id="<?php echo $eventType->class_name ?>-link" class="oe-event-type step-3 add_event_disabled"
                                title="<?php echo $eventType->disabled ? $eventType->disabled_title : 'You do not have permission to add ' . $eventType->name ?>">
                                <?php echo CHtml::link('<img src="' . $eventType->getEventIcon() . '" title="' . $eventType->name . ' icon" /> - <strong>' . $eventType->name . '</strong>',
                                    '#') ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </ul>

            <div class="back-date-event">
                <label><input id="back-date-event" type="checkbox"> Back Date Event</label>
                <div class="back-date-options">
                    <div style="margin-bottom:10px">
                        <input class="event-date" type="date" placeholder="DD/MM/YYYY">
                    </div>
                    <div>
                        <label>
                            <select style="width:40px;">
                                <option>01</option>
                                <option>02</option>
                                <option>03</option>
                                <option>..</option>
                            </select>
                            <select style="width:40px;">
                                <option>01</option>
                                <option>02</option>
                                <option>03</option>
                                <option>..</option>
                            </select>
                            HH:MM</label>
                    </div>
                </div>
            </div>
        </td>

    </tr>
    </tbody></table>