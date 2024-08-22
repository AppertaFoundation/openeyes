<?php

/**
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/**
 * @var $coreapi CoreAPI
 */
$patientSummaryPopup = $this->createWidget(
    'application.widgets.PatientSummaryPopup',
    array(
        'patient' => $patient,
    )
);
?>
<tr id="<?=$booking->element_id ?>">
    <td>
        <div class="op-time js-diaryViewMode js-admit-time-view"><?= $time; ?></div>
        <input class="cols-10 js-diaryEditMode js-admit-time" type="time" autocomplete="off" name="admitTime_<?=$booking->element_id ?>" style="display:none" value="<?=$time;?>">
        <label class="inline highlight ">
            <input name="confirm_<?=$booking->element_id ?>" value="0" type="hidden">
            <input name="confirm_<?=$booking->element_id ?>" value="1" type="checkbox" class="js-confirmed" disabled <?=$booking->confirmed ? 'checked' : '';?>> confirmed
        </label>
        <div class="op-duration"><?= $total_duration; ?> mins</div>
    </td>
    <td><!-- patient meta data -->

        <div class="oe-patient-meta">
            <div class="patient-name">
                <a href="<?= $coreapi->generatePatientLandingPageLink($patient) ?>">
                    <span class="patient-surname"><?= strtoupper($patient->last_name) ?></span>,
                    <span class="patient-firstname"><?= "$patient->first_name ($patient->title)" ?></span>
                </a>
            </div>
            <div class="patient-details">
                <div class="hospital-number">
                    <span>ID</span>
                    <a href="<?= $coreapi->generatePatientLandingPageLink($patient) ?>"><?= $patient->getHos() ?></a>
                </div>
                <div class="patient-gender"><em>Gen</em><?= $patient->getGenderString() ?></div>
                <div class="patient-age"><em>Age</em><?= $patient->age ?></div>
            </div>
        </div><!-- .oe-patient-meta -->
        <div class="theatre-patient-icons">
<?php if ($show_patient_summary_popup) : ?>
            <div id="oe-patient-details" class="js-oe-patient" data-patient-id="<?= $patient->id ?>">
                <i class="js-patient-quick-overview eye-circle medium pad  oe-i js-worklist-btn" id="js-worklist-btn"></i>
            </div>
