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

/**
 * @var WaitingListController $this
 * @var Element_OphTrOperationbooking_Operation[] $operations
 */
$operations = $dataProvider->getData();
if (isset($_POST['status']) && $_POST['status'] != '') {
    $operations = array_filter($operations, function ($operation) {
        return $operation->getNextLetter() == $_POST['status'];
    });
}
$institution = Institution::model()->getCurrent();
$selected_site_id = Yii::app()->session['selected_site_id'];
$primary_identifier_usage_type = SettingMetadata::model()->getSetting('display_primary_number_usage_code');
$primary_identifier_prompt = PatientIdentifierHelper::getIdentifierDefaultPromptForInstitution(
    $primary_identifier_usage_type,
    $institution->id,
    $selected_site_id
);

$assetManager = Yii::app()->getAssetManager();
$widgetPath = $assetManager->publish('protected/widgets/js');
Yii::app()->clientScript->registerScriptFile($widgetPath . '/PatientPanelPopupMulti.js');

?>

<div id="pas_warnings" class="alert-box alert with-icon" style="display: none;">
    <ul>
        <li>One or more patients has no <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> practice address, please correct in PAS before printing <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> letter.</li>
        <li>One or more patients has no Address, please correct in PAS before printing a letter for them.</li>
    </ul>
    <a href="#" class="close">×</a>
</div>

