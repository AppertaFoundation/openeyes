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

<?php foreach (Yii::app()->user->getFlashes() as $message) { ?>
<p class="alert-box info" style="margin-bottom: 0px;"><?= $message ?></p>
<?php } ?>

<?php
if (isset($model)) {
    echo \CHtml::errorSummary(
      $model,
      null,
      null,
      ["class" => "alert-box alert with-icon"]
    );
}
?>
<h2>Manage Subspecialty Subsection Assignments</h2>
<div class="cols-5">
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
                    <?= \CHtml::dropDownList(
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
                        ])
?>
                    </td>
                </tr>
                <tr>
                    <td><h3>Subsection: </h3></td>
                    <td>
                    <?= \CHtml::dropDownList(
                        'subsection',
                        $subsection_id,
                        CHtml::listData(
                            SubspecialtySubsection::model()->findAll('subspecialty_id = :specid', [':specid' => $subspecialty_id]),
                            'id',
                            'name',
                            'subspecialtySubsection.name'
                        ),
                        [
                            'id' => 'subsection-select',
                            'empty' => 'Select a subsection'
                        ])
?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php if ($subsection_id && !empty($subsection_id)) { ?>
        <table class="standard generic-admin">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Procedure</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach (ProcedureSubspecialtySubsectionAssignment::model()->findAll('subspecialty_subsection_id = :subid', [':subid' => $subsection_id]) as $model) { ?>
                <tr>
                    <td><input type="checkbox" name="select[]" value="<?= $model->id ?>"/></td>
                    <td><?= $model->getRelated('proc')->term ?></td>
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
                <?= \CHtml::button(
                    'Delete',
                    [
                        'class' => 'button large',
                        'name' => 'delete',
                        'data-object' => 'subspecialty_section_assignments',
                        'data-uri' => '/oeadmin/SubspecialtySubsectionAssignment/delete',
                        'id' => 'et_delete'
                    ]
                ); ?>
                </tr>
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
                            'id' => 'procedure-add',
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
    $('#subspecialty-select').change( e => {
        window.location.href = 'list?subspecialty_id=' + e.target.value;
    });
    $('#subsection-select').change( e => {
        window.location.href = 'list?' + e.target.baseURI.split('?')[1].split('&')[0] + '&subsection_id=' + e.target.value;
    });
    $('#procedure-add').change( e => {
        window.location.href = 'add?' + e.target.baseURI.split('?')[1] + '&procedure_id=' + e.target.value;
    });
</script>
