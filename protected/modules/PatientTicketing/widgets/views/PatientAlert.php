<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2014
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2014, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var OEModule\PatientTicketing\models\Ticket[] $tickets
 * @var string $summary_widget
 */
?>


<?php
if ($closing_flsh = Yii::app()->user->getFlash('patient-ticketing-closing')) {
    ?>
  <div class="alert-box with-icon success">
      <?= $closing_flsh; ?>
  </div>
<?php }
if (count($tickets) && Yii::app()->user->checkAccess('OprnViewClinical')) {
    $qs_svc = Yii::app()->service->getService('PatientTicketing_QueueSet');
    if ($this->assetFolder) { ?>
      <script type="text/javascript" src="<?php echo $this->assetFolder ?>/<?php echo $this->shortName ?>.js"></script>
    <?php } ?>

  <div id="oe-vc-scratchpad" class="oe-vc-mode vc-floating-input" draggable="true" style="display:none;">
    <div class="title">ScratchPad</div>
    <div class="vc-content">
      <div class="row">
        <textarea class="cols-full" placeholder="ScratchPad (wiped on patient change)"></textarea>
      </div>
    </div>
  </div>

  <script>
    $(function () {

      $('.vc-floating-input').draggable();

      var storageKey = 'sratchpad_' + OE_patient_id;
      var oldScratchValue = window.localStorage.getItem(storageKey);
      var $scratchInput = $('.vc-floating-input textarea');
      if (oldScratchValue) {
        $scratchInput.val(oldScratchValue);
        $('.vc-floating-input').show();
        $scratchInput.autosize().input();
      }

      $scratchInput.change(function () {
        console.log('!!!');
        window.localStorage.setItem(storageKey, $(this).val());
      });
    });
  </script>

  <div id="patient-alert-patientticketing"
       class="oe-hotlist-panel"
       data-patient-id="<?= $this->patient->id ?>"
       style="display: block;">
    <div class="oe-vc-mode">
      <div class="vc-content">
          <?php
          if (isset(Yii::app()->session['patientticket_ticket_in_review'])) {
              $patient_ticket_in_review = Yii::app()->session['patientticket_ticket_in_review'];
              if ($patient_ticket_in_review['patient_id'] != $this->patient->id) {
                  unset(Yii::app()->session['patientticket_ticket_in_review']);
              }
          }

          foreach ($tickets as $ticket) {
              if (@$patient_ticket_in_review['ticket_id'] == $ticket->id) {
                  $cat = $t_svc->getCategoryForTicket($ticket);
                  ?>
                <div data-ticket-id="<?= $ticket->id ?>">
                    <?php if ($ticket->priority) { ?>
                      <div class="priority">
                        <i class="oe-i circle-<?= $ticket->priority->name ?> medium"></i>
                      </div>
                    <?php } ?>

                  <div class="scratchpad">
                    <button id="js-vc-scratchpad" class="blue hint">ScratchPad</button>
                  </div>
                  <script>
                    $(document).on('click', '#js-vc-scratchpad', function () {
                      $('#oe-vc-scratchpad').toggle();

                      var txt = $('#oe-vc-scratchpad').is(':visible') ? 'Hide ScratchPad' : 'ScratchPad';
                      $(this).text(txt);
                    });
                  </script>

                  <h3>VC: <b><?= strtoupper(trim($ticket->patient->last_name)) ?>,
                          <?= $ticket->patient->first_name ?></b></h3>

                  <div class="row divider">
                    <ul class="vc-steps">
                      <li class="selected">1. VC Step one</li>
                      <li>2. VC Step two</li>
                    </ul>
                  </div>

                    <?php if ($ticket->event) { ?>
                      <div class="row divider">
                        <div class="data-label">Date of <?= $ticket->event->eventType->name ?>
                          : <?= Helper::convertDate2NHS($ticket->event->event_date) ?></div>
                        <div class="data-value">
                          <textarea readonly="" rows="1" class="cols-full"
                                    style="overflow: hidden; overflow-wrap: break-word; height: 25px;">No significant issues reported</textarea>
                        </div>
                      </div>
                    <?php } ?>

                  <div data-ticket-id="<?= $ticket->id ?>">

                    <!--<header><strong class="box-title"><?= $cat->name ?>: Patient is
                      in <?= $ticket->current_queue->queueset->name ?>, <?= $ticket->current_queue->name ?></strong>
                  </header>-->

                      <?php $this->widget($summary_widget, array('ticket' => $ticket)); ?>


                      <?php
                      $qs_r = $qs_svc->getQueueSetForTicket($ticket->id);
                      if ($qs_svc->isQueueSetPermissionedForUser($qs_r, Yii::app()->user->id)) {
                          $this->widget('OEModule\PatientTicketing\widgets\TicketMove', array(
                                  'ticket' => $ticket,
                              )
                          );
                      }
                      ?>


                  </div>
                </div>
              <?php }
          }
          ?>
      </div>
    </div>
  </div>
<?php } ?>