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
<div class="booking-letter">
    <header>
        <?php $this->renderPartial('../default/letter_start', array(
            'toAddress' => $to_address,
            'patient' => $patient,
            'date' => date('Y-m-d'),
            'site' => $site,
        ))?>
    </header>
    <div class="accessible">
        <?php echo $this->renderPartial('../letters/letter_introduction', array(
                'to' => $patient->salutationname,
                'patient' => $patient,
        ))?>

        <p>
            <?php if ($operation->status->name == 'Rescheduled') {?>
                I am writing to inform you that the date for your <?php echo $operation->textOperationName?> has been changed<?php if (isset($operation->cancelledBookings[0])) {
                    ?> from <?php echo date('jS F Y', strtotime($operation->cancelledBookings[0]->session_date));
                                                                  }?>, the new details are:
            <?php } else {?>
                I am pleased to confirm the date of your <?php echo $operation->textOperationName?> under care of <?php echo $firm->consultantName?>. The details are:
            <?php }?>
        </p>

        <table class="borders">
            <tr>
                <th>Date of admission:</th>
                <td><?php echo date('jS F Y', strtotime($operation->booking->session->date))?></td>
            </tr>
            <tr>
                <th>Time to arrive:</th>
                <td><?php echo date('g:ia', strtotime($operation->booking->admission_time))?></td>
            </tr>
            <tr>
                <th>Ward:</th>
                <td>
                    <?php echo $operation->booking->ward ? $operation->booking->ward->longName : 'None'?>
                </td>
            </tr>
            <tr>
                <th>Location:</th>
                <td><?=\CHtml::encode($site->name)?></td>
            </tr>
            <tr>
                <th>Consultant:</th>
                <td><?php echo $firm->consultantName?></td>
            </tr>
            <tr>
                <th>Speciality:</th>
                <td><?php echo $firm->serviceSubspecialtyAssignment->subspecialty->name?></td>
            </tr>
        </table>
        <p></p>
        <br /><br />
        <?php if (!$patient->isChild()) {?>
            <p>
                If this is not convenient or you no longer wish to proceed with surgery, please contact <?php echo $operation->refuseContact?> as soon as possible.
            </p>

            <?php if (!$operation->overnight_stay) {?>
                <p>
                    <em>This is a daycase and you will be discharged from hospital on the same day.</em>
                </p>
            <?php }?>

            <?php if ($operation->booking->showWarning('Preop Assessment')) {?>
                <p>
                    <?php echo $operation->booking->getWarningHTML('Preop Assessment')?>
                </p>
            <?php }?>
        <?php }?>

        <?php if (!$patient->isChild()) {?>
            <p>
                If you are unwell the day before admission, please contact us to ensure that it is still safe and appropriate to do the procedure.  If you do not speak English, please arrange for an English speaking adult to stay with you until you reach the ward and have been seen by a doctor and/or an anaesthetist.
            </p>
        <?php }?>

        <?php if (!$patient->isChild()) {?>
            <?php if ($operation->booking->showWarning('Prescription')) {?>
                <p>
                    <?php echo $operation->booking->getWarningHTML('Prescription')?>
                </p>
            <?php }?>
        <?php }?>
        <div class="break"><!-- **** page break ***** --></div>
        <p>To help ensure your admission proceeds smoothly, please follow these instructions:</p>
        <ul>
            <?php if ($operation->booking->showWarning('Admission Instruction')) {?>
                <li>
                    <?php echo $operation->booking->getWarningHTML('Admission Instruction')?>
                </li>
            <?php }?>
            <li>
                Bring this letter with you on date of admission
            </li>
            <?php if ($operation->booking->ward) {?>
                <li>
                    Please go directly to <?php echo $operation->booking->ward->directionsText?>
                    <?php if ($patient->isChild()) {?>
                        at the time of your child's admission
                    <?php }?>
                </li>
            <?php }?>
            <?php if (!$patient->isChild()) {?>
                <li>
                    You must not drive yourself to or from hospital
                </li>
                <?php if ($operation->booking->showWarning('Seating')) {?>
                    <li>
                        <?php echo $operation->booking->getWarningHTML('Seating')?>
                    </li>
                <?php }?>
                <?php if ($operation->booking->showWarning('Prescription charges')) {?>
                    <li>
                        <?php echo $operation->booking->getWarningHTML('Prescription charges')?>
                    </li>
                <?php }?>
            <?php }?>
        </ul>

        <?php if ($patient->isChild()) {?>
            <?php if ($operation->booking->showWarning('Child health advice')) { ?>
            <p>
                If there has been any change in your child's general health, such as a cough or cold, any infectious
                disease, or any other condition which might affect their fitness for operation,
                <?= $operation->booking->getWarningHTML('Child health advice') ?>.
            </p>
            <?php } ?>
            <p>
                If you do not speak English, please arrange for an English speaking adult to stay with you until you reach the ward and have been seen by a doctor and anaesthetist.
            </p>

            <p>
                It is very important that you let us know immediately if you are unable to keep this admission date. Please let us know by return of post, or if necessary, telephone <?php echo $operation->refuseContact?>.
            </p>
        <?php }?>

        <?php echo $this->renderPartial('../letters/letter_end')?>
    </div>
</div>
