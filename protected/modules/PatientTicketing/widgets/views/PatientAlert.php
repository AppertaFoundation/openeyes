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
$closing_flash = Yii::app()->user->getFlash('patient-ticketing-closing');
if ($closing_flash) {
    ?>
  <div class="alert-box with-icon success">
      <?= $closing_flash; ?>
  </div>
<?php } ?>

<?php
$patient_ticket_in_review = null;
$api = Yii::app()->moduleAPI->get('PatientTicketing');

if (isset(Yii::app()->session['patientticket_ticket_in_review'])) {
    $patient_ticket_in_review = Yii::app()->session['patientticket_ticket_in_review'];
    if ($patient_ticket_in_review['patient_id'] != $this->patient->id) {
        unset(Yii::app()->session['patientticket_ticket_in_review']);
    }
}

$tickets = array_filter($tickets, function ($ticket) use ($patient_ticket_in_review) {
    return @$patient_ticket_in_review['ticket_id'] == $ticket->id;
});

if (count($tickets) && Yii::app()->user->checkAccess('OprnViewClinical')) { ?>
    <?php if ($this->assetFolder) { ?>
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


  <div id="patient-alert-patientticketing"
       class="oe-hotlist-panel"
       data-patient-id="<?= $this->patient->id ?>"
       style="display: none;">
    <div class="oe-vc-mode">
      <div class="vc-content">
          <?php foreach ($tickets as $ticket) { ?>
            <div data-ticket-id="<?= $ticket->id ?>">
                <?php if ($ticket->priority) { ?>
                  <div class="priority">
                      <span class="highlighter <?= $ticket->priority->colour ?>"><?= $ticket->priority->name ?></span>
                  </div>
                <?php } ?>

              <div class="scratchpad">
                <button id="js-vc-scratchpad" class="blue hint">Scratchpad</button>
              </div>

              <h3>VC: <b><?= strtoupper(trim($ticket->patient->last_name)) ?>,
                      <?= $ticket->patient->first_name ?></b></h3>

              <div class="row divider">
                <ul class="vc-steps">
                    <?= $api->renderVirtualClinicSteps($ticket) ?>
                </ul>
              </div>

                <?php if ($ticket->event) { ?>
                  <div class="row divider">
                    <div class="data-label">Date of
                      <?= $ticket->event->eventType->name ?> :
                      <?= Helper::convertDate2NHS($ticket->event->event_date) ?>
                    </div>
                  </div>
                <?php } ?>

              <div data-ticket-id="<?= $ticket->id ?>">
                  <?php $this->widget($summary_widget, array('ticket' => $ticket)); ?>
                  <?php
                    $qs_svc = Yii::app()->service->getService('PatientTicketing_QueueSet');
                    $qs_r = $qs_svc->getQueueSetForTicket($ticket->id);
                    if ($qs_svc->isQueueSetPermissionedForUser($qs_r, Yii::app()->user->id)) {
                        $this->widget('OEModule\PatientTicketing\widgets\TicketMove', array(
                              'ticket' => $ticket,
                          ));
                    }
                    ?>
              </div>
            </div>
            <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>