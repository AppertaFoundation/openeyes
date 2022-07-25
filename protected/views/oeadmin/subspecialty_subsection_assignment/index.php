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

/**
 * @var $model ProcedureSubspecialtySubsectionAssignment
 * @var $this SubspecialtySubsectionAssignmentController
 * @var $subsection_id int
 * @var $subspecialty_id int
 * @var $institution_id int
 */
?>

<?php foreach (Yii::app()->user->getFlashes() as $message) { ?>
<p class="alert-box info" style="margin-bottom: 0px;"><?= $message ?></p>
<?php } ?>

<?php
if (isset($model)) {
    echo CHtml::errorSummary(
        $model,
        null,
        null,
        ["class" => "alert-box alert with-icon"]
    );
}
?>
<h2>Manage Subspecialty Subsection Assignments</h2>
<div class="cols-full">
    <form id="admin_subspecialty_section_assignments">
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
                    <?= CHtml::dropDownList(
                        'subspecialty',
                        $subspecialty_id,
                        CHtml::listData(
                            Subspecialty::model()->findAll(),
                            'id',
                            'name',
                            'subspecialty.name'
                        ),
                        [
                                                          'id' => 'subspecialty-select',
                                                          'empty' => 'Select a subspecialty'
                        ]
                    )?>
                    </td>
                </tr>
                <tr>
                    <td><h3>Subsection: </h3></td>
                    <td>
                    <?= CHtml::dropDownList(
                        'subsection',
                        $subsection_id,
                        CHtml::listData(
                            SubspecialtySubsection::model()->findAll('subspecialty_id = :subspecialty_id', [':subspecialty_id' => $subspecialty_id]),
                            'id',
                            'name',
                            'subspecialtySubsection.name'
                        ),
                        [
                            'id' => 'subsection-select',
                            'empty' => 'Select a subsection'
                        ]
                    )?>
                    </td>
                </tr>
                <?php if ($this->checkAccess('admin')) { ?>
                <tr>
                    <td><h3>Institution: </h3></td>
                    <td>
                        <?= CHtml::dropDownList(
                            'institution-filter',
                            $institution_id,
                            CHtml::listData(
                                Institution::model()->getTenanted(),
                                'id',
                                'name',
                            ),
                            [
                                    'id' => 'institution-select',
                                    'empty' => 'Select an institution'
                                ]
                        ) ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php if ($subsection_id && $institution_id) { ?>
        <table class="standard generic-admin">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Procedure</th>
                    <th>Institution</th>
                </tr>
            </thead>
            <tbody>
            <?php $assignments = ProcedureSubspecialtySubsectionAssignment::model()->findAll(
                'subspecialty_subsection_id = :subsection_id AND institution_id = :institution_id',
                [':subsection_id' => $subsection_id, ':institution_id' => $institution_id]
            );
            foreach ($assignments as $model) { ?>
                <tr>
                    <td><input type="checkbox" name="select[]" value="<?= $model->id ?>"/></td>
                    <td><?= isset($model->proc) ? $model->proc->term : "" ?></td>
                    <td><?= $model->institution->name ?></td>
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
                    <td colspan="2">
                        <?= CHtml::button(
                            'Delete',
                            [
                                'class' => 'button large',
                                'name' => 'delete',
                                'data-object' => 'subspecialty_section_assignments',
                                'data-uri' => '/oeadmin/SubspecialtySubsectionAssignment/delete',
                                'id' => 'et_delete'
                            ]
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <td><h3>Add Procedure: </h3></td>
                    <td>
                    <?= CHtml::dropDownList(
                        'procedure',
                        null,
                        CHtml::listData(
                            Procedure::model()->findAll(['order' => 'term asc']),
                            'id',
                            'term',
                            'procedure.term'
                        ),
                        [
                            'id' => 'procedure-add',
                            'empty' => 'Select a procedure'
                        ]
                    )
                    ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php } ?>
    </form>
</div>
<script>
    $('#subspecialty-select').change( e => {
        window.location.href = 'list?subspecialty_id=' + e.target.value;
    });
    $('#subsection-select').change( e => {
        window.location.href = 'list?' + e.target.baseURI.split('?')[1].split('&')[0] + '&subsection_id=' + e.target.value;
    });
    $('#institution-select').change( e => {
        let tokens = e.target.baseURI.split('?')[1].split('&');
        window.location.href = 'list?' + tokens[0] + '&' + tokens[1] + '&institution_id=' + e.target.value;
    });
    $('#procedure-add').change(e => {
        window.location.href = 'add?' + e.target.baseURI.split('?')[1] + '&procedure_id=' + e.target.value;
    });
</script>
