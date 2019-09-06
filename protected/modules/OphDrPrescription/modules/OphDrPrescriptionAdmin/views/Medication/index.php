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
<div class="row divider">
    <h2>Medications</h2>
</div>

<div class="row divider">
    <form id="medical_set_search" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>

        <hr class="">

        <table class="cols-8">
            <colgroup>
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-3">
                <col class="cols-3">
            </colgroup>

            <tbody>
            <tr class="col-gap">
                <td>
                    <?= \CHtml::textField(
                        'search[source_type]',
                        $search['source_type'],
                        [
                            'class' => 'cols-full js-search-data',
                            'placeholder' => 'Source Type',
                            'data-name' => 'source_type'
                        ]
                    ); ?>
                </td>
                <td>
                    <?= \CHtml::textField(
                        'search[source_subtype]',
                        $search['source_subtype'],
                        [
                            'class' => 'cols-full js-search-data',
                            'placeholder' => 'Source Subtype',
                            'data-name' => 'source_subtype'
                        ]
                    ); ?>
                </td>
                <td>
                    <?= \CHtml::textField(
                        'search[preferred_code]',
                        $search['preferred_code'],
                        [
                            'class' => 'cols-full js-search-data',
                            'placeholder' => 'Preferred Code',
                            'data-name' => 'preferred_code'
                        ]
                    ); ?>
                </td>
                <td>
                    <?= \CHtml::textField(
                        'search[preferred_term]',
                        $search['preferred_term'],
                        [
                            'class' => 'cols-full js-search-data',
                            'placeholder' => 'Preferred Term',
                            'data-name' => 'preferred_term'
                        ]
                    ); ?>
                </td>

                <td>
                    <button class="blue hint" type="button" id="et_search">Search</button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<div class="cols-12">
    <form id="admin_medication_sets">
        <table id="medication-list" class="standard">
            <colgroup>
                <col style="width:1%">
                <col style="width:4%">
                <col style="width:8%">
                <col style="width:8%">
                <col style="width:10%">
                <col class="cols-2">
                <col style="width:10%">
                <col class="cols-2">
                <col class="cols-2">
                <col style="width:1%">
            </colgroup>
            <thead>
            <tr>
                <th><?= \CHtml::checkBox('selectall'); ?></th>
                <th>Id</th>
                <th>Source Type</th>
                <th>Source Subtype</th>
                <th>Preferred Code</th>
                <th>Preferred Term</th>
                <th>VTM Terms</th>
                <th>VMP Terms</th>
                <th>AMP Terms</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data_provider->getData() as $set) {
                    $this->renderPartial('/Medication/_row', ['set' => $set]);
                }
                ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="7">
                    <?= \CHtml::submitButton('Add', [
                        'id' => 'et_add',
                        'data-uri' => "/OphDrPrescription/admin/medication/edit",
                        'class' => 'button large'
                    ]); ?>
                    <?= \CHtml::submitButton('Delete', [
                        'id' => 'delete_medication',
                        'data-uri' => '/OphDrPrescription/admin/medication/delete',
                        'class' => 'button large',
                        'data-object' => 'Medication'
                    ]); ?>
                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', ['pages' => $data_provider->pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>

<script type="text/html" id="medication_template" style="display:none">
    <tr>
        <td><input type="checkbox" value="{{id}}" name="delete-ids[]" /></td>
        <td>{{id}}</td>
        <td>{{source_type}}</td>
        <td>{{source_subtype}}</td>
        <td>{{preferred_code}}</td>
        <td>{{preferred_term}}</td>
        <td>{{vtm_term}}</td>
        <td>{{vmp_term}}</td>
        <td>{{amp_term}}</td>
        <td><a href="/OphDrPrescription/admin/Medication/edit/{{id}}" class="button">Edit</a></td>
    </tr>
</script>

<script>
    let medsController = new OpenEyes.OphDrPrescriptionAdmin.DrugSetController({
        tableSelector: '#medication-list',
        searchUrl: '/OphDrPrescription/admin/Medication/search',
        templateSelector: '#medication_template',
        deleteButtonSelector: '#delete_medication'
    });
    $('#admin_medication_sets').data('medsController', medsController);
</script>
