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

?>

<dic class="cols-full">
    <div class="row divider">
        <h2><?php echo $title ?></h2>
    </div>

    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <?php if ($this->action->id == 'edit') { ?>
        <tr>
            <td>Id</td>
            <td class="cols-full">
                <?= \CHtml::activeHiddenField(
                    $model,
                    'id'
                ); ?>
                <?= $model->id ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
            <td>Name</td>
            <td class="cols-full">
                <?= \CHtml::activeTextArea(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Institution</td>
            <td>
                <?=\CHtml::activeDropDownList(
                    $model,
                    'institution_id',
                    CHtml::listData(
                        Institution::model()->findAll(),
                        'id',
                        'name',
                        'institution.name'
                    ),
                    ['empty' => 'None', 'class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Site</td>
            <td>
                <?=\CHtml::activeDropDownList(
                    $model,
                    'site_id',
                    CHtml::listData(
                        Site::model()->findAll(),
                        'id',
                        'name',
                        'site.name'
                    ),
                    ['empty' => 'None', 'class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Subpecialty</td>
            <td>
                <?=\CHtml::activeDropDownList(
                    $model,
                    'subspecialty_id',
                    CHtml::listData(
                        Subspecialty::model()->findAll(),
                        'id',
                        'name',
                        'specialty.name'
                    ),
                    ['empty' => 'None', 'class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Type</td>
            <td>
                <?=\CHtml::activeDropDownList(
                    $model,
                    'type_id',
                    CHtml::listData(
                        OphTrConsent_Type_Type::model()->findAll(),
                        'id',
                        'name',
                        'type.name'
                    ),
                    ['empty' => 'None', 'class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Procedures</td>
            <td>
                <?php echo $form->multiSelectList(
                    $model,
                    'OphTrConsent_Template[procedures]',
                    'procedures',
                    'id',
                    CHtml::listData(Procedure::model()->findAll(), 'id', 'term'),
                    array(),
                    array('class' => 'cols-full', 'empty' => '-- Add --',  'nowrapper' => true)
                ) ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
