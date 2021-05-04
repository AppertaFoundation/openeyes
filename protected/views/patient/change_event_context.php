<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2011-2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/**
 * @var EventType[] $eventTypes
 */
?>

<script type="text/html" id="change-subspecialty-template">
    <li class="oe-specialty-service {{classes}}"
        data-id="{{id}}"
        data-subspecialty-id="{{subspecialtyId}}"
        data-service-id="{{serviceId}}">
        {{name}}
        <div class="tag">{{shortName}}</div>
            <span class="service">{{serviceName}}</span>
            {{#multiple_services}}
            <select class="change-service pro-theme cols-full" style="display: none;">
                <option value="{{serviceId}}">{{serviceName}}</option>
                {{#services_available}}
                <option value="{{id}}">{{name}} ({{shortName}})</option>
                {{/services_available}}
            </select>
            {{/multiple_services}}
            {{^id}}
            <div class="change-new-specialty"></div>
            {{/id}}
    </li>
</script>
<script type="text/html" id="new-change-subspecialty-template">
    <div class="oe-specialty-service new-added-subspecialty-service selected {{classes}}"
         data-subspecialty-id="{{subspecialtyId}}"
         data-service-id="{{serviceId}}">
        {{name}}
        <span class="tag">{{shortName}}</span>
        <span class="service">{{serviceName}}</span>
        <div class="change-new-specialty"></div>
    </div>
</script>
<script type="text/html" id="change-context-template">
    <table class="oe-create-event-step-through">
        <tbody>
        <tr>
            <td class="step-subspecialties">
                <h3>Subspecialties</h3>
                <ul class="subspecialties-list" id="js-subspecialties-list">
                    {{#currentSubspecialties}}
                    {{>subspecialty}}
                    {{/currentSubspecialties}}
                </ul>
                <div class="change-subspecialty">
                    <h6>Add New Subspecialty</h6>
                    <select class="new-subspecialty">
                        <option value="">Select</option>
                        {{#selectableSubspecialties}}
                        <option value="{{id}}">{{name}} ({{shortName}})</option>
                        {{/selectableSubspecialties}}
                    </select>

                    <h6 style="margin-top:5px"><?= Firm::serviceLabel() ?></h6>
                    <div class="no-subspecialty"><h6>Select Subspecialty</h6></div>
                    <div class="fixed-service" style="display: none;"></div>
                    <select class="select-service" style="display: none;">
                    </select>

                    <button class="add-subspecialty-btn button hint green" id="js-add-subspecialty-btn">
                        <i class="oe-i plus"></i>
                    </button>
                </div>
            </td>
            <td class="step-context oe-create-event-step-through " style="visibility: hidden;">
                <h3><?= Firm::contextLabel() ?></h3>
                <ul class="context-list"></ul>
            </td>
            <td class="step-event-types" style="visibility: hidden;">
                <h3>Change last workflow step</h3>
                <button class="button green js-confirm-context-change" type="button" style="display: none;">Confirm
                    change
                </button>
                <ul id="event-type-list" class="event-type-list">
                </ul>

                <div class="back-date-event" style="display: none;">
                    <!-- TODO: implement back dated event changes -->
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
                                HH:MM
                            </label>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</script>
<script type="text/javascript">
    $(document).ready(function () {
        var changeContextDialog;

        $(document).on('click', '<?= $button_selector ?>', function (event) {
            event.preventDefault();
            changeContextDialog = new OpenEyes.UI.Dialog.NewEvent({
                mode: 'ChangeContext',
                title: 'Change event Context',
                selector: '#change-context-template',
                subspecialtyTemplateSelector: '#change-subspecialty-template',
                newSubspecialtyTemplateSelector: '#new-change-subspecialty-template',
                class: 'oe-change-event-context-popup',
                viewSubspecialtyId: <?= $view_subspecialty ? $view_subspecialty->id : 'undefined' ?>,
                patientId: <?= $patient_id ?>,
                userSubspecialtyId: '<?= $context_firm->getSubspecialtyID() ?: 'SS'; ?>',
                userContext: <?= CJSON::encode(NewEventDialogHelper::structureFirm($context_firm)) ?>,
                currentSubspecialties: <?= CJSON::encode(NewEventDialogHelper::structureEpisodes($episodes)) ?>,
                subspecialties: <?= CJSON::encode(NewEventDialogHelper::structureAllSubspecialties()) ?>,
                newSubspecialtyTemplateSelector: '#new-change-subspecialty-template',
                showSteps: (OE_module_name === 'OphCiExamination'),
                workflowSteps: <?= CJSON::encode($workflowSteps) ?>,
                currentStep: <?= CJSON::encode($currentStep) ?>,
                currentFirm: <?= $currentFirm ?>,
                eventType: "<?= $event_types ?>",
            }).open();

            //scroll view to selected service
            const selected_service = document.querySelector('.oe-specialty-service.selected');
            if (selected_service) {
                selected_service.scrollIntoView();
            }
        });
    });
</script>