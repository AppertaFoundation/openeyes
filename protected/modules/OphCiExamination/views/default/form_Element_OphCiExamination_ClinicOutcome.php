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

use OEModule\OphCiExamination\models\DischargeDestination;
use \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Role;
use OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Status;
use \OEModule\OphCiExamination\models\OphCiExamination_ClinicOutcome_Risk_Status;
$current_institution_id = Yii::app()->session['selected_institution_id'];

Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/ClinicOutcome.js", CClientScript::POS_HEAD);
$model_name = CHtml::modelName($element);

$ticket = $element->getPatientTicket();
$queues = [];
$ticket_api = Yii::app()->moduleAPI->get('PatientTicketing');
if ($ticket_api) {
    $queues = $element->getPatientTicketQueues($this->firm, $this->patient);
}

$patient_ticket_statuses = [];
foreach (OphCiExamination_ClinicOutcome_Status::model()->findAll('patientticket=:patientticket', [':patientticket' => 1]) as $status) {
    $patient_ticket_statuses[] = $status->id;
}
?>

<div class="element-fields flex-layout full-width">
    <input id="pt_status_list" type="hidden" data-statuses="<?= htmlspecialchars(json_encode($patient_ticket_statuses)); ?>"/>
    <div class="cols-10">
        <table id="<?= $model_name ?>_entry_table" class="cols-full">
            <colgroup>
                <col>
                <col>
                <col class="cols-2">
            </colgroup>
            <tbody class="entries">
            <?php
            $row_count = 0;
            foreach ($element->entries as $entry) {
                $this->renderPartial(
                    'ClinicOutcomeEntry_event_edit',
                    array(
                        'entry' => $entry,
                        'form' => $form,
                        'model_name' => $model_name,
                        'field_prefix' => $model_name . '[entries]' . '[' . $row_count . ']',
                        'row_count' => $row_count,
                        'condition_text' => $row_count ? "AND" : '',
                        'ticket_api' => $ticket_api ? $ticket_api : null,
                        'queues' => $queues,
                        'ticket' => $ticket,
                        'patient_ticket' => $entry->isPatientTicket(),
                    )
                );
                $row_count++;
            }
            ?>
            </tbody>
        </table>

        <div id="outcomes-comments" class="flex-layout flex-left comment-group js-comment-container"
             style="<?= $element->comments ? '' : 'display: none;' ?>"
             data-comment-button="#outcomes-comment-button">
            <?php echo $form->textArea(
                $element,
                'comments',
                array('nowrapper' => true),
                false,
                array(
                    'class' => 'autosize js-comment-field',
                    'placeholder' => $element->getAttributeLabel('comments'),
                )
            ) ?>
            <i class="oe-i remove-circle small-icon pad-left js-remove-add-comments"></i>
        </div>
    </div>
    <div class="flex-item-bottom">
        <button id="outcomes-comment-button"
                class="button js-add-comments"
                data-comment-container="#outcomes-comments"
                style="<?= ($element->comments) ? "visibility: hidden;" : "" ?>"
                type="button">
            <i class="oe-i comments small-icon"></i>
        </button>

        <button class="button hint green js-add-select-search" id="show-follow-up-popup-btn" type="button">
            <i class="oe-i plus pro-theme"></i>
        </button>

        <div id="add-to-follow-up" class="oe-add-select-search auto-width" style="display: none;">
            <div class="close-icon-btn"><i class="oe-i remove-circle medium"></i></div>
            <button class="button hint green add-icon-btn" id="add-followup-btn" type="button">
                <i class="oe-i plus pro-theme"></i>
            </button>
            <table class="select-options">
                <thead>
                <tr>
                    <th></th>
                    <th style="display: none;" class="follow-up-options-discharge-only">Discharge Status</th>
                    <th style="display: none;" class="follow-up-options-discharge-only">Discharge Destination</th>
                    <th style="display: none;" class="follow-up-options-discharge-transfer-only">Transfer to Institution</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <div class="flex-layout flex-top flex-left">
                            <ul class="add-options" id="followup-outcome-options">
                                <?php
                                $outcomes = OphCiExamination_ClinicOutcome_Status::model()->active()->bySubspecialty($this->firm->getSubspecialty())->findAll('institution_id = :id', [':id' => $current_institution_id]);
                                $authRoles = Yii::app()->authManager->getRoles(Yii::app()->user->id);
                                foreach ($outcomes as $opt) : ?>
                                    <li data-id="<?= $opt->id ?>" data-label="<?= $opt->name ?>"
                                        <?= $opt->patientticket && (!count($queues) || !isset($authRoles['Patient Tickets'])) ? 'disabled' : '' ?>
                                        data-followup="<?= $opt->followup ?>"
                                        data-discharge="<?= $opt->discharge ?>"
                                        data-patient-ticket="<?= $opt->patientticket ?>">
                                        <span class="fixed-width extended"><?= $opt->name ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </td>
                    <td class="follow-up-options-follow-up-only" style="display: none">
                        <div class="flex-layout flex-top flex-left">
                            <ul class="add-options number" id="follow-up-quantity-options">
                                <?php foreach ($element->getFollowUpQuantityOptions() as $quantity) : ?>
                                    <li data-quantity="<?= $quantity ?>">
                                    <?= $quantity ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <ul class="add-options" id="follow-up-period-options">
                                <?php foreach (Period::model()->findAll(array('order' => 'display_order')) as $period) : ?>
                                    <li data-period-id="<?= $period->id ?>" data-label="<?= $period->name ?>">
                                        <span class="restrict-width"><?= $period->name ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </td>
                    <td class="flex-layout flex-top follow-up-options-follow-up-only" style="display: none">
                        <ul class="add-options" id="follow-up-role-options">
                            <?php foreach (OphCiExamination_ClinicOutcome_Role::model()->active()->findAll('institution_id = :id', [':id' => $current_institution_id]) as $role) : ?>
                                <li data-role-id="<?= $role->id ?>" data-label="<?= $role->name ?>">
                                    <span class="restrict-width"><?= $role->name ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td class="follow-up-options-follow-up-only" style="display: none">
                        <ul class="add-options" id="follow-up-risk-status-options">
                            <?php foreach (OphCiExamination_ClinicOutcome_Risk_Status::model()->findAll() as $risk_status_entry) : ?>
                                <li data-risk-status-id="<?= $risk_status_entry->id ?>" data-label="<?= $risk_status_entry->name ?>" data-display="<?= $risk_status_entry->name?>. <?= $risk_status_entry->alias ?>">
                                    <span class="fixed-width extended">
                                        <i 
                                        class="oe-i triangle-<?=$risk_status_entry->getIndicatorColor()?> small pad-right js-risk-status-details"
                                        ></i><?= $risk_status_entry->alias ?> (<?= $risk_status_entry->name ?>)
                                        <br>
                                        <?= $risk_status_entry->description ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td class="follow-up-options-follow-up-only" style="display: none">
                        <div class="flex-layout flex-top flex-left">
                            <input type="text" id="followup_comments" placeholder="Name (optional)">
                        </div>
                    </td>
                    <td class="follow-up-options-discharge-only" style="display: none;">
                        <ul class="add-options" id="discharge-status-options">
                            <?php foreach (\OEModule\OphCiExamination\models\DischargeStatus::model()->findAll() as $status_entry) : ?>
                                <li data-discharge-status-id="<?= $status_entry->id ?>" data-label="<?= $status_entry->name ?>">
                                    <span class="restrict-width"><?= $status_entry->name ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td class="follow-up-options-discharge-only" style="display: none;">
                        <ul class="add-options" id="discharge-destination-options">
                            <?php foreach (DischargeDestination::model()->findAll() as $destination_entry) : ?>
                                <li data-discharge-destination-id="<?= $destination_entry->id ?>"
                                    data-label="<?= $destination_entry->name ?>"
                                    data-institution-required="<?= $destination_entry->institution_required ?>">
                                    <span class="restrict-width"><?= $destination_entry->name ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </td>
                    <td class="follow-up-options-discharge-transfer-only" style="display: none;">
                        <input type="search" placeholder="Institution" class="search" id="transfer-to-search" name="search_institution"/>
                        <ul class="add-options" id="discharge-transfer-to-options">
                        </ul>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/template" id="<?= $model_name . '_followup_entry_template' ?>" style="display: none">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\ClinicOutcomeEntry();
    $this->renderPartial(
        'ClinicOutcomeEntry_event_edit',
        [
            'entry' => $empty_entry,
            'form' => $form,
            'model_name' => $model_name,
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'row_count' => '{{row_count}}',
            'patient_ticket' => false,
            'values' => [
                'status_id' => '{{status_id}}',
                'status' => '{{status}}',
                'discharge_status_id' => '{{discharge_status_id}}',
                'discharge_status' => '{{discharge_status}}',
                'discharge_destination_id' => '{{discharge_destination_id}}',
                'discharge_destination' => '{{discharge_destination}}',
                'transfer_institution_id' => '{{transfer_institution_id}}',
                'transfer_to' => '{{transfer_to}}',
                'followup_quantity' => '{{followup_quantity}}',
                'followup_period_id' => '{{followup_period_id}}',
                'followup_period' => '{{followup_period}}',
                'followup_comments' => '{{followup_comments}}',
                'followup_comments_display' => '{{followup_comments_display}}',
                'role_id' => '{{role_id}}',
                'role' => '{{role}}',
                'risk_status_id' => '{{risk_status_id}}',
                'risk_status_class' => '{{risk_status_class}}',
                'risk_status_content' => '{{risk_status_content}}',
                'is_template' => true
            ],
        ]
    );
    ?>
