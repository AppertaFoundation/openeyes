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
use \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Role;

$model_name = CHtml::modelName($element);

$queues = array();
if ($ticket_api = Yii::app()->moduleAPI->get('PatientTicketing')) {
    $queues = $element->getPatientTicketQueues($this->firm, $this->patient);
}
?>

<div class="element-fields flex-layout full-width">


    <?php echo $form->hiddenField($element, 'status_id'); ?>
    <?php echo $form->hiddenField($element, 'followup_quantity'); ?>
    <?php echo $form->hiddenField($element, 'followup_period_id'); ?>
    <?php echo $form->hiddenField($element, 'role_id'); ?>
    <?php echo $form->hiddenField($element, 'role_comments'); ?>

  <div class="cols-7">
        <?=\CHtml::textField('follow-up-dummy-input', '', array(
          'class' => 'cols-full',
          'rows' => 1,
          'placeholder' => 'Please select an option from the right',
          'disabled' => true,
          'style' => 'overflow: hidden; overflow-wrap: break-word; height: 24px;',
      )) ?>

    <div id="outcomes-comments" class="flex-layout flex-left comment-group js-comment-container"
         style="<?= $element->description ? '' : 'display: none;' ?>" data-comment-button="#outcomes-comment-button">
        <?php echo $form->textArea(
            $element,
            'description',
            array('nowrapper' => true),
            false,
            array(
                'class' => 'autosize js-comment-field',
                'placeholder' => $element->getAttributeLabel('description'),
            )
        ) ?>
      <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
    </div>

        <?php if ($ticket_api) { ?>
        <br/>
        <div data-queue-ass-form-uri="<?= $ticket_api->getQueueAssignmentFormURI() ?>"
             id="div_<?= CHtml::modelName($element) ?>_patientticket"
             style="<?= !($element->status && $element->status->patientticket) ? 'display: none;' : '' ?>">
          <!-- TODO, this should be pulled from the ticketing module somehow -->
            <?php $ticket = $element->getPatientTicket();
            if ($ticket) { ?>
              <span class="field-info">Already Referred to Virtual Clinic:</span><br/>
                <?php $this->widget($ticket_api::$TICKET_SUMMARY_WIDGET, array('ticket' => $ticket)); ?>
            <?php } else { ?>
              <fieldset class="flex-layout">
                Virtual Clinic:
                <div class="cols-3">
                    <?php if (count($queues) == 0) { ?>
                      <span>No valid Virtual Clinics available</span>
                    <?php } elseif (count($queues) == 1) {
                        echo reset($queues);
                        $qid = key($queues);
                        $_POST['patientticket_queue'] = $qid;
                        ?>
                      <input type="hidden" name="patientticket_queue" value="<?= $qid ?>"/>

                    <?php } else {
                        echo CHtml::dropDownList('patientticket_queue', @$_POST['patientticket_queue'], $queues,
                            array('empty' => 'Select', 'nowrapper' => true, 'options' => array()));
                    } ?>
                </div>
                <div class="cols-1">
                  <i class="oe-i spinner" style="display: none;"></i>
                </div>
              </fieldset>
              <div id="queue-assignment-placeholder">
                  <?php if (@$_POST['patientticket_queue']) {
                        $this->widget(
                          $ticket_api::$QUEUE_ASSIGNMENT_WIDGET,
                          array('queue_id' => $_POST['patientticket_queue'], 'label_width' => 3, 'data_width' => 5)
                        );
                  } ?>
              </div>
            <?php } ?>
        </div>
        <?php } ?>
  </div>
  <div class="flex-item-bottom">
    <button id="outcomes-comment-button"
            class="button js-add-comments"
            data-comment-container="#outcomes-comments"
            style="<?php if ($element->description) :
                ?>visibility: hidden;<?php
                   endif; ?>"
            type="button">
      <i class="oe-i comments small-icon"></i>
    </button>

    <button class="button hint green js-add-select-search" id="show-follow-up-popup-btn" type="button">
      <i class="oe-i plus pro-theme"></i>
    </button>

    <div id="add-to-follow-up" class="oe-add-select-search auto-width" style="display: none;">
      <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
      <button class="button hint green add-icon-btn" id="add-follow-up-btn" type="button">
        <i class="oe-i plus pro-theme"></i>
      </button>
          <table class="select-options">
            <tbody>
            <tr>
              <td>
                <div class="flex-layout flex-top flex-left">
                  <ul class="add-options" id="follow-up-outcome-options">
                        <?php
                        $outcomes = \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status::model()->activeOrPk($element->status_id)->bySubspecialty($this->firm->getSubspecialty())->findAll();
                        $authRoles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
                        foreach ($outcomes as $opt) : ?>
                        <li data-outcome-id="<?= $opt->id ?>" data-followup="<?= $opt->followup ?>"
                            data-str="<?= $opt->name ?>"
                            data-ticket="<?= $opt->patientticket ?>"
                            class="<?= $element->status_id == $opt->id ? 'selected' : '' ?>"
                              <?= $opt->patientticket && (!count($queues) || !isset($authRoles['Patient Tickets'])) ? 'disabled' : '' ?>>
                          <span class="restrict-width"><?= $opt->name ?></span>
                        </li>
                        <?php endforeach; ?>
                  </ul>
                </div>
              </td>
              <td class="follow-up-options-follow-up-only"  style="<?= !($element->status && $element->status->followup) ? 'display: none;' : '' ?>;">
                <div class="flex-layout flex-top flex-left">
                  <ul class="add-options" id="follow-uo-quantity-options">
                        <?php foreach ($element->getFollowUpQuantityOptions() as $quantity) : ?>
                        <li data-str="<?= $quantity ?>"
                            class="<?= $element->followup_quantity == $quantity ? 'selected' : '' ?>">
                            <?= $quantity ?>
                        </li>
                        <?php endforeach; ?>
                  </ul>
                  <ul class="add-options" id="follow-up-period-options">
                        <?php foreach (Period::model()->findAll(array('order' => 'display_order')) as $period) : ?>
                        <li data-str="<?= $period->name ?>" data-period-id="<?= $period->id ?>"
                            class="<?= $element->followup_period_id == $period->id ? 'selected' : '' ?>">
                          <span class="restrict-width"><?= $period->name ?></span>
                        </li>
                        <?php endforeach; ?>
                  </ul>
                </div>
                  </div>
              </td>
              <td class="flex-layout flex-top follow-up-options-follow-up-only"  style="<?= !($element->status && $element->status->followup) ? 'display: none;' : '' ?>;">
                  <ul class="add-options" id="follow-up-role-options">
                        <?php foreach (OphCiExamination_ClinicOutcome_Role::model()->active()->findAll() as $role) : ?>
                        <li data-str="<?= $role->name ?>" data-role-id="<?= $role->id ?>"
                            class="<?= $element->role_id == $role->id ? 'selected' : '' ?>">
                          <span class="restrict-width"><?= $role->name ?></span>
                        </li>
                        <?php endforeach; ?>
                  </ul>
                </div>
              </td>
              <td class="follow-up-options-follow-up-only"  style="<?= !($element->status && $element->status->followup) ? 'display: none;' : '' ?>;">
                <div class="flex-layout flex-top flex-left">
                    <?=\CHtml::textField('follow_up_role_comments', $element->role_comments,
                        array('autocomplete' => Yii::app()->params['html_autocomplete'], 'placeholder' => 'Name (optional)')) ?>
                </div>
                  </div>
              </td>
            </tr>
            </tbody>
          </table>
      </div>
    </div>
  </div>

<script>

  var Element_OphCiExamination_ClinicOutcome_templates = {
        <?php foreach (\OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Template::model()->findAll() as $template) : ?>
    "<?php echo $template->id?>": {
      "clinic_outcome_status_id": <?php echo $template->clinic_outcome_status_id ?>,
      "followup_quantity": "<?php echo $template->followup_quantity ?>",
      "followup_period_id": "<?php echo $template->followup_period_id ?>"
    },
        <?php endforeach ?>
  };
  $(function () {
    setUpAdder(
      $('#add-to-follow-up'),
      null,
      function () {
      },
      $('#show-follow-up-popup-btn'),
      null,
      $('#add-to-follow-up').find('.close-icon-btn')
    );

    // Remove the quantity if it has defaulted to zero (which isn't allowed, it should be null instead)
    if ($('#<?= $model_name ?>_followup_quantity').val() === '0') {
      $('#<?= $model_name ?>_followup_quantity').val('');
    }
  });
</script>
