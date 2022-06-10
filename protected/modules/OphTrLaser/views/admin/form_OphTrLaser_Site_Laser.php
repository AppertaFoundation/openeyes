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

<div class="cols-5">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Name</td>
            <td class="cols-full">
                <?=\CHtml::activeTelField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Institution</td>
            <td class="cols-full">
                <?= CHtml::activeDropDownList(
                    $model,
                    'institution_id',
                    Institution::model()->getTenantedList(true),
                    ['class' => 'cols-full', 'empty' => '- Institution -'],
                ) ?>
            </td>
        </tr>
        <tr>
            <td>Site</td>
            <td>
                <?= CHtml::activeDropDownList(
                    $model,
                    'site_id',
                    Site::model()->getListForCurrentInstitution(),
                    ['empty' => '- Site -', 'class' => 'cols-full']
                ); ?>
                <?php
                $types = OphTrLaser_Type::model()->findAll();
                $typesArray = array();
                foreach ($types as $type) {
                    $typesArray[$type->id] = $type->name;
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>Type</td>
            <td>
                <?= CHtml::activeDropDownList(
                    $model,
                    'type_id',
                    $typesArray,
                    ['empty' => '- Type -', 'class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Wavelength</td>
            <td>
                <?=\CHtml::activeTelField(
                    $model,
                    'wavelength',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Active</td>
            <td>
                <?=\CHtml::activeRadioButtonList(
                    $model,
                    'active',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>








