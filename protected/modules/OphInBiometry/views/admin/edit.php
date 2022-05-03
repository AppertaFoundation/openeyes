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

<div class="row divider">
    <h2><?= $lensType_lens->id ? 'Edit' : 'Add' ?> Lens type</h2>
</div>

<?= $this->renderPartial('//admin/_form_errors', array('errors' => $errors)) ?>

<div class="cols-5">
    <form method="POST">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">
            <colgroup>
                <col class="cols-4">
                <col class="cols-5">
            </colgroup>
            <tbody>

            <?php
            $htmlOptions = ['class' => 'cols-full', 'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete')];

            foreach (['name', 'display_name', 'description', 'position_id', 'comments', 'acon', 'sf', 'pACD', 'a0', 'a1', 'a2'] as $field) {
                if ($field === "position_id") { ?>
                    <tr>
                        <td><?= $lensType_lens->getAttributeLabel($field); ?></td>
                        <td><?= \CHtml::activeDropDownList($lensType_lens, $field, CHtml::listData(OphInBiometry_Lens_Position::model()->findAll(array('order' => 'display_order')), 'id', 'name'), ['class' => 'cols-full', 'empty' => '- Select Grade -']) ?></td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td><?= $lensType_lens->getAttributeLabel($field); ?></td>
                        <td><?= \CHtml::activeTextField($lensType_lens, $field, $htmlOptions); ?></td>
                    </tr>
                <?php }
            } ?>

            <tr>
                <td>Active</td>
                <td>
                    <?= \CHtml::activeCheckBox($lensType_lens, 'active'); ?>
                </td>
            </tr>

            </tbody>
            <tfoot>
            <tr>
                <td colspan="5">
                    <?= \CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button small button',
                            'name' => 'save',
                            'id' => 'et_save'
                        ]
                    ); ?>
                    <?= \CHtml::submitButton(
                        'Cancel',
                        [
                            'class' => 'warning button small',
                            'data-uri' => '/OphInBiometry/lensTypeAdmin/list',
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
