<?php

/**
 * (C) OpenEyes Foundation, 2020
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

<div class="cols-11">
    <?php if (!$patient_identifier_types) :?>
        <div class="row divider">
            <div class="alert-box issue"><b>No results found</b></div>
        </div>
    <?php endif; ?>
    <div class="row divider">
        <form id="patient-identifier-search-form" action="/Admin/PatientIdentifierType/index" method="get">
            <table class="standard">
                <colgroup>
                    <col class="cols-1">
                    <col class="cols-1">
                    <col class="cols-7">
                </colgroup>
                <tr>
                    <td>
                        <?= \CHtml::dropDownList(
                            'institution',
                            $search['institution'],
                            CHtml::listData(Institution::model()->findAll(), 'id', 'name'),
                            ['empty' => 'All']
                        ); ?>
                    </td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'site',
                            $search['site'],
                            CHtml::listData(Site::model()->findAllByAttributes(['institution_id' => $search['institution']]), 'id', 'name'),
                            ['empty' => 'All']
                        ); ?>
                    </td>
                    <td>
                        <button class="blue hint" id="search-button" type="submit">Search</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <form id="admin_patientidentifiertypes">
        <table class="standard">
            <thead>
            <tr>
            <th><input type="checkbox" id="checkall" class="patient_identifier_types" /></th>
            <?php $patient_identifier_type_fields = ['id', 'institution_id', 'site_id', 'usage_type', 'short_title', 'long_title',
                'validate_regex', 'value_display_prefix', 'value_display_suffix', 'pad', 'spacing_rule', 'pas_api', 'unique_row_string']?>
            <?php foreach ($patient_identifier_type_fields as $field) { ?>
                <th><?= $element->getAttributeLabel($field) ?></th>
            <?php } ?>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($patient_identifier_types as $patient_identifier_type) { ?>
                    <tr class="clickable"
                        data-id="<?= $patient_identifier_type->id ?>"
                        data-uri="Admin/PatientIdentifierType/edit?patient_identifier_type_id=<?= $patient_identifier_type->id ?>">
                        <td><input type="checkbox" name="patient_identifier_types[]" value="<?= $patient_identifier_type->id ?>"/>
                        <td><?= $patient_identifier_type->id ?></td>
                        <td><?= $patient_identifier_type->institution ? $patient_identifier_type->institution->name : '-' ?></td>
                        <td><?= $patient_identifier_type->site ? $patient_identifier_type->site->name : '-' ?></td>
                        <td><?= $patient_identifier_type->usage_type ?></td>
                        <td><?= $patient_identifier_type->short_title ?></td>
                        <td><?= $patient_identifier_type->long_title ? $patient_identifier_type->long_title : '-' ?></td>
                        <td><?= $patient_identifier_type->validate_regex ? $patient_identifier_type->validate_regex : '-' ?></td>
                        <td><?= $patient_identifier_type->value_display_prefix ? $patient_identifier_type->value_display_prefix : '-' ?></td>
                        <td><?= $patient_identifier_type->value_display_suffix ? $patient_identifier_type->value_display_suffix : '-' ?></td>
                        <td><?= $patient_identifier_type->pad ? $patient_identifier_type->pad : '-' ?></td>
                        <td><?= $patient_identifier_type->spacing_rule ? $patient_identifier_type->spacing_rule : '-' ?></td>
                        <td>
                            <?php if (isset($patient_identifier_type->pas_api['enabled']) && $patient_identifier_type->pas_api['enabled'] === true) :?>
                                <?=\OEHtml::icon('tick-green', ['class' => 'small']);?>
                            <?php else : ?>
                                <?=\OEHtml::icon('cross-red', ['class' => 'small']);?>
                            <?php endif; ?>
                        </td>
                        <td><?= $patient_identifier_type->unique_row_string ?></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="3">
                    <?=\CHtml::button(
                        'Add Patient Identifier',
                        [
                            'class' => 'button large',
                            'id' => 'et_add',
                            'data-uri' => '/Admin/PatientIdentifierType/add',
                        ]
                    ); ?>
                    <?= \CHtml::button(
                        'Delete',
                        [
                            'class' => 'button large',
                            'data-object' => 'patientidentifiertypes',
                            'data-uri' => '/Admin/PatientIdentifierType/delete',
                            'id' => 'et_delete'
                        ]
                    ); ?>
                </td>
                <td colspan="15">
                    <?php $this->widget(
                        'LinkPager',
                        ['pages' => $pagination]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#institution').on('change', function () {
            getInstitutionSites($(this).val(), $('#site'));
        });

        $('#checkall').click(function (e) {
            $('input[name="patient_identifier_types[]"]').attr('checked', $(this).is(':checked') ? 'checked' : false);
        });
    });
</script>