<table class="standard waiting-list">
    <thead>
        <tr>
            <th></th>
            <th>Patient</th>
            <th><?= $primary_identifier_prompt ?></th>
            <th>Location</th>
            <th>Procedure</th>
            <th>Eye</th>
            <th>Firm</th>
            <th class="right">Decision date</th>
            <th>Priority</th>
            <th>Complexity</th>
            <th>Book status (requires...)</th>

            <th>
                <label>
                    <input id="checkall" value="" type="checkbox">
                </label>
            </th>
            <?php if ($this->module->isTheatreDiaryDisabled()) : ?>
                <th></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($operations)) { ?>
            <tr>
                <td>
                    There are no patients who match the specified criteria.
                </td>
            </tr>
        <?php } else { ?>
            <?php foreach ($operations as $eo) {
                $patient = $eo->event->episode->patient;
                $contact = $patient->contact;
                ?>

                <?php
                switch ($eo->getWaitingListStatus()) {
                    case Element_OphTrOperationbooking_Operation::STATUS_PURPLE:
                        $letterStatusClass = 'send-invitation-letter';
                        break;
                    case Element_OphTrOperationbooking_Operation::STATUS_GREEN1:
                        $letterStatusClass = 'send-another-reminder';
                        break;
                    case Element_OphTrOperationbooking_Operation::STATUS_GREEN2:
                        $letterStatusClass = 'send-another-reminder';
                        break;
                    case Element_OphTrOperationbooking_Operation::STATUS_ORANGE:
                        $letterStatusClass = 'send-gp-removal-letter';
                        break;
                    case Element_OphTrOperationbooking_Operation::STATUS_RED:
                        $letterStatusClass = 'patient-due-removed';
                        break;
                    default:
                        $letterStatusClass = '';
                        break;
                } ?>
                <tr>
                    <td class="letter-status <?php echo $letterStatusClass ?>">
                        <?php if ($eo->sentInvitation()) { ?>
                            <i class="oe-i letter-in small js-has-tooltip" data-tooltip-content="Invitation Letter"></i>
                        <?php } ?>
                        <?php if ($eo->sent1stReminder()) { ?>
                            <i class="oe-i letter-1 small js-has-tooltip" data-tooltip-content="1st Reminder"></i>
                        <?php } ?>
                        <?php if ($eo->sent2ndReminder()) { ?>
                            <i class="oe-i letter-2 small js-has-tooltip" data-tooltip-content="2nd Reminder"></i>
                        <?php } ?>
                        <?php if ($eo->sentGPLetter()) { ?>
                            <i class="oe-i letter-GP small js-has-tooltip" data-tooltip-content="<?= \SettingMetadata::model()->getSetting('gp_label') . " Removal" ?>"></i>
                        <?php } ?>
                    </td>

                    <td>
                        <?= CHtml::link(
                            '<strong>' . CHtml::encode(strtoupper(trim($contact->last_name))) . '</strong>' . CHtml::encode(" {$contact->first_name} ({$patient->age})"),
                            Yii::app()->createUrl('/OphTrOperationbooking/default/view/' . $eo->event_id)
                        );

                        ?>
                        <?php
                        $patientSummaryPopup = $this->createWidget(
                            'application.widgets.PatientSummaryPopup',
                            array(
                                'patient' => $patient,
                            )
                        );
                        ?>
                        <div id="oe-patient-details" class="js-oe-patient" data-patient-id="<?= $patient->id ?>">
                            <i class="js-patient-quick-overview eye-circle medium pad  oe-i js-worklist-btn" id="js-worklist-btn"></i>
                            <?php $patientSummaryPopup->render('application.widgets.views.PatientSummaryPopup' . 'WorklistSide', []); ?>
                        </div>
                        <script>
                            $(function() {
                                PatientPanel.patientPopups.init(false, <?= $patient->id ?>);

                            });
                        </script>
                    </td>

                    <td class="nowrap">
                        <?php $primary_identifier = PatientIdentifierHelper::getIdentifierForPatient(
                            SettingMetadata::model()->getSetting('display_primary_number_usage_code'),
                            $patient->id,
                            $institution->id,
                            $selected_site_id
                        ); ?>
                        <?= CHtml::encode(PatientIdentifierHelper::getIdentifierValue($primary_identifier)) ?>
                        <?php $this->widget(
                            'application.widgets.PatientIdentifiers',
                            [
                                'patient' => $patient,
                                'show_all' => true,
                                'tooltip_size' => 'small'
                            ]
                        ); ?>
                    </td>
                    <td><?= CHtml::encode($eo->site->short_name) ?></td>
                    <td><?= $eo->getProceduresCommaSeparated('short_format') ?></td>
                    <td>
                        <?php $this->widget('EyeLateralityWidget', array('eye' => $eo->eye)); ?>
                    </td>
                    <td><?= $eo->event->episode->firm->name ?>
                        (<?php echo $eo->event->episode->firm->serviceSubspecialtyAssignment->subspecialty->name ?>)
                    </td>
                    <td class="right"><span class="oe-date">
                            <?php echo Helper::convertDate2HTML($eo->NHSDate('decision_date')) ?>
                    </td>

                    <td>
                        <ul class="dot-list">
                            <li>
                                <?= $eo->priority->name . " "?>
                            </li><?php
                            foreach ($eo->anaesthetic_type as $index => $anaesthetic_type) {
                                echo '<li>';
                                switch ($anaesthetic_type->name) {
                                    case 'Sedation':
                                        echo 'S';
                                        break;
                                    case 'No Anaesthetic':
                                        echo 'N/A';
                                        break;
                                    case 'LA':
                                        echo $anaesthetic_type->name;

                                        if (($this->module->showLAC()) && $eo->is_lac_required == '1') {
                                            echo '</li><li>with Cover';
                                        }
                                        break;
                                    default:
                                        echo $anaesthetic_type->name;
                                        break;
                                }
                                echo '</li>';
                            } ?>
                        </ul>
                    </td>
                    <td><?php echo $eo->getComplexityCaption(); ?></td>
                    <td><?php echo ucfirst(preg_replace('/^Requires /', '', $eo->status->name)) ?></td>
                    <td<?php if ($letterStatusClass == '' && Yii::app()->user->checkAccess('admin')) {
                        ?> class="admin-td" <?php
                       } ?>>

                        <?php if (
                            ($patient && $patient->contact->correspondAddress)
                            && $eo->id
                            && ($eo->getDueLetter() != Element_OphTrOperationbooking_Operation::LETTER_GP
                                || ($eo->getDueLetter() == Element_OphTrOperationbooking_Operation::LETTER_GP && $patient->practice && $patient->practice->contact->address)
                            )
) { ?>
                            <div>
                                <input <?php if ($letterStatusClass == '' && !$this->checkAccess('OprnConfirmBookingLetterPrinted')) {
                                    ?> disabled="disabled" <?php
                                       } ?> type="checkbox" id="operation<?php echo $eo->id ?>" value="1" />
                            </div>
                        <?php } ?>

                        <?php if (!$patient->practice || !$patient->practice->contact->address) { ?>
                            <script type="text/javascript">
                                $('#pas_warnings').show();
                                $('#pas_warnings .no_gp').show();
                            </script>
                            <span class="no-gp error">No <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> </span>
                        <?php } ?>

                        <?php if ($patient && !$patient->contact->correspondAddress) { ?>
                            <script type="text/javascript">
                                $('#pas_warnings').show();
                                $('#pas_warnings .no_address').show();
                            </script>
                            <span class="no-address error">No Address</span>
                        <?php } ?>
                        </td>
                        <?php if ($this->module->isTheatreDiaryDisabled()) : ?>
                            <td>
                                <a href="/OphTrOperationbooking/default/update/<?php echo $eo->event_id; ?>?waiting-list=1" class="button blue hint">Edit Booking</a>
                            </td>
                        <?php else : ?>
                            <td>
                                <a href="/OphTrOperationbooking/booking/schedule/<?php echo $eo->event_id; ?>?waiting-list=1" class="button blue hint">Schedule Operation</a>
                            </td>
                        <?php endif; ?>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">
                <?php $this->widget('LinkPager', ['pages' => $dataProvider->pagination]); ?>
            </td>
        </tr>
        <tr>
            <td colspan="13">
                <div class="waiting-list-key">
                    <h3>Key</h3>
                    <ul>
                        <li>
                            <i class="oe-i letter-in small"></i>- Invitation
                        </li>
                        <li>
                            <i class="oe-i letter-1 small"></i>- 1<sup>st</sup> Reminder
                        </li>
                        <li>
                            <i class="oe-i letter-2 small"></i>- 2<sup>nd</sup> Reminder
                        </li>
                        <li>
                            <i class="oe-i letter-GP small"></i>- <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> Removal
                        </li>
                    </ul>
                    <ul>
                        <li class="send-invitation-letter">
                            Send invitation letter
                        </li>
                        <li class="send-another-reminder">
                            Send another reminder (2 weeks)
                        </li>
                        <li class="send-gp-removal-letter">
                            Send <?php echo \SettingMetadata::model()->getSetting('gp_label') ?> removal letter
                        </li>
                        <li class="patient-due-removed">
                            Patient is due to be removed
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </tfoot>
</table>

<script type="text/javascript">
    $('#checkall').click(function() {
        $('input[id^="operation"]:enabled')
            .attr('checked', $('#checkall').is(':checked'))
            .trigger('change');
    });

    // Row highlighting
    $(this).undelegate('.waiting-list td', 'click').delegate('.waiting-list td', 'click', function() {
        var $tr = $(this).closest("tr");
        $tr.toggleClass('hover');
    });

    // Mark item as booked (in case theatre diary is disabled)
    $(document).on("click", ".btn-booked", function(e) {
        e.preventDefault();
        var event_id = $(this).data("event-id");
        $.get("/OphTrOperationbooking/waitingList/setBooked?event_id=" + event_id,
            function(data) {
                if (data.success) {
                    window.location.reload();
                } else {
                    var alert = new OpenEyes.UI.Dialog.Alert({
                        content: 'An error occured: ' + data.message
                    });
                    alert.open();
                }
            });
    })
</script>
