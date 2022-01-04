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

<?php $this->renderPartial('/admin/_form_errors', $errors); ?>

<div class="cols-5">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
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
            <td>Assigned to current institution</td>
            <td class="cols-full">
                <?= CHtml::checkBox("assigned_institution", $model->hasMapping(ReferenceData::LEVEL_INSTITUTION, Institution::model()->getCurrent()->id), ['class' => 'cols-full']) ?>
            </td>
        </tr>
        </tbody>
    </table>


    <?= \OEHtml::submitButton() ?>

    <?= \OEHtml::cancelButton("Cancel", [
        'data-uri' => '/oeadmin/CommonSystemicDisorderGroup/list',
    ]) ?>

</div>