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

<div class="cols-5">
    <form id="admin_<?= get_class(OphCoDocument_Sub_Types::model()); ?>">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard generic-admin sortable">
            <thead>
            <tr>
                <th>Name</th>
                <th>Order</th>
                <th>Display Order</th>
                <th>Active</th>
            </tr>
            </thead>
            <colgroup>
                <col class="cols-1">
                <col class="cols-5">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <tbody>
                <?php $this->renderPartial('/admin/sub_types/_sub_types', array('sub_types' => $sub_types)) ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?=\CHtml::button(
                        'Add',
                        [
                            'data-uri' => '/OphCoDocument/oeadmin/documentSubTypesSettings/create',
                            'class' => 'button large',
                            'id' => 'et_add',
                            'formmethod' => 'get',
                        ]
                    ); ?>

                    <?=\CHtml::submitButton(
                        'Save',
                        [
                            'class' => 'button large primary event-action',
                            'name' => 'save',
                            'id' => 'et_admin-save',
                            'formmethod' => 'post',
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
