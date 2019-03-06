<?php
/**
 * OpenEyes.
 *
 * (C) Moorfields Eye Hospital NHS Foundation Trust, 2008-2011
 * (C) OpenEyes Foundation, 2011-2012
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2011-2012, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="cols-5">

    <form id="admin_users">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken ?>"/>
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Full name</th>
            </tr>
            </thead>

            <tbody>
            <?php
            if (isset($operators['items'])){
            foreach ($operators['items'] as $i => $operator) { ?>
                <tr class="clickable" data-id="<?php echo $operator->id ?>"
                    data-uri="OphTrLaser/admin/editLaserOperator/<?php echo $operator->id ?>">
                    <td><input type="checkbox" name="operators[]" value="<?php echo $operator->id ?>"/></td>
                    <td><?php echo $operator->operator->fullName ?></td>
                </tr>
            <?php } } ?>
            </tbody>

            <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?=\CHtml::submitButton(
                        'Add',
                        [
                            'class' => 'button large',
                            'name' => 'add_operator',
                            'id' => 'et_add_operator'
                        ]
                    ); ?>
                    <?=\CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete_operator',
                            'data-object' => 'users',
                            'id' => 'et_delete_operator'
                        ]
                    ); ?>
                    <?php echo $this->renderPartial('_pagination', array(
                        'pagination' => $pagination,
                    )) ?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