</script>
<script type="text/template" id="<?= $model_name . '_patient_ticket_entry_template' ?>" style="display: none">
    <?php
    $empty_entry = new \OEModule\OphCiExamination\models\ClinicOutcomeEntry();
    $this->renderPartial(
        'ClinicOutcomeEntry_event_edit',
        [
            'entry' => $empty_entry,
            'form' => $form,
            'model_name' => $model_name,
            'field_prefix' => $model_name . '[entries][{{row_count}}]',
            'ticket_api' => $ticket_api ? $ticket_api : null,
            'queues' => $queues,
            'ticket' => $ticket,
            'row_count' => '{{row_count}}',
            'patient_ticket' => true,
            'values' => [
                'id' => '',
                'status_id' => '{{status_id}}',
                'status' => '{{status}}',
            ],
            'is_template' => true
        ]
    );
    ?>
</script>
<script type="text/javascript">
    $(function () {
        setUpAdder(
            $('#add-to-follow-up'),
            null,
            function () {
                $('.OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome').data('controller').onAdderDialogReturn();
            },
            $('#show-follow-up-popup-btn'),
            $('#add-followup-btn'),
            $('#add-to-follow-up').find('.close-icon-btn')
        );

        if ($('#div_OEModule_OphCiExamination_models_Element_OphCiExamination_ClinicOutcome_patientticket').length) {
            $('#followup-outcome-options li[data-patient-ticket="1"]').hide();
        }
    });
</script>
