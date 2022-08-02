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

<div class="cols-full">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-full">
        </colgroup>
        <tbody>
            <tr>
                <td>Id</td>
                <td class="cols-full">
                    <?php if ($this->action->id == 'edit') { ?>
                        <?= \CHtml::activeHiddenField(
                            $model,
                            'id'
                        ); ?>
                        <?= $model->id ?>
                    <?php } else { ?>
                        <?= \CHtml::activeTextField(
                            $model,
                            'id',
                            ['class' => 'cols-full']
                        ); ?>
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Fully Specified Name</td>
                <td class="cols-full">
                    <?= \CHtml::activeTextArea(
                        $model,
                        'fully_specified_name',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Term</td>
                <td>
                    <?= \CHtml::activeTextArea(
                        $model,
                        'term',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Aliases</td>
                <td>
                    <?= \CHtml::activeTextArea(
                        $model,
                        'aliases',
                        ['class' => 'cols-full autosize',
                            'style' => 'overflow: hidden; ']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Specialty</td>
                <td>
                    <?=\CHtml::activeDropDownList(
                        $model,
                        'specialty_id',
                        CHtml::listData(
                            Specialty::model()->findAll(),
                            'id',
                            'name',
                            'specialty.name'
                        ),
                        ['empty' => 'None', 'class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>ECDS Code</td>
                <td>
                    <?= \CHtml::activeTextArea(
                        $model,
                        'ecds_code',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>ECDS Term</td>
                <td>
                    <?= \CHtml::activeTextArea(
                        $model,
                        'ecds_term',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>ICD-10 Code</td>
                <td>
                    <?= \CHtml::activeTextArea(
                        $model,
                        'icd10_code',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>ICD-10 Term</td>
                <td>
                    <?= \CHtml::activeTextArea(
                        $model,
                        'icd10_term',
                        ['class' => 'cols-full']
                    ); ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
