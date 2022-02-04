<?php

/**
 * OpenEyes.
 *
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

<div class="cols-full">
    <div class="row divider">
        <h2>Edit Unique Codes</h2>
    </div>

    <?php echo $this->renderPartial('//admin/_form_errors', array('errors' => $errors))?>

    <form method = "POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-2">
                <col class="cols-full">
            </colgroup>

            <tbody>
            <tr>
                <td>Code</td>
                <td> <?=\CHtml::activeTextField(
                    $unique_code,
                    'code',
                    [
                        'class' => 'cols-full',
                        'autocomplete' => Yii::app()->params['html_autocomplete']
                    ]
                ); ?></td>
            </tr>
            <tr>
                <td>Active</td>
                <td>
                    <?=\CHtml::activeCheckBox($unique_code, 'active'); ?>
                </td>
            </tr>
            </tbody>

            <tfoot>
            <tr>
                <td colspan="5">
                    <?=\CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ); ?>
                    <?=\CHtml::submitButton(
                        'Cancel',
                        [
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/uniqueCodes/list',
                            'name' => 'cancel',
                            'id' => 'et_cancel'
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
