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
<h2><?= $medication_set->isNewRecord ? 'Create' : 'Edit';?> Medication set</h2>
<div class="row divider"></div>
<form id="drugset-admin-form" action="/OphDrPrescription/admin/DrugSet/edit/<?=$medication_set->id;?>" method="post">
<?php if (!empty($errors)) {?>
    <div class="alert-box alert error with-icon">
        <p>Please fix the following input errors:</p>
        <ul>
            <?php foreach ($errors as $field => $errs) {?>
                <?php foreach ($errs as $err) {?>
                    <li>
                        <?php echo $err?>
                    </li>
                <?php }?>
            <?php }?>
        </ul>
    </div>
<?php }?>
    <div class="row flex-layout flex-top col-gap">
        <div class="cols-6">
            <table class="large">
                <colgroup>
                    <col class="cols-3">
                    <col class="cols-6">
                    <col class="cols-1">
                </colgroup>
                <tbody>
                <tr>
                    <td>Name</td>
                    <td>
                        <?= \CHtml::activeHiddenField($medication_set, 'id');?>
                        <?= \CHtml::activeTextField($medication_set, 'name', [
                                    'class' => 'cols-full',
                                    'placeholder' => 'Name of the set'
                            ]);
?>
                    </td>
                    <td>
                        <div class="js-spinner-as-icon" style="display:none"><i class="spinner as-icon"></i></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?= $this->renderPartial('_usage_rules', ['medication_set' => $medication_set]); ?>

<?=\CHtml::submitButton(
    'Save',
    [
        'class' => 'button large green hint',
        'name' => 'save',
        'id' => 'et_save'
    ]
); ?>
    <?=\CHtml::submitButton(
    'Cancel',
    [
        'class' => 'button large red hint',
        'data-uri' => '/OphDrPrescription/admin/DrugSet/index',
        'name' => 'cancel',
        'id' => 'et_cancel'
    ]
); ?>
    <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken?>" />
    <input type="hidden" class="js-search-data js-update-row-data" data-name="set_id" value="<?=$medication_set->id;?>" />

    <?php if (!$medication_set->isNewRecord && !$medication_set->automatic) :?>
        <div class="row divider"></div>
        <?php $this->renderPartial('/DrugSet/_meds_in_set', ['medication_set' => $medication_set, 'medication_data_provider' => $medication_data_provider]); ?>
    <?php endif; ?>

</form>
<script>
    let drugSetController = new OpenEyes.OphDrPrescriptionAdmin.DrugSetController({
        tableSelector: '#meds-list',
        searchUrl: '/OphDrPrescription/admin/DrugSet/searchmedication',
        templateSelector: '#medication_template'
    });
    $('#meds-list').data('drugSetController', drugSetController);

    let tableInlineEditController = new OpenEyes.PrescriptionAdminMedicationSet({
        tableSelector: '#meds-list',
        templateSelector: '#medication_template',
        onAjaxError: function() {
            drugSetController.refreshResult();
        }
    });

    $('#meds-list').data('tableInlineEditController', tableInlineEditController);
</script>
