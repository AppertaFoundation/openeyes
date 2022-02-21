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
        <h2>Address</h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Email</td>
            <td class="cols-full">
                <?= \CHtml::activeEmailField(
                    $model->contact,
                    'email',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Address1</td>
            <td class="cols-full">
                <?= \CHtml::activeTextArea(
                    $model,
                    'address1',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Address2</td>
            <td>
                <?= \CHtml::activeTextArea(
                    $model,
                    'address2',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>City</td>
            <td>
                <?= \CHtml::activeTextArea(
                    $model,
                    'city',
                    ['class' => 'cols-full autosize',
                        'style' => 'overflow: hidden; ']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Postcode</td>
            <td>
                <?= \CHtml::activeTextArea(
                    $model,
                    'postcode',
                    ['class' => 'cols-full autosize',
                        'style' => 'overflow: hidden; ']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>County</td>
            <td>
                <?= \CHtml::activeTextArea(
                    $model,
                    'county',
                    ['class' => 'cols-full autosize',
                        'style' => 'overflow: hidden; ']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Country</td>
            <td>
                <?= \CHtml::activeDropDownList(
                    $model,
                    'country_id',
                    CHtml::listData(
                        Country::model()->findAll(),
                        'id',
                        'name',
                        'code
                        '
                    ),
                    [
                        'empty' => 'None',
                        'options' => array(Address::model()->getDefaultCountryId() => array('selected' => true)),
                        'class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Date Start</td>
            <td>
                <?php echo $form->datePicker($model, 'date_start', array(), array('nowrapper' => true, 'null' => true)) ?>
            </td>
        </tr>
        <tr>
            <td>Date End</td>
            <td>
                <?php echo $form->datePicker($model, 'date_end', array(), array('nowrapper' => true, 'null' => true)) ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