<?php endif; ?>
            <?php
            $warnings = $booking->operation->event->episode->patient->getWarnings();
            if ($warnings) {
                $msgs = [];
                foreach ($warnings as $warn) {
                    $msgs[] = $warn['long_msg'] . ' - ' . $warn['details'];
                } ?>
                <i class="oe-i warning medium pad js-has-tooltip"
                   data-tooltip-content="<?= implode(' / ', $msgs) ?>"></i>
            <?php } ?>
        </div>

        <?= $show_patient_summary_popup ? $patientSummaryPopup->render('application.widgets.views.PatientSummaryPopup' . 'WorklistSide', []) : '' ?>
    </td>
    <td>
        <i class="oe-i circle-<?= $operation->getComplexityColor() ?> small pad js-has-tooltip" data-tt-type="basic"
           data-tooltip-content="<?= strtoupper($operation->getComplexityCaption()) ?> complexity">
        </i>
    </td>
    <td>
        <div class="operation">[<?= Eye::methodPostFix($operation->eye_id); ?>
            ] <?= $operation->getProceduresCommaSeparated() ?></div>
        <div class="operation-details">
            <ul class="dot-list">
                <li>
                    <?php if ($operation->priority->name === 'Urgent') : ?>
                        <span class="highlighter red"><?= $operation->priority->name; ?></span>
                    <?php else : ?>
                        <?= $operation->priority->name ?>
                    <?php endif; ?>
                </li>
                    <?php foreach ($operation->anaesthetic_type as $type) :
                        echo '<li>' . $type->name . '</li>';
                        if (($this->module->showLAC()) && $type->code === 'LA' && $operation->is_lac_required == '1') :
                            echo '<li>with Cover</li>';
                        endif;
                    endforeach; ?>
                <li>
                    <div class="theatre-procedure-icons">
                        <?php if ($operation->comments_rtt) :?>
                            <i class="oe-i waiting small pad js-has-tooltip" data-tt-type="basic" data-tooltip-content="<?=$operation->comments_rtt?>"></i>
                        <?php endif;?>
                        <?php if ($operation->comments) :?>
                            <i class="oe-i comments-who small pad js-has-tooltip"
                               data-tt-type="basic"
                               data-tooltip-content="<em><?=$operation->comments?></em><br>by <?=$operation->op_user->fullName?>"></i>
                        <?php endif;?>
                        <?php if ($operation->is_golden_patient) :?>
                            <i class="oe-i star small pad js-has-tooltip" data-tt-type="basic" data-tooltip-content="Golden patient"></i>
                        <?php endif;?>
                        <?php if ($operation->named_consultant_id) :?>
                            <i class="oe-i exclamation-red small pad js-has-tooltip" data-tt-type="basic" data-tooltip-content="Consultant required"></i>
                        <?php endif;?>

                        <i class="oe-i audit-trail small pad js-has-tooltip" data-tt-type="basic" data-tooltip-content="Operation booking by </br><?=$operation->op_user->fullName?>"></i>
                    </div>
                </li>
            </ul>
        </div>
        <div class="operation-actions">
            <a target="_blank" href="<?=\Yii::app()->createUrl('/' . $event->eventType->class_name . '/whiteboard/view/' . $event->id)?>" class="button">
                <i class="oe-i whiteboard small pad-right "></i>Whiteboard
            </a>
            <a href="/OphTrOperationbooking/default/view/<?=$event->id?>" class="button">
                <i class="oe-i-e i-TrOperation pad-right"></i>
                Op-Booking
            </a>

            <?php if ($biometry) :?>
                <?php
                    $biometry_event = Event::model()->findByPk($biometry->event_id);
                    $is_automated = OphInBiometry_Imported_Events::model()->countByAttributes(['event_id' => $event->id]);
                    $al_right = "AL {$biometry->axial_length_right} mm" . (!$is_automated ? ' * AL entered manually' : '');
                    $al_left = "AL {$biometry->axial_length_left} mm" . (!$is_automated ? ' * AL entered manually' : '');
                    $k1_right = "{$biometry->k1_right} D @ {$biometry->k1_axis_right}°";
                    $k1_left = "{$biometry->k1_left} D @ {$biometry->k1_axis_left}°";
                    $k2_right = "{$biometry->k2_right} D @ {$biometry->k2_axis_right}°";
                    $k2_left = "{$biometry->k2_left} D @ {$biometry->k2_axis_left}°";

                    $delta_k_right = "ΔK {$biometry->delta_k_right} D @ {$biometry->delta_k_axis_right}°";
                    $delta_k_left = "ΔK {$biometry->delta_k_left} D @ {$biometry->delta_k_axis_left}°";

                    $acd_right = "ACD {$biometry->acd_right} mm";
                    $acd_left = "ACD {$biometry->acd_left} mm";
                ?>

                <?php if ($biometry_event) : ?>
                    <i class="oe-i-e i-InBiometry js-has-tooltip" data-tt='{"type":"bilateral","tipR":"<b class=\"fade\">Created:<\/b> <?=$biometry_event->event_date;?><br \/><b>Lens <?=$biometry->lens_right;?><\/b><br \/><b>Power <?=$biometry->iol_power_right;?><\/b><br \/><?=$biometry->formula_right;?>\/T<br \/><?=$al_right?><br \/><?=$k1_right?><br \/><?=$k2_right?><br \/><?=$delta_k_right?><br \/><?=$acd_right?>","tipL":"<b class=\"fade\">Created:<\/b> <?=$biometry_event->event_date?><br \/><b>Lens <?=$biometry->lens_left ?><\/b><br \/><b>Power <?=$biometry->iol_power_left ?><\/b><br \/><?=$biometry->formula_right?><br \/><?=$al_left?><br \/><?=$k1_left?><br \/><?=$k2_left?><br \/><?=$delta_k_left?><br \/><?=$acd_left?>","eyeIcons":true,"clickPopup":false}'></i>
                    <a href="/OphInBiometry/default/view/<?=$biometry_event->id?>" class="button"> Biometry</a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($consent_event) :?>
                <a href="/OphTrConsent/default/view/<?=$consent_event->event_id?>" class="button">
                    <i class="oe-i-e i-CoPatientConsent pad-right"></i>Consent
                </a>
            <?php endif; ?>
        </div>
    </td>
    <td class="edit-order-icon">
        <i class="oe-i menu large pad js-diaryEditMode" style="display: none"></i>
    </td>
</tr>

<?php
if ($show_patient_summary_popup) {
    $assetManager = Yii::app()->getAssetManager();
    Yii::app()->clientScript->registerScriptFile(Yii::app()->assetManager->getPublishedPathOfAlias('application.widgets.js') . '/PatientPanelPopupMulti.js');
    ?>
<script>
    $(function () {
        PatientPanel.patientPopups.init(false,<?= $patient->id?>);
    });
</script>

<?php } ?>
