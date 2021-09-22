<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-5">
    <form id="admin_<?= get_class(DrawingTemplate::model()); ?>s"> <!-- the appended 's' needs for the delete logic ... -->
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard generic-admin sortable" id="et_sort" data-uri="/OphCiExamination/admin/FreehandDraw/sort">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Order</th>
                <th>Display Order</th>
                <th>Active</th>
            </tr>
            </thead>
            <colgroup>
                <col class="cols-1">
                <col class="cols-1">
                <col class="cols-4">
                <col class="cols-2">
                <col class="cols-1">
            </colgroup>
            <tbody>
                <?php $this->renderPartial('/FreehandDraw/_template_row', array('templates' => $templates)) ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="3">
                    <?=\CHtml::htmlButton(
                        'Add',
                        [
                            'data-uri' => '/OphCiExamination/admin/FreehandDraw/create',
                            'class' => 'button large',
                            'id' => 'et_add',
                            'formmethod' => 'get',
                        ]
                    ); ?>

                    <?= \CHtml::htmlButton(
                        'Delete',
                        [
                            'class' => 'red hint',
                            'name' => 'delete',
                            'id' => 'et_delete',
                            'data-object' => 'DrawingTemplate',
                            'data-uri' => '/OphCiExamination/ExaminationAdmin/freehandDraw/delete',
                        ]
                    ); ?>
                </td>
                <td colspan="2">
                    <?php $this->widget(
                        'LinkPager',
                        ['pages' => $pagination]
                    ); ?>
                </td>
            </tr>
            </tfoot>
        </table>
</div>
<script>
    ready(() => {
        console.log($('.generic-admin.sortable tbody'));
        $('.generic-admin.sortable tbody').sortable({
            stop: OpenEyes.Admin.saveSorted
        });
    });
</script>