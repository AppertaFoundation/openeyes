<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2017
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2017, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<?php
/**
 * @var EventType[] $eventTypes
 */
?>

<?php Yii::app()->assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.js') ?>
<?php Yii::app()->assetManager->registerScriptFile('js/OpenEyes.UI.Dialog.NewEvent.js', null, -10) ?>

<script type="text/html" id="subspecialty-template">
  <li class="oe-specialty-service {{classes}}"
      data-id="{{id}}"
      data-subspecialty-id="{{subspecialtyId}}"
      data-service-id="{{serviceId}}"
  >{{name}}
    <div class="tag">{{shortName}}</div>
    <span class="service">{{serviceName}}</span>
    {{^id}}
    <div class="change-new-specialty"></div>
    {{/id}}
  </li>
</script>
<script type="text/html" id="new-subspecialty-template">
  <div class="oe-specialty-service new-added-subspecialty-service selected {{classes}}"
       data-subspecialty-id="{{subspecialtyId}}"
       data-service-id="{{serviceId}}"
  >{{name}}
    <span class="tag">{{shortName}}</span>
    <span class="service">{{serviceName}}</span>
    <div class="change-new-specialty"></div>
  </div>
</script>
<script type="text/html" id="add-new-event-template">
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
          <select class="select-service cols-10" style="display: none;">
          </select>

          <button class="add-subspecialty-btn button hint green" id="js-add-subspecialty-btn"><i class="oe-i plus"></i></button>
        </div>
      </td>
      <td class="step-context oe-create-event-step-through " style="visibility: hidden;">
        <h3><?= Firm::contextLabel() ?></h3>
        <ul class="context-list">
        </ul>
      </td>
      <td class="step-event-types" style="visibility: hidden;">
        <h3>Select New Event</h3>
        <ul id="event-type-list" class="event-type-list">
            <?php foreach ($event_types as $eventType) {
                $args = $this->getCreateArgsForEventTypeOprn($eventType, array('episode'));
                if (call_user_func_array(array($this, 'checkAccess'), $args)) {
                    ?>
                  <li id="<?php echo $eventType->class_name ?>-link" class="oe-event-type step-3"
                      data-eventType-id="<?= $eventType->id ?>"
                      data-support-services="<?= $eventType->support_services ?>">
                    <?= $eventType->getEventIcon() ?><?= $eventType->name ?>
                  </li>
                <?php } else { ?>
                  <li id="<?php echo $eventType->class_name ?>-link" class="oe-event-type step-3 add_event_disabled"
                      title="<?php echo $eventType->disabled ? $eventType->disabled_title : 'You do not have permission to add ' . $eventType->name ?>">
                      <?= $eventType->getEventIcon() ?><?= $eventType->name ?>
                  </li>
                <?php } ?>
            <?php } ?>
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
                HH:MM</label>
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
        var newEventDialog;

        $(document).on('click', '<?= $button_selector ?>', function () {
            newEventDialog = new OpenEyes.UI.Dialog.NewEvent({
                id: 'add-new-event-dialog',
                    class: 'oe-create-event-popup',
                viewSubspecialtyId: <?= @$view_subspecialty ? $view_subspecialty->id : 'undefined' ?>,
                patientId: <?= $patient_id ?>,
                userSubspecialtyId: '<?= $context_firm->getSubspecialtyID() ?: 'SS'; ?>',
                userContext: <?= CJSON::encode(NewEventDialogHelper::structureFirm($context_firm)) ?>,
                currentSubspecialties: <?= CJSON::encode(NewEventDialogHelper::structureEpisodes($episodes)) ?>,
                subspecialties: <?= CJSON::encode(NewEventDialogHelper::structureAllSubspecialties()) ?>
            }).open();

            //scroll view to selected service
            const selected_service = document.querySelector('.oe-specialty-service.selected');
            if (selected_service) {
                selected_service.scrollIntoView();
            }
        });
    });

</script>
