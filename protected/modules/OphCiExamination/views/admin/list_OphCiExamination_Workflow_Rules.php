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
?>

<?php $this->renderPartial('//base/_messages') ?>

<div class="hidden" id="add-new-form" style="margin-bottom: 10px">
    <?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
        'id' => 'clinical-create',
        'enableAjaxValidation' => false,
        'action' => Yii::app()->createURL($this->module->getName() . '/admin/addEmailRecipient'),
    ));

    $this->endWidget();
    ?>
</div>

<div class="cols-5">
    <div>
    <?php
    if ($this->checkAccess('admin')) {
        echo 'Institution: ' . CHtml::dropDownList(
            'institution_id',
            $institution_id,
            CHtml::listData(Institution::model()->getTenanted(), 'id', 'name'),
            array('empty' => 'All institutions', 'id' => 'js-institution-setting-filter')
        );
    } elseif ($institution_id !== null) {
        echo 'Institution: ' . Institution::model()->findByPk($institution_id)->name;
    }
    ?>
    </div>
    <form id="admin_workflowrules">
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Subspecialty</th>
                <th><?php echo Firm::contextLabel() ?></th>
                <th>Episode status</th>
                <th>Workflow</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) { ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="OphCiExamination/admin/editWorkflowRule/<?php echo $model->id ?>">
                    <td><input type="checkbox" name="workflowrules[]" value="<?php echo $model->id ?>"/></td>
                    <td><?php echo $model->subspecialty ? $model->subspecialty->name : 'All' ?></td>
                    <td><?php echo $model->firm ? $model->firm->name : 'All' ?></td>
                    <td><?php echo $model->episode_status ? $model->episode_status->name : 'All' ?></td>
                    <td data-test="workflow-name"><?php echo $model->workflow->name ?></td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?= \CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'id' => 'et_add',
                            'name' => 'add',
                            'data-uri' => '/OphCiExamination/admin/addWorkflowRule',
                        ]
                    ); ?>
                    <?= \CHtml::button(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete',
                            'data-object' => 'workflowrules',
                            'id' => 'et_delete',
                            'data-uri' => '/OphCiExamination/admin/deleteWorkflowRules',
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#js-institution-setting-filter').change(function(e) {
            let url = new URL(window.location.href);

            url.searchParams.set('institution_id', $(this).val());

            window.location = url;
        });
    });
</script>
