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

<?php $this->renderPartial('//base/_messages')?>

<div class="hidden" id="add-new-form" style="margin-bottom: 10px">
<?php
    $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
            'id' => 'clinical-create',
            'enableAjaxValidation' => false,
            'action' => Yii::app()->createURL($this->module->getName().'/admin/addEmailRecipient'),
    ));

    $this->endWidget();
    ?>
</div>

<div class="cols-4">
    <form id="admin_workflows">
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Name</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model_list as $i => $model) {
                ?>
                <tr class="clickable" data-id="<?php echo $model->id ?>"
                    data-uri="OphCiExamination/admin/editWorkflow/<?php echo $model->id ?>">
                    <td><input type="checkbox" name="workflows[]" value="<?php echo $model->id ?>"/></td>
                    <td data-test="workflow-name">
                        <?php echo $model->name ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::button(
                        'Add',
                        [
                            'class' => 'button large',
                            'id' => 'et_add',
                            'data-uri' => '/OphCiExamination/admin/addWorkflow',
                        ]
                    ); ?>
                    <?=\CHtml::button(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete',
                            'data-object' => 'workflows',
                            'id' => 'et_delete',
                            'data-uri' => '/OphCiExamination/admin/deleteWorkflows',
                        ]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
