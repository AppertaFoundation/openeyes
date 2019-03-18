<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2019
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
if (!empty($operation->booking)) {
    $session = $operation->booking->session;
    ?>

    <table>
        <tr>
            <td>Firm:</td>
            <td><strong><?= \CHtml::encode($session->FirmName); ?></strong></td>
        </tr>
        <tr>
            <td>Location:</td>
            <td><strong><?= \CHtml::encode($session->TheatreName); ?></strong></td>
        </tr>
        <tr>
            <td>Date of operation:</td>
            <td><strong><?= $session->NHSDate('date'); ?></strong></td>
        </tr>
        <tr>
            <td>Session time:</td>
            <td><strong><?= substr($session->start_time, 0, 5) . ' - ' . substr($session->end_time, 0, 5); ?></strong></td>
        </tr>
        <tr>
            <td>Admission time:</td>
            <td><strong><?= substr($operation->booking->admission_time, 0, 5); ?></strong></td>
        </tr>
        <tr>
            <td>Duration of operation:</td>
            <td><strong><?= $operation->total_duration . ' minutes'; ?></strong></td>
        </tr>
    </table>
<?php } ?>
