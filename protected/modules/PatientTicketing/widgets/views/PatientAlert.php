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
    if ($this->assetFolder) {?>
        <script type="text/javascript" src="<?php echo $this->assetFolder?>/<?php echo $this->shortName ?>.js"></script>
    <?php }?>

    <div class="row" id="patient-alert-patientticketing" data-patient-id="<?= $this->patient->id ?>">
        <div class="large-12 column">
            <?php
            if (isset(Yii::app()->session['patientticket_ticket_in_review'])) {
                $patient_ticket_in_review = Yii::app()->session['patientticket_ticket_in_review'];
                if ($patient_ticket_in_review['patient_id'] !=  $this->patient->id) {
                    unset(Yii::app()->session['patientticket_ticket_in_review']);
                }
            }
            foreach ($tickets as $ticket) {
                if(@$patient_ticket_in_review['ticket_id'] == $ticket->id) {
                    $cat = $t_svc->getCategoryForTicket($ticket);
                    $expand = false;
                    if (in_array($ticket->id, $current_ticket_ids)) {
                        $expand = true;
                    }
                    ?>
                    <div class="alert-box js-toggle-container patientticketing" data-ticket-id="<?= $ticket->id ?>">
                        <header><strong class="box-title"><?= $cat->name ?>: Patient is in <?= $ticket->current_queue->queueset->name ?>, <?= $ticket->current_queue->name ?></strong></header>
                        <a href="#" class="toggle-trigger toggle-<?= $expand ? 'hide' : 'show' ?> js-toggle">
								<span class="icon-showhide">
									Show/hide this section
								</span>
                        </a>
                        <div class="js-toggle-body" <?php if (!$expand) {?>style="display: none;"<?php }?>>
                            <div class="row">
                                <div class="large-6 column">
                                    <?php $this->widget($summary_widget, array('ticket' => $ticket)); ?>
                                </div>
                                <div class="large-6 column">
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


                        </div>
                    </div>
                <?php }
            }
            ?>
        </div>
    </div>
<?php } ?>