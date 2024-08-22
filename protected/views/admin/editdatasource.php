<?php

/**
 * (C) OpenEyes Foundation, 2018
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

?>

<div class="cols-5">

    <div class="row divider">
        <h2><?php echo $source->id ? 'Edit' : 'Add' ?> data source</h2>
    </div>

    <?php echo $this->renderPartial('_form_errors', array('errors' => $errors)) ?>
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'adminform',
        'enableAjaxValidation' => false,
        'focus' => '#ImportSource_name',
        'layoutColumns' => array(
            'label' => 2,
            'field' => 5,
        ),
    )) ?>


    <table class="standard cols-full">
        <colgroup>
            <col class="cols-2">
            <col class="cols-5">
        </colgroup>
        <tbody>
        <tr>
            <td>Name</td>
            <td> <?= \CHtml::activeTextField(
                $source,
                'name',
                ['class' => 'cols-full'],
                ['autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')]
            ); ?> </td>
        </tr>
        <tfoot>
        <tr>
            <td colspan="5">
                <?= \CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?= \CHtml::submitButton(
                    'Cancel',
                    [
                        'class' => 'button large',
                        'data-uri' => '/admin/datasources',
                        'name' => 'cancel',
                        'id' => 'et_cancel'
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<?php $this->endWidget() ?>
