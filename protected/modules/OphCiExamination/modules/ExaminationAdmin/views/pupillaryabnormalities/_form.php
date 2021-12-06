<?php

/**
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<?=\CHtml::errorSummary(
    $model,
    null,
    null,
    ["class" => "alert-box alert with-icon"]
); ?>

<div class="cols-full">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-full">
        </colgroup>
        <tbody>
        <tr>
            <td>Name</td>
            <td class="cols-full">
                <?=\CHtml::activeTextField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Active</td>
            <td class="cols-full">
                <?=\CHtml::activeCheckBox(
                    $model,
                    'active',
                    []
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>

    <?= \OEHtml::submitButton() ?>

    <?= \OEHtml::cancelButton("Cancel", [
        'data-uri' => '/OphCiExamination/admin/PupillaryAbnormalities/index/',
    ]) ?>

</div>
