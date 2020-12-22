<?php
/**
 * (C) Copyright Apperta Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

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
            'id' => 'operation-checklists-select',
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
            <h3 class="element-title">Create Operation Checklist</h3>
        </header>

        <input type="hidden" name="SelectBooking"/>

        <div class="data-group">
            <div class="data-value flex-layout">
                <p>
                    <?php if (count($operations) > 0) : ?>
                        Please indicate whether this operation checklist relates to a booking or an unbooked emergency:
                    <?php else : ?>
                        There are no open bookings in the current episode so only an emergency operation checklist can be created.
                    <?php endif; ?>
                </p>
            </div>
            <br/>
            <div class="cols-8">
                <div class="data-group" style="padding-left: 100px">
                    <table class="cols-10 last-left">
                        <thead>
                        <tr>
                            <th>Booked Date</th>
                            <th>Procedure</th>
                            <th>Comments</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($operations as $operation) : ?>
                            <tr>
                                <td>
                    <span class="cols-3 column <?php echo $theatre_diary_disabled ? 'hide' : '' ?>">
                            <?php if (!$theatre_diary_disabled) {
                                if ($operation->booking) {
                                    echo $operation->booking->session->NHSDate('date');
                                }
                            } ?>
                    </span>
                                </td>
                                <td>
                                    <a href="#" class="booking-select" data-eye-id="<?= $operation->eye->id ?>"
                                       data-booking="booking<?= $operation->event_id ?>">
                                        <?php
                                        echo implode('<br />', array_map(function ($procedure) use ($operation) {
                                            return $operation->eye->name . ' ' . $procedure->term;
                                        }, $operation->procedures));
                                        ?>
                                    </a>
                                </td>
                                <td>
                                    <?= $operation->comments; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td>N/A</td>
                            <td>
                                <a href="#" class="booking-select" data-booking="emergency">Emergency / Unbooked<a>
                            </td>
                            <td></td>
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
    function selectBooking(booking) {
        $('[name="SelectBooking"]').val(booking);
        $('#operation-checklists-select').submit();
    }

    $(function () {
        $('.booking-select').on('click', function () {
            let booking = $(this).data('booking');
            selectBooking(booking);
        });
    });
</script>
<?php $this->endContent(); ?>

