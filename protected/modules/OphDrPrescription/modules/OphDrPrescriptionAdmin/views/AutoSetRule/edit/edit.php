<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<h2>Add Automatic Set Rules</h2>

<form id="medication-autoset-form" method="post">

    <?=\CHtml::errorSummary(
        array_merge([$set], $set->medicationSetRules),
        null,
        null,
        ["class" => "alert-box alert with-icon"]
    ); ?>

    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
    <div class="row divider flex-layout flex-top col-gap">
        <div class="cols-left">
            <table class="large">
                <tbody>
                <tr>
                    <td>Name</td>
                    <td>
                        <?= \CHtml::activeTextField($set, 'name', [
                            'class' => 'cols-full',
                            'autocomplete' => \Yii::app()->params['html_autocomplete']
                        ])
                        ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="cols-right">
            <table class="large">
                <tbody>
                <tr>
                    <td>Hidden/system:</td>
                    <td>
                        <?= \CHtml::activeRadioButtonList($set, 'hidden', [1 => 'Hidden/system set', 0 => 'Visible Set'], [
                            'template' => "{beginLabel}{input}{labelTitle}{endLabel}",
                            'separator' => ' ',
                            'container' => '',
                            'labelOptions' => ['class' => 'inline highlight'],
                        ]); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?= $this->renderPartial('../DrugSet/_usage_rules', ['medication_set' => $set]); ?>
    <div class="row divider">
        <?php $this->renderPartial('edit/edit_attributes', ['set' => $set]); ?>
    </div>

    <div class="row divider">
        <?php $this->renderPartial('edit/edit_set_membership', ['set' => $set]); ?>
    </div>

    <div class="row divider">
        <?php $this->renderPartial('edit/edit_individual_medications', ['set' => $set]); ?>
    </div>

    <?= \OEHtml::submitButton() ?>
    <?= \OEHtml::cancelButton("Cancel", [
        'data-uri' => '/OphDrPrescription/admin/AutoSetRule/index',
    ]) ?>
</form>
