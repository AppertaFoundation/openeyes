<?php
/**
 * OpenEyes.
 *
 * (C) OpenEyes Foundation, 2016
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2016, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="row divider">
    <h2><?php echo $model->isNewRecord ? 'Create' : 'Edit'; ?> No Treatment Reason</h2>
</div>

<?php echo $form->errorSummary($model); ?>

<table class="standard cols-full">
    <tbody>
    <tr>
        <td>Name</td>
        <td >
            <?=\CHtml::activeTextField(
                $model,
                'name',
                [
                    'class' => 'cols-full',
                    'autocomplete' => Yii::app()->params['html_autocomplete']
                ]
            ); ?>
        </td>
    </tr>
    <tr>
        <td>Correspondence</td>
        <td>
            <?=\CHtml::activeTextField(
                $model,
                'letter_str',
                [
                    'class' => 'cols-full',
                    'autocomplete' => Yii::app()->params['html_autocomplete']
                ]
            ); ?>
        </td>
    </tr>
    </tbody>
</table>
