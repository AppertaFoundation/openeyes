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
 *
 * @var $Element_OphTrOperationbooking_Operation[] $operations
 */

?>
<?php
$this->beginContent('//patient/event_container', array('no_face' => true));
$assetAliasPath = 'application.modules.OphTrOperationbooking.assets';
$this->moduleNameCssClass .= ' edit';
?>

<?php
$clinical = $clinical = $this->checkAccess('OprnViewClinical');
$warnings = $this->patient->getWarnings($clinical);
?>

<div class="cols-12 column">
  <section class="element">
        <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'operation-note-select',
            'enableAjaxValidation' => false,
        ));
?>

        <?php $this->displayErrors($errors) ?>

        <?php if ($warnings) { ?>
            <div class="cols-12 column">
              <div class="alert-box patient with-icon">
                  <?php foreach ($warnings as $warn) { ?>
                    <strong><?php echo $warn['long_msg']; ?></strong>
                    - <?php echo $warn['details'];
                  } ?>
              </div>
            </div>
        <?php } ?>

      <header class="element-header">
        <h3 class="element-title">Create Operation Note</h3>
      </header>

      <input type="hidden" name="SelectBooking"/>
      <input type="hidden" name="template_id"/>

      <div class="data-group">
          <div class="data-value flex-layout">
            <p>
                <?php if (count($operations) > 0) : ?>
                  Please indicate whether this operation note relates to a booking, an unbooked emergency or an outpatient minor note:
                <?php else : ?>
                  There are no open bookings in the current episode so only an emergency or outpatient minor operation note can be created.
                <?php endif; ?>
            </p>
          </div>
          <br/>
        <div class="cols-10">
          <div class="data-group" style="padding-left: 100px">
            <table class="cols-10">
              <thead>
              <tr>
                <th>Booked Date</th>
                <th>Procedure</th>
                <th>Comments</th>
                <th></th>
              </tr>
              </thead>
              <tbody>

                <?php foreach ($operations as $operation) :
                    $procedure_set = ProcedureSet::findForBooking($operation->id);

                    $templates = array();
                    if ($procedure_set) {
                        $templates = OphTrOperationnote_Template::model()
                          ->forUserId(Yii::app()->user->id)
                          ->forProcedureSet($procedure_set)
                          ->findAll();
                    }
                    ?>
                <tr>
                  <td>
                    <span class="cols-4 column <?php echo $theatre_diary_disabled ? 'hide' : '' ?>">
                    <?php if (!$theatre_diary_disabled) {
                        if ($operation->booking) {
                            echo $operation->booking->session->NHSDate('date');
                        }
                    } ?>
                    </span>
                  </td>
                  <td>
                    <?php
                    echo implode('<br />', array_map(function ($procedure) use ($operation) {
                        return $operation->eye->name . ' ' . $procedure->term;
                    }, $operation->procedures));
                    ?>
                  </td>
                  <td>
                      <?= $operation->comments; ?>
                      <?php
                        $existing_consent_criteria = new CDbCriteria();
                        $existing_consent_criteria->with = ['event'];
                        $existing_consent_criteria->compare('event.deleted', 0);
                        $existing_consent_criteria->compare('t.booking_event_id', $operation->event_id);
                        $has_consent = Element_OphTrConsent_Procedure::model()->find($existing_consent_criteria);

                        if ($has_consent) {
                            $withdrawal_criteria = new CDbCriteria();
                            $withdrawal_criteria->compare('event_id', $has_consent->event_id);
                            $withdrawal = Element_OphTrConsent_Withdrawal::model()->find($withdrawal_criteria);
                            if ($withdrawal && $withdrawal->signature_id !== null) { ?>
                            <div class="alert-box warning with-icon">
                              This event has a consent which has been withdrawn.
                            </div>
                            <?php } ?>
                        <?php } ?>
                  </td>
                  <td>
                    <ul class="row-list">
                        <li>
                            <button class="booking-select" data-eye-id="<?=$operation->eye->id?>" data-booking="booking<?= $operation->event_id ?>">
                                Create op note
                            </button>
                        </li>
                        <?php
                        if (count($templates) == 0) { ?>
                            <li><small class="fade">No pre-fill templates</small></li>
                        <?php } else {
                            foreach ($templates as $template) { ?>
                            <li>
                                <button class="booking-select"
                                    data-eye-id="<?=$operation->eye->id?>"
                                    data-booking="booking<?= $operation->event_id ?>"
                                    data-template="<?= $template->id ?>"
                                    data-test="template-entry">
                                    <i class="oe-i starline small pad-r"></i>
                                    <?= $template->event_template->name ?>
                                </button>
                            </li>
                            <?php }
                        } ?>
                    </ul>
                  </td>
                </tr>
                <?php endforeach; ?>
              <tr>
                <td>N/A</td>
                <td>
                  Emergency / Unbooked
                </td>
                <td></td>
                <td>
                   <button class="booking-select" data-booking="emergency">Create default op note</button>
                </td>
              </tr>
              <tr>
                <td>N/A</td>
                <td>
                  Outpatient Minor Ops
                </td>
                <td></td>
                <td>
                   <button class="booking-select" data-booking="outpatient-minor-op">Create minor ops note</button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
            <?php $this->displayErrors($errors, true) ?>
            <?php $this->endWidget(); ?>
    </section>
</div>
<script type="text/javascript">
    /**
     * set the selected booking and submit the form
     * @param booking
     */
    function selectBooking(booking, template) {
        $('[name="SelectBooking"]').val(booking);
        $('[name="template_id"]').val(template);
        $('#operation-note-select').submit();
    }

  $(function () {
    $('.booking-select').on('click', function () {
        let eyeId = $(this).data('eye-id');
        let booking = $(this).data('booking');

        let template_id = $(this).data('template') ? $(this).data('template') : null;
        if (eyeId === 3) {
            // if the procedure is for BOTH eyes, show an alert:
            new OpenEyes.UI.Dialog.Alert({
                content: "Bilateral cataract operation notes are not currently supported. Please complete details for the first eye in this event, then create a second operation note event for the second eye.",
                closeCallback: function () {
                    selectBooking(booking, template_id);
                }
            }).open();
        }
        else {
            selectBooking(booking, template_id);
        }
    });
  });
</script>
<?php $this->endContent(); ?>
