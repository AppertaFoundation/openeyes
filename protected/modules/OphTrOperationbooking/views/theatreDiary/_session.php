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
<div class="hide" id="infoBox_<?php echo $session->id ?>">
  <div class="cols-12 column">
    <div class="alert-box success with-icon">
      <strong>Session updated!</strong>
    </div>
  </div>
</div>

<?php $this->beginWidget('CActiveForm', array(
    'id' => 'session_form' . $session->id,
    'action' => Yii::app()->createUrl('/OphTrOperationbooking/theatreDiary/saveSession'),
    'enableAjaxValidation' => false,
)) ?>
<div class="action_options diaryViewMode" data-id="<?php echo $session->id ?>" style="float: right;">
  <img id="loader_<?php echo $session->id ?>"
       src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>" alt="loading..."
       style="margin-right: 5px; margin-bottom: 4px; display: none;"/>
</div>

<div class="schedule-group">
  <h3 class="theatre-booking-heading">
    <span class="date">
      <strong>
          <?php echo date('d M', strtotime($session->date)) ?>
      </strong>
        <?php echo date('Y', strtotime($session->date)) ?>
    </span>
    -
    <strong>
      <span class="day">
        <?php echo date('l', strtotime($session->date)) ?>
      </span>,
      <span class="time">
        <?php echo $session->start_time ?>
        -
          <?php echo $session->end_time ?>
      </span>
    </strong>
    for
      <?php echo $session->firm ? $session->firm->name : 'Emergency List' ?>
      <?php echo $session->firm ? 'for (' . $session->firm->serviceSubspecialtyAssignment->subspecialty->name . ')' : '' ?>
    -
    <strong><?php echo $theatre->name . ' (' . $theatre->site->name . ')' ?></strong>
  </h3>

  <div class="theatre-booking-actions">
  <span class="session_options diaryViewMode" data-id="<?php echo $session->id ?>">
    <a class="button large selected inactive" href="#">View</a>
      <?php if ($this->checkAccess('OprnEditTheatreSession')) { ?>
        <a href="#" rel="<?php echo $session->id ?>" class="button large blue hint edit-session">Edit</a>
      <?php } ?>
  </span>
    <span class="session_options diaryEditMode" data-id="<?php echo $session->id ?>" style="display: none">
      <a href="#" rel="<?php echo $session->id ?>" class="button large blue hint view-session">View</a>
      <a class="aBtn_inactive button large selected inactive">Edit</a>
  </span>
  </div>

  <div class="theatre-sessions">
    <table class="theatre-bookings">
      <thead id="thead_<?php echo $session->id ?>">
      <tr>
        <th>Admit time</th>
        <th class="diaryEditMode" data-id="<?php echo $session->id ?>"
            style="display: none;">Sort
        </th>
        <th>Hospital #</th>
        <th>Confirmed</th>
        <th>Patient (Age)</th>
        <th>[Eye] Operation</th>
        <th>Priority</th>
        <th>Anesth</th>
        <th>Ward</th>
        <th>Info</th>
      </tr>
      </thead>
      <tbody id="tbody_<?php echo $session->id ?>">
      <?php foreach ($session->getActiveBookingsForWard($ward_id) as $booking) { ?>
        <tr id="oprow_<?php echo $booking->element_id ?>">
          <td class="session">
            <input style="display: none;" type="text"
                   autocomplete="<?php echo Yii::app()->params['html_autocomplete'] ?>"
                   class="admitTime diaryEditMode"
                   name="admitTime_<?php echo $booking->element_id ?>"
                   data-id="<?php echo $session->id ?>"
                   data-operation-id="<?php echo $booking->element_id ?>"
                   value="<?php echo substr($booking->admission_time, 0, 5) ?>" size="4">
            <span class="admitTime_ro diaryViewMode" data-id="<?php echo $session->id ?>"
                  data-operation-id="<?php echo $booking->element_id ?>"><?php echo substr($booking->admission_time,
                    0, 5) ?></span>
          </td>
          <td class="td_sort diaryEditMode" data-id="<?php echo $session->id ?>"
              style="display: none;">
            <i class="oe-i menu medium pad"></i>
          </td>
          <td class="hospital">
              <?=\CHtml::link($booking->operation->event->episode->patient->hos_num,
                  Yii::app()->createUrl('/OphTrOperationbooking/default/view/' . $booking->operation->event_id));
              ?></td>
          <td class="confirm">
            <input type="hidden" name="confirm_<?php echo $booking->element_id ?>" value="0"/>
            <input id="confirm_<?php echo $booking->element_id ?>" type="checkbox" value="1"
                   name="confirm_<?php echo $booking->element_id ?>" disabled="disabled"
                   <?php if ($booking->confirmed) { ?>checked="checked" <?php } ?>/>
          </td>
          <td class="patient">
              <?php echo strtoupper($booking->operation->event->episode->patient->last_name) ?>,
              <?php echo $booking->operation->event->episode->patient->first_name ?>
              (<?php echo $booking->operation->event->episode->patient->age ?>)
              <?php
                $warnings = $booking->operation->event->episode->patient->getWarnings();
                if ($warnings) {
                    $msgs = [];
                    foreach ($warnings as $warn) {
                        $msgs[] = $warn['long_msg'] . " - " . $warn['details'];
                    } ?>
                    <i class="oe-i warning medium pad js-has-tooltip"
                       data-tooltip-content="<?= implode(' / ', $msgs) ?>"></i>
                <?php } ?>
          </td>
          <td class="operation">
              <i class="oe-i circle-<?=$booking->operation->getComplexityColor()?> small pad-right js-has-tooltip" data-tooltip-content="<?=$booking->operation->getComplexityCaption()?> complexity"></i>
              <?php echo $booking->operation->procedures ? '[' . $booking->operation->eye->adjective . '] ' . $booking->operation->getProceduresCommaSeparated('short_format') : 'No procedures' ?>
          </td>
          <td class="">
              <?php echo $booking->operation->priority->name ?>
          </td>
          <td class="anesthetic">
              <?php echo $booking->operation->getAnaestheticTypeDisplay() ?>
          </td>
          <td class="ward">
              <?php echo $booking->ward ? $booking->ward->name : 'None' ?>
          </td>
          <td class="alerts">
              <?php if ($booking->operation->event->episode->patient->gender == 'M') { ?>
                <i class="oe-i male medium pad js-has-tooltip" data-tooltip-content="Male"></i>
              <?php } else { ?>
                <i class="oe-i female medium pad js-has-tooltip" data-tooltip-content="Female"></i>
              <?php } ?>


              <?php if ($booking->operation->comments && preg_match('/\w/', $booking->operation->comments)): ?>
                <i class="oe-i info medium pad js-has-tooltip"
                   data-tooltip-content="<?= CHtml::encode($booking->operation->comments, ENT_COMPAT,
                       'UTF-8') ?>"></i>
              <?php endif; ?>

              <?php
              if ($booking->operation->comments_rtt && preg_match('/\w/', $booking->operation->comments_rtt)): ?>
                <i class="oe-i comments medium pad js-has-tooltip"
                   data-tooltip-content="<?=\CHtml::encode($booking->operation->comments_rtt, ENT_COMPAT,
                       'UTF-8') ?>"></i>
              <?php endif; ?>

              <?php if ($booking->operation->overnight_stay): ?>
                <i class="oe-i overnight medium pad js-has-tooltip"
                   data-tooltip-content="Overnight stay required"></i>
              <?php endif; ?>
              <?php if ($booking->operation->consultant_required) { ?>
                <i class="oe-i asterisk medium pad js-has-tooltip"
                   data-tooltip-content="Consultant required"></i>
              <?php } ?>
            <i class="oe-i audit-trail medium pad js-has-tooltip"
               data-tooltip-content="Created by: <?= $booking->user->fullName . "\n" ?>Last modified by: <?= $booking->usermodified->fullName ?>"></i>
              <?php if($booking->operation->is_golden_patient){ ?>
                  <i class="oe-i star medium pad js-has-tooltip" data-tooltip-content="Golden Patient"></i>
            <?php  }?>
          </td>
        </tr>
      <?php } ?>
      </tbody>
      <tfoot id="tfoot_<?php echo $session->id ?>">
      <tr>
          <?php
          $minutes_status = ($session->availableMinutes > 0);
          $proc_status = (!$session->max_procedures || $session->getAvailableProcedureCount() > 0);
          $complex_proc_status = (!$session->max_complex_procedures || $session->getAvailableComplexProcedureCount() > 0);
          $status = ($minutes_status && $proc_status && $session->available);
          ?>
        <td colspan="10" data-minutes-available="<?= $session->availableMinutes ?>"
            class="<?php echo ($status) ? 'available' : ''; ?>">
          <div class="session_timeleft time-left <?php echo ($status) ? 'available' : 'full'; ?>">
              <?php if ($minutes_status) { ?>
                  <?php echo $session->availableMinutes ?> minutes unallocated
              <?php } else { ?>
                  <?php echo abs($session->availableMinutes) ?> minutes overbooked
              <?php } ?>
            <span data-currproccount="<?php echo $session->getBookedProcedureCount() ?>"
                  class="procedure-count" id="procedure_count_<?php echo $session->id ?>"
                  <?php if (!$session->max_procedures) { ?>style="display: none;"<?php } ?>>
                    <br/>
                    <span class="available-val">
                        <?= $proc_status ? $session->getAvailableProcedureCount() : '0' ?>
                    </span> procedure(s) available
                    <span
                        class="overbooked"<?php if ($session->getAvailableProcedureCount() >= 0) { ?> style="display: none;"<?php } ?>>
                    (Overbooked by <span
                          class="overbooked-proc-val"><?= abs($session->getAvailableProcedureCount()); ?></span>)</span>
                  </span>
            <span data-curr-complex-proccount="<?php echo $session->getBookedComplexProcedureCount() ?>"
                class="complex-procedure-count" id="complex_procedure_count_<?php echo $session->id ?>"
                <?php if (!$session->max_complex_procedures) { ?>style="display: none;"<?php } ?>>
                <br/>
                <span class="complex-available-val">
                    <?= $complex_proc_status ? $session->getAvailableComplexProcedureCount() : '0' ?>
                </span> complex procedure(s) available
                <span class="overbooked"<?php if ($session->getAvailableComplexProcedureCount() >= 0) { ?> style="display: none;"<?php } ?>>
                    (Overbooked by
                    <span
                            class="overbooked-complex-proc-val"><?= abs($session->getAvailableComplexProcedureCount()); ?></span>)
                </span>
            </span>
            <span class="session-unavailable" id="session_unavailable_<?php echo $session->id ?>"
                <?php if ($session->available) { ?> style="display:none;" <?php } ?>> - session unavailable
            <span id="session_unavailablereason_<?php echo $session->id ?>">
                <?php if ($session->unavailablereason) {
                    echo ' - ' . $session->unavailablereason->name;
                } ?>
            </span>
          </span>
          </div>
          <div class="specialists">
            <div class="diaryViewMode"
                <?php if (!$session->consultant && !$session->anaesthetist && !$session->paediatric && !$session->max_procedures): ?>
                  style="display: none"
                <?php endif; ?>>
              <div <?php if (!$session->consultant): ?>style="display: none;"<?php endif; ?>
                   id="consultant_icon_<?php echo $session->id ?>" class="consultant"
                   title="Consultant Present">
                Consultant
              </div>
              <div <?php if (!$session->anaesthetist): ?>style="display: none;"<?php endif; ?>
                   id="anaesthetist_icon_<?php echo $session->id ?>" class="anaesthetist"
                   title="Anaesthetist Present">
                Anaesthetist <?php if ($session->general_anaesthetic) { ?>(GA)<?php } ?></div>
              <div<?php if (!$session->paediatric) { ?> style="display: none;"<?php } ?>
                  id="paediatric_icon_<?php echo $session->id ?>" class="paediatric"
                  title="Paediatric Session">Paediatric
              </div>
              <div<?php if (!$session->max_procedures) { ?> style="display: none;"<?php } ?>
                  id="max_procedures_icon_<?php echo $session->id ?>" class="max-procedures"
                  title="Max <?php echo $session->max_procedures ?>">Max <span
                    class="max-procedures-val"><?php echo $session->max_procedures ?></span>
                Procedures
              </div>
              <div<?php if (!$session->max_complex_procedures) { ?> style="display: none;"<?php } ?>
                  id="max_complex_procedures_icon_<?php echo $session->id ?>" class="max-complex-procedures"
                  title="Max Complex <?php echo $session->max_complex_procedures ?>">Max <span
                    class="max-complex-procedures-val"><?php echo $session->max_complex_procedures ?></span>
                Complex Procedures
              </div>
            </div>

              <?php if ($this->checkAccess('OprnEditTheatreSessionDetails')) { ?>
                <div class="session-options diaryEditMode" data-id="<?php echo $session->id ?>" style="display: none;">
                  <input type="hidden" name="consultant_<?php echo $session->id ?>" value="0"/>
                  <input type="hidden" name="paediatric_<?php echo $session->id ?>" value="0"/>
                  <input type="hidden" name="anaesthetist_<?php echo $session->id ?>" value="0"/>
                  <input type="hidden" name="general_anaesthetic_<?php echo $session->id ?>" value="0"/>
                  <input type="hidden" name="available_<?php echo $session->id ?>" value="0"/>
                  <label>
                    <input type="checkbox" id="consultant_<?php echo $session->id ?>"
                           name="consultant_<?php echo $session->id ?>"
                           value="1"<?php if ($session->consultant) { ?> checked="checked"<?php } ?> />
                    Consultant present
                  </label>
                  <label>
                    <input type="checkbox" id="paediatric_<?php echo $session->id ?>"
                           name="paediatric_<?php echo $session->id ?>"
                           value="1"<?php if ($session->paediatric) { ?> checked="checked"<?php } ?> />
                    Paediatric
                  </label>
                  <label>
                    <input type="checkbox" id="anaesthetist_<?php echo $session->id ?>"
                           name="anaesthetist_<?php echo $session->id ?>"
                           value="1"<?php if ($session->anaesthetist) { ?> checked="checked"<?php } ?> />
                    Anaesthetist present
                  </label>
                  <label>
                    <input type="checkbox" id="general_anaesthetic_<?php echo $session->id ?>"
                           name="general_anaesthetic_<?php echo $session->id ?>"
                           value="1"<?php if ($session->general_anaesthetic) { ?> checked="checked"<?php } ?> />
                    General anaesthetic available
                  </label>
                  <label>
                    <input type="checkbox" class="session-available"
                           id="available_<?php echo $session->id ?>"
                           name="available_<?php echo $session->id ?>"
                           value="1"<?php if ($session->available) { ?> checked="checked"<?php } ?> />
                    Session available
                  </label>
                  <label <?php if ($session->available) { ?>style="display: none; color:#000;"<?php } ?>>
                      <?=\CHtml::dropDownList('unavailablereason_id_' . $session->id,
                          $session->unavailablereason_id,
                          CHtml::listData($session->getUnavailableReasonList(), 'id', 'name'),
                          array('empty' => 'Select', 'class' => 'unavailable-reasons')) ?>
                  </label>
                  <input style="display: inline-block;" type="text"
                         autocomplete="<?php echo Yii::app()->params['html_autocomplete'] ?>"
                         class="limited-width" id="max_procedures_<?php echo $session->id ?>" maxlength="2"
                         size="2" name="max_procedures_<?php echo $session->id ?>"
                         value="<?php echo $session->max_procedures; ?>"/>
                  <label style="display: inline-block;">
                      <?php echo $session->getAttributeLabel('max_procedures'); ?>
                  </label>
                  <div>
                    <input style="display: inline-block;" type="text"
                           autocomplete="<?php echo Yii::app()->params['html_autocomplete'] ?>"
                           class="limited-width" id="max_complex_procedures_<?php echo $session->id ?>" maxlength="2"
                           size="2" name="max_complex_procedures_<?php echo $session->id ?>"
                           value="<?php echo $session->max_complex_procedures; ?>"/>
                    <label style="display: inline-block;">
                      <?php echo $session->getAttributeLabel('max_complex_procedures'); ?>
                    </label>
                  </div>
                </div>
              <?php } else { ?>
                <input type="hidden" id="consultant_<?php echo $session->id ?>"
                       name="consultant_<?php echo $session->id ?>" value="<?php echo $session->consultant ?>"/>
                <input type="hidden" id="paediatric_<?php echo $session->id ?>"
                       name="paediatric_<?php echo $session->id ?>" value="<?php echo $session->paediatric ?>"/>
                <input type="hidden" id="anaesthetist_<?php echo $session->id ?>"
                       name="anaesthetist_<?php echo $session->id ?>"
                       value="<?php echo $session->anaesthetist ?>"/>
                <input type="hidden" id="general_anaesthetic_<?php echo $session->id ?>"
                       name="general_anaesthetic_<?php echo $session->id ?>"
                       value="<?php echo $session->general_anaesthetic ?>"/>
                <input type="hidden" id="available_<?php echo $session->id ?>"
                       name="available_<?php echo $session->id ?>" value="<?php echo $session->available ?>"/>
              <?php } ?>
          </div>
        </td>
      </tr>
      </tfoot>
    </table>

    <div class="session-comments">
      <form>
        <textarea name="comments_<?php echo $session->id ?>"
                  class="cols-9 comments diaryEditMode"
                  style="display: none;"
                  data-id="<?php echo $session->id ?>"><?=\CHtml::encode($session['comments']) ?></textarea>
          <?php $title = 'Modified on ' . Helper::convertMySQL2NHS($session->last_modified_date) . ' at ' . substr($session->last_modified_date,
                  13, 5) . ' by ' . $session->session_usermodified->fullName; ?>

        <span class="comments diaryViewMode"
              data-id="<?php echo $session->id ?>"
              title="<?php echo $title; ?>">
          <i class="oe-i info medium no-click pad"></i>
          <span class="comment"><?=\CHtml::encode($session->comments) ?></span>
        </span>
      </form>
    </div>
  </div>

  <div class="data-group">
    <div style="display: none;" data-id="<?php echo $session->id ?>" class="diaryEditMode">
      <i id="loader2_<?php echo $session->id ?>" class="spinner" style="display:none"></i>

      <button type="submit" class="secondary small theatre"
              id="btn_edit_session_save_<?php echo $session->id ?>"><span
            class="button-span button-span-green">Save changes to session</span></button>
      <button type="submit" class="warning small theatre"
              id="btn_edit_session_cancel_<?php echo $session->id ?>"><span
            class="button-span button-span-red">Cancel</span></button>
    </div>
  </div>

</div>
<?php $this->endWidget() ?>
