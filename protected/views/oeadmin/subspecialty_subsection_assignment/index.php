<?php
/**
 * (C) OpenEyes Foundation, 2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<h2>Manage Subspecialty Subsection Assignments</h2>
<div class="cols-5">
    <form id="admin_Subspecialty_Section_Assignment">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>" />
        <input type="hidden" name="page" value="1" />
        <table>
            <colgroup>
                <col class="cols-6">
                <col class="cols-6">
            </colgroup>
            <tbody>
                <tr>
                    <td><h3>Subspecialty: </h3></td>
                    <td>
                    <?= \CHtml::dropDownList(
                        'subspecialty',
                        $spec_id,
                        CHtml::listData(
                            Subspecialty::model()->findAll(),
                            'id',
                            'name',
                            'subspecialty.name'
                        ),
                        [
                            'id' => 'ssa-subspecialty-select',
                            'empty' => 'Select a subspecialty'
                        ])
                    ?>
                    </td>
                </tr>
                <tr>
                    <td><h3>Subsection: </h3></td>
                    <td>
                    <?= \CHtml::dropDownList(
                        'subsection',
                        $sub_id,
                        CHtml::listData(
                            SubspecialtySubsection::model()->findAll('subspecialty_id = :specid', [':specid' => $spec_id]),
                            'id',
                            'name',
                            'subspecialtySubsection.name'
                        ),
                        [
                            'id' => 'ssa-subsection-select',
                            'empty' => 'Select a subsection'
                        ])
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php if ($sub_id && !empty($sub_id)) { ?>
        <table class="standard generic-admin sortable" id="et_sort">
            <thead>
                <tr>
                    <th>Procedure</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (ProcedureSubspecialtySubsectionAssignment::model()->findAll('subspecialty_subsection_id = :subid', [':subid' => $sub_id]) as $model) { ?>
                <tr>
                    <td><?= $model->getRelated('proc')->term ?></td>
                    <td><a href='delete?id=<?= $model->id ?>
                                &subspecialty_id=<?= $spec_id ?>
                                &subsection_id=<?= $sub_id ?>' >delete</a></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <table>
            <colgroup>
                <col class="cols-6">
                <col class="cols-6">
            </colgroup>
            <tbody>
                <tr>
                    <td><h3>Add Procedure: </h3></td>
                    <td>
                    <?= \CHtml::dropDownList(
                        'procedure',
                        null,
                        CHtml::listData(
                            Procedure::model()->findAll(),
                            'id',
                            'term',
                            'procedure.term'
                        ),
                        [
                            'id' => 'ssa-procedure-add',
                            'empty' => 'Select a procedure'
                        ])
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php } ?>
    </form>
</div>
<script>
    $('#ssa-subspecialty-select').change( e => {
        window.location.href = 'list?subspecialty_id=' + e.target.value;
    });
    $('#ssa-subsection-select').change( e => {
        window.location.href = e.target.baseURI + '&subsection_id=' + e.target.value;
    });
    $('#ssa-procedure-add').change( e => {
        window.location.href = 'add?' + e.target.baseURI.split('?')[1] + '&procedure_id=' + e.target.value;
    });
</script>
