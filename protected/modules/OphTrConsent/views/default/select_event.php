<?php

/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>
<?php
$this->beginContent('//patient/event_container', array('no_face' => true));
$assetAliasPath = 'application.modules.OphTrOperationbooking.assets';
$this->moduleNameCssClass .= ' edit';
?>
<section class="element edit full">

    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'consent-form',
        'enableAjaxValidation' => false,
    )); ?>
    <?php $this->displayErrors($errors) ?>

    <header class="element-header">
        <h3 class="element-title">Create Consent Form</h3>
    </header>
    <input type="hidden" name="SelectBooking"/>
    <div class="element-actions"><!-- note: order is important because of Flex, trash must be last element --><!-- No icons --></div><!-- *** Element DATA in EDIT mode *** -->
    <div class="element-fields full-width">
        <div class="row large-text">
            Booked procedures without consent forms
        </div>


        <?php if ($bookings) {?>
            <table class="cols-full">
                <colgroup>
                    <col class="cols-icon">
                    <col class="cols-2">
                    <col class="cols-1">
                    <col class="cols-3">
                    <col class="cols-3">
                    <col><!-- auto -->
                </colgroup">
                <thead>
                    <tr>
                        <th><!-- blank --></th>
                        <th>Last updated</th>
                        <th>State</th>
                        <th>Procedure</th>
                        <th>Comments</th>
                        <th><!-- blank --></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $operation) { ?>
                        <?php
                            $existing_cosnent_criteria = new CDbCriteria();
                            $existing_cosnent_criteria->with = ['event'];
                            $existing_cosnent_criteria->compare('event.deleted', 0);
                            $existing_cosnent_criteria->compare('t.booking_event_id', $operation->event_id);
                            $has_consent = Element_OphTrConsent_Procedure::model()->find($existing_cosnent_criteria); ?>
                        <?php if (!$has_consent) : ?>
                            <tr>
                                <td><i class="oe-i-e i-TrOperation"></i></td>
                                <td><?= date('j M Y', strtotime($operation->last_modified_date)) ?></td>
                                <td>
                                    <?php if ($operation->booking) {
                                        echo 'Scheduled';
                                    } elseif ($operation->operation_cancellation_date) {
                                        echo 'Cancelled';
                                    } else {
                                        echo 'Unscheduled';
                                    } ?>
                                </td>
                                <td>
                                    <?php foreach ($operation->procedures as $i => $procedure) {
                                        if ($i > 0) {
                                            echo '<br />';
                                        }
                                        echo $operation->eye->name . ' ' . $procedure->term;
                                    } ?>
                                </td>
                                <td>
                                    <?php if (strcmp($operation->comments, "") != 0) : ?>
                                        <i class="oe-i comments-who small pad-right js-has-tooltip" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $operation->op_usermodified->first_name . ' ' . $operation->op_usermodified->last_name ?>"}' data-tooltip-content="<small>User comment by </small><br /><?php echo $operation->op_usermodified->first_name . ' ' . $operation->op_usermodified->last_name ?>"></i>
                                        <span class="user-comment"><?= $operation->comments; ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="booking-select"
                                            data-booking="booking<?= $operation->event_id ?>" >Create consent</button>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php } ?>
                </tbody>
            </table>
        <?php } else { ?>
            There are no open bookings in the current episode so you can only create a consent form for unbooked procedures.
        <?php } ?>

        <hr class="divider" />

        <div class="row large-text">
            Booked procedures with consent forms
        </div>


        <?php
        if ($bookings) {
            ?><table class="cols-full">
                <colgroup>
                    <col class="cols-icon">
                    <col class="cols-2">
                    <col class="cols-1">
                    <col class="cols-3">
                    <col class="cols-3">
                    <col><!-- auto -->
                </colgroup">
                <thead>
                    <tr>
                        <th><!-- blank --></th>
                        <th>Last updated</th>
                        <th>State</th>
                        <th>Procedure</th>
                        <th>Comments</th>
                        <th><!-- blank --></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $operation) { ?>
                            <?php
                                $existing_cosnent_criteria = new CDbCriteria();
                                $existing_cosnent_criteria->with = ['event'];
                                $existing_cosnent_criteria->compare('event.deleted', 0);
                                $existing_cosnent_criteria->compare('t.booking_event_id', $operation->event_id);
                                $has_consent = Element_OphTrConsent_Procedure::model()->find($existing_cosnent_criteria); ?>
                            <?php if ($has_consent) : ?>
                                <tr>
                                    <?php
                                        $existing_cosnent_criteria = new CDbCriteria();
                                        $existing_cosnent_criteria->compare('event_id', $has_consent->event_id);
                                        $existing_cosnent_criteria->addCondition('healthprof_signature_id IS NOT NULL');
                                        $signature = Element_OphTrConsent_Esign::model()->find($existing_cosnent_criteria);
                                    ?>
                                    <td><i class="oe-i-e i-CoPatientConsent"></i></td>
                                    <td><?= date('j M Y', strtotime($operation->last_modified_date)) ?></td>
                                    <td>
                                        <?php if ($signature) {
                                            echo 'Signed';
                                        } else {
                                            echo 'Unsigned';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php foreach ($operation->procedures as $i => $procedure) {
                                            if ($i > 0) {
                                                echo '<br />';
                                            }
                                            echo $operation->eye->name . ' ' . $procedure->term;
                                        } ?>
                                    </td>
                                    <td>
                                        <?php if (strcmp($operation->comments, "") != 0) : ?>
                                            <i class="oe-i comments-who small pad-right js-has-tooltip" data-tip='{"type":"basic","tip":"<small>User comment by </small><br /><?php echo $operation->op_usermodified->first_name . ' ' . $operation->op_usermodified->last_name ?>"}' data-tooltip-content="<small>User comment by </small><br /><?php echo $operation->op_usermodified->first_name . ' ' . $operation->op_usermodified->last_name ?>"></i>
                                            <span class="user-comment"><?= $operation->comments; ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td><button class="blue delete-consent-button" data-id="<?= $has_consent->event_id ?>">Delete &amp; replace consent</button></td>
                                </tr>
                            <?php endif; ?>
                    <?php } ?>
                    </tbody>
                </table>
        <?php } else { ?>
                There are no open bookings in the current episode so you can only create a consent form for unbooked procedures.
        <?php } ?>

        <hr class="divider" />
        <?php if ($no_operation_booking) { ?>
            <div class="row large-text">
                Unbooked procedures with consent forms
            </div>
            <table class = "cols-full">
                <colgroup>
                    <col class="cols-icon">
                    <col class="cols-2">
                    <col class="cols-1">
                    <col class="cols-3">
                    <col class="cols-3">
                    <col><!-- auto -->
                </colgroup>
                <thead>
                    <tr>
                        <th><!-- blank --></th>
                        <th>Last updated</th>
                        <th>State</th>
                        <th>Procedure</th>
                        <th><!-- blank --></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($no_operation_booking as $consent_event) {?>
                            <tr>
                                <td><i class="oe-i-e i-CoPatientConsent"></i></td>
                                        <td><?= date('j M Y', strtotime($consent_event->last_modified_date)) ?></td>
                                <td>
                                    <?php
                                    $existing_cosnent_criteria = new CDbCriteria();
                                    $existing_cosnent_criteria->compare('event_id', $consent_event->id);
                                    $existing_cosnent_criteria->addCondition('healthprof_signature_id IS NOT NULL');
                                    $signature = Element_OphTrConsent_Esign::model()->find($existing_cosnent_criteria);
                                    if ($signature) {
                                        echo 'Signed';
                                    } else {
                                        echo 'Unsigned';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php $proceduress = Element_OphTrConsent_Procedure::model()->findAll('event_id=' . $consent_event->id) ?>
                                    <?php foreach ($proceduress as $i => $procedures) {
                                        foreach ($procedures->procedure_assignments as $i => $procedure) {
                                            if ($i > 0) {
                                                echo '<br />';
                                            }
                                            echo $procedure->eye->name . ' ' . $procedure->proc->term;
                                        }
                                    } ?>
                                </td>
                                <td><!-- blank --></td>
                                <td><button class="blue delete-consent-button" data-id="<?= $consent_event->id ?>">Delete &amp; replace consent</button></td>
                            </tr>
                    <?php } ?>
                </tbody>
            </table>
            <hr class="divider" />
        <?php } ?>

        <div class="row large-text">
                Create New Consent for Unbooked Procedure(s)
            </div>
        <table class="cols-full">
            <tbody>
                <tr>
                    <th>Standard form</th>
                    <td><button href="#" class="booking-select" data-booking="unbooked">Create consent</button></td>
                </tr>
                <tr></tr>
            </tbody>
        </table>

        <?php if ($consent_templates) { ?>
            <table class="cols-full">
                <colgroup>
                    <col class="cols-2">
                </colgroup">
                <tbody>
                    <?php foreach ($consent_templates as $consent_template) { ?>
                        <tr>
                            <th>Form template</th>
                            <td><?= $consent_template->name ?></td>
                            <td>
                                <?php
                                    echo CHtml::openTag('label', ['class' => 'inline highlight']);
                                    echo CHtml::checkBox("template" . $consent_template->id . "[right_eye]", null, ['class' => 'js-right-eye', 'data-eye-side' => 'right']) . ' R';
                                    echo CHtml::closeTag('label');
                                ?>
                            </td>
                            <td>
                                <?php
                                    echo CHtml::openTag('label', ['class' => 'inline highlight']);
                                    echo CHtml::checkBox("template" . $consent_template->id . "[left_eye]", null, ['class' => 'js-left-eye', 'data-eye-side' => 'left']) . ' L';
                                    echo CHtml::closeTag('label');
                                ?>
                            </td>
                            <td><button class="booking-select" data-booking="template<?= $consent_template->id ?>" >Consent</button></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
    <?php $this->displayErrors($errors, true) ?>
    <?php $this->endWidget(); ?>
</section>

<script>
    $(function () {
        $('.booking-select').on('click', function () {
            if ($(this).data('booking')) {
                $('[name="SelectBooking"]').val($(this).data('booking'));
            } else if($(this).data('template')) {
                $('[name="SelectBooking"]').val($(this).data('template'));
            }
            $('#consent-form').submit();
        });
    });
</script>
<?php $this->endContent(); ?>
