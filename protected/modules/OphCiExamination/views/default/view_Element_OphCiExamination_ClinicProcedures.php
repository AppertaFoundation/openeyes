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
Yii::app()->clientScript->registerScriptFile("{$this->assetPath}/js/ClinicProcedures.js", CClientScript::POS_HEAD);
$past_procedures = $this->getPastClinicProcedures();
?>

<div class="element-data full-width">
    <div class="cols-full">
        <table class="cols-full last-page">
            <colgroup>
                <col class="cols-4">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-3">
            </colgroup>
            <tbody>
            <?php foreach ($element->entries as $entry) { ?>
            <tr>
                <td>
                    <?= $entry->procedure->term ?>
                    <i class="oe-i info small pad-left js-has-tooltip" data-tt-type="basic"
                       data-tooltip-content="<?= $entry->usermodified->getFullname() ?>"></i>
                </td>
                <td class="nowrap">
                    <?php $this->widget('EyeLateralityWidget', ['laterality' => $entry->eye]) ?>
                </td>
                <td>
                    <span class="oe-date">
                        <?= Helper::formatFuzzyDate($entry->date) ?>
                    </span>
                </td>
                <td>
                    <small>at</small>
                    <?= $entry->outcome_time ?>
                </td>
                <td class="align-left">
                    <?php if ($entry->comments !== null) { ?>
                    <i class="oe-i comments-who small pad-right js-has-tooltip"
                       data-tt-type="basic"
                       data-tooltip-content="<small>User comment by </small><br /><?= $entry->usermodified->getFullname() ?>">
                    </i>
                    <span class="user-comment"><?= $entry->comments ?></span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
    <hr class="divider">
    <div class="collapse-data" id="past_clinic_procedures">
        <div class="collapse-data-header-icon expand">
            Previous clinic procedures
        </div>
        <div class="collapse-data-content" style="display: none;">
            <?php foreach ($past_procedures as $procedure) {
                $this->renderPartial(
                    'ClinicProcedures_past_procedures',
                    [
                        'procedure_entries' => OphCiExamination_ClinicProcedures_Entry::model()->findAll('element_id = ?', [$procedure['id']]),
                    ]
                );
            } ?>
        </div>
    </div>
</div>
