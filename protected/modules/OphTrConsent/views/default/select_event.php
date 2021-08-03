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
<section class="element">

    <?php $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'consent-form',
        'enableAjaxValidation' => false,
    ));
?>
    <?php $this->displayErrors($errors) ?>

    <header class="element-header">
        <h3 class="element-title">Create Consent Form</h3>
    </header>
    <input type="hidden" name="SelectBooking"/>

    <div class="data-group">
        <div class="data-value flex-layout">
            <p>
                <?php if (count($bookings) > 0) { ?>
                    Please indicate whether this Consent Form is for an existing booking or for unbooked procedures:
                <?php } else { ?>
                    There are no open bookings in the current episode so you can only create a consent form for unbooked procedures.
                <?php } ?>
            </p>
        </div>
        <br/>
        <div class="cols-10">
            <div class="data-group" style="padding-left: 100px">
                <table class="cols-full last-left">
                    <thead>
                    <tr>
                        <th>Booked Date</th>
                        <th>Procedure</th>
                        <th>Comments</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if ($bookings) {
                        foreach ($bookings as $operation) { ?>
                            <?php $has_consent = Element_OphTrConsent_Procedure::model()->find('booking_event_id=?', [$operation->event_id]) ?>
                            <tr>
                                <td>
                                    <?php if ($operation->booking) {
                                        echo $operation->booking->session->NHSDate('date');
                                    } elseif ($operation->operation_cancellation_date) {
                                        echo 'CANCELLED';
                                    } else {
                                        echo 'UNSCHEDULED';
                                    } ?>
                                </td>
                                <td>
                                    <?php if ($has_consent) : ?>
                                    <div class="status-box amber flex-layout flex-left flex-top">
                                        <b style="padding-right:15px">already has a consent form</b>
                                    <?php endif; ?>

                                        <a href="#" class="booking-select"
                                           data-booking="booking<?= $operation->event_id ?>">
                                            <?php
                                            foreach ($operation->procedures as $i => $procedure) {
                                                if ($i > 0) {
                                                    echo '<br />';
                                                }
                                                echo $operation->eye->name . ' ' . $procedure->term;
                                            }
                                            ?>
                                        </a>
                                    <?php if ($has_consent) : ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $operation->comments; ?>
                                </td>
                            </tr>

                        <?php }
                    }
                    ?>
                    <tr>
                        <td>N/A</td>
                        <td>
                            <a href="#" class="booking-select" data-booking="unbooked"> Unbooked procedures </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php $this->displayErrors($errors, true) ?>
        <?php $this->endWidget(); ?>
</section>
<script>
    $(function () {
        $('.booking-select').on('click', function () {
            $('[name="SelectBooking"]').val($(this).data('booking'));
            $('#consent-form').submit();
        });
    });
</script>
<?php $this->endContent(); ?>
