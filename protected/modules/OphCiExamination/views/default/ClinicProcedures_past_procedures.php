<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

use OEModule\OphCiExamination\models\OphCiExamination_ClinicProcedures_Entry;

?>

<table class="cols-full last-left">
    <colgroup>
        <col class="cols-4">
        <col class="cols-1">
        <col class="cols-2">
        <col class="cols-1">
        <col class="cols-1">
        <col class="cols-3">
    </colgroup>
    <tbody>
    <?php foreach ($procedure_entries as $procedure) { ?>
    <tr>
        <td>
            <?= $procedure->procedure->term ?>
            <i class="oe-i info small pad-left js-has-tooltip"
               data-tt-type="basic"
               data-tooltip-content="<?= $procedure->usermodified->getFullname() ?>"></i>
        </td>
        <td class="nowrap">
            <?php $this->widget('EyeLateralityWidget', ['laterality' => $procedure->eye]) ?>
        </td>
        <td>
            <?= $procedure->subspecialty->name ?>
        </td>
        <td>
            <span class="oe-date">
                <?= Helper::formatFuzzyDate($procedure->date) ?>
            </span>
        </td>
        <td class="nowrap">
            <small>at</small>
            <?= $procedure->outcome_time ?>
        </td>
        <td class="align-left">
            <?php if ($procedure->comments !== null) { ?>
                <i class="oe-i comments-who small pad-right js-has-tooltip"
                   data-tt-type="basic"
                   data-tooltip-content="<small>User comment by </small><br /><?= $procedure->usermodified->getFullname() ?>">
                </i>
                <span class="user-comment"><?= $procedure->comments ?></span>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
    </tbody>
</table>
