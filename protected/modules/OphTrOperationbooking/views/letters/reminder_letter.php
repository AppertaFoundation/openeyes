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
            'toAddress' => $toAddress,
            'patient' => $patient,
            'date' => date('Y-m-d'),
            'site' => $site,
        ))?>
    </header>

    <?php echo $this->renderPartial('../letters/letter_introduction', array(
            'to' => $to,
            'accessible' => true,
            'patient' => $patient,
    ))?>

    <p class="accessible">
        I recently invited you to telephone to arrange a date for your <?php if ($patient->isChild()) {
            ?>child's <?php
                                                                       }?> admission for surgery under the care of <?=\CHtml::encode($consultantName) ?>.   I have not yet heard from you.
    </p>

    <p class="accessible">
        This is currently anticipated to be a
        <?php
        if ($overnightStay) {
            echo 'an overnight stay';
        } else {
            echo 'day case';
        }
        ?>
        procedure.
    </p>

    <p class="accessible">
        Please will you telephone <?php echo $changeContact ?> within 2 weeks of the date of this letter to discuss and agree
        a convenient date for this operation.
    </p>

    <p class="accessible">
        Should you<?php	if ($patient->isChild()) {
            ?>r child<?php
                  }?> no longer require treatment please let me know as soon as possible.
    </p>

    <?php echo $this->renderPartial('../letters/letter_end', array('accessible' => true)); ?>
</div>
