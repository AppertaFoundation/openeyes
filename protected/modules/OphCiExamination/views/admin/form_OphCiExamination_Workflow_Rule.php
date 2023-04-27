<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2013
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2013, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
use OEModule\OphCiExamination\models\OphCiExamination_Workflow;

$current_institution_id = Institution::model()->getCurrent()->id;

$restrict_to_current_institution = !$this->checkAccess('admin');

$firms = $restrict_to_current_institution
    ? Firm::model()->activeOrPk($model->firm_id)->findAll('institution_id = :id', [':id' => $current_institution_id])
    : Firm::model()->activeOrPk($model->firm_id)->findAll();

$workflows = $restrict_to_current_institution
    ? OphCiExamination_Workflow::model()->findAll('institution_id = :id', [':id' => $current_institution_id])
    : OphCiExamination_Workflow::model()->findAll();
?>

<div class="row divider">
    <h2><?php echo $title ?></h2>
</div>
<div class="row divider">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-3">
            <col class="cols-5">
        </colgroup>
        <tbody>
            <tr>
                <td>Institution</td>
                <td class="cols-full">
                    <?php
                    if (!$restrict_to_current_institution) {
                        echo CHtml::dropDownList(
                            'institution_id',
                            $current_institution_id,
                            CHtml::listData(Institution::model()->getTenanted(), 'id', 'name'),
                            ['empty' => 'All institutions', 'id' => 'js-institution', 'class' => 'cols-full']
                        );
                    } else {
                        echo Institution::model()->getCurrent()->name;
                    }
                    ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<table class="standard cols-full">
    <colgroup>
        <col class="cols-3">
        <col class="cols-5">
    </colgroup>
    <tbody>
    <?php if ($form->errorSummary($model)) : ?>
        <tr>
            <td>Errors</td>
            <td class="cols-full">
                <?php echo $form->errorSummary($model) ?>
            </td>
        </tr>
    <?php endif; ?>
    
    <tr>
        <td>Context</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'firm_id',
                CHtml::listData($firms, 'id', 'nameAndSubspecialty'),
                ['class' => 'cols-full', 'id' => 'js-firm', 'empty' => '- All -']
            ); ?>
        </td>
    </tr>
    <tr>
        <td>Episode status</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'episode_status_id',
                CHtml::listData(EpisodeStatus::model()->findAll(), 'id', 'name'),
                ['class' => 'cols-full', 'empty' => '- All -']
            ) ?>
        </td>
    </tr>
    <tr>
        <td>Subspecialty</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'subspecialty_id',
                CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'),
                ['class' => 'cols-full', 'empty' => '- All -']
            ) ?>
        </td>
    </tr>
    <tr>
        <td>Workflow</td>
        <td class="cols-full">
            <?= \CHtml::activeDropDownList(
                $model,
                'workflow_id',
                CHtml::listData($workflows, 'id', 'name'),
                ['class' => 'cols-full', 'id' => 'js-workflow']
            ) ?>
        </td>
    </tr>
    </tbody>
</table>

<script type="text/javascript">
    $(document).ready(function () {
        $('#js-institution').change(function() {
            let id = $(this).val();

            $.getJSON(`/OphCiExamination/admin/getInstitutionFirms/${id}`, null, function (response) {
                const into = $('#js-firm');

                into.children('[value!=""]').remove();

                $.each(response, function (index, item) {
                    into.append(`<option value="${item.id}">${item.name}</option>`);
                });
            });

            $.getJSON(`/OphCiExamination/admin/getInstitutionWorkflows/${id}`, null, function (response) {
                const into = $('#js-workflow');

                into.empty();

                $.each(response, function (index, item) {
                    into.append(`<option value="${item.id}">${item.name}</option>`);
                });
            });
        });
    });
</script>
