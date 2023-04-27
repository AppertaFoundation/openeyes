<?php

/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2020, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

?>

<div class="cols-5">
    <form id="admin_<?= get_class(\OEModule\OphCoMessaging\models\OphCoMessaging_Message_MessageType::model()); ?>">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="standard generic-admin sortable">
            <thead>
            <tr>
                <th>Name</th>
                <th>SubTypes</th>
                <th>Display Order</th>
                <th>Reply required</th>
            </tr>
            </thead>
            <colgroup>
                <col class="cols-1">
                <col class="cols-5">
                <col class="cols-1">
                <col class="cols-1">
            </colgroup>
            <tbody>
            <?php
            if (!empty($sub_types)) {
                foreach ($sub_types as $i => $sub_type) { ?>
                        <tr class="clickable"
                            data-id="<?= $sub_type->id ?>"
                            data-uri="OphCoMessaging/MessageSubTypesSettings/edit/<?= $sub_type->id ?>">
                            <td class="reorder">
                                <span>↑↓</span>
                                <?= CHtml::activeHiddenField($sub_type, "[$i]display_order"); ?>
                                <?= CHtml::activeHiddenField($sub_type, "[$i]id"); ?>
                            <td><?= $sub_type->name ?></td>
                            <td><?= $sub_type->display_order ?></td>
                            <td><i class="oe-i <?= ($sub_type->reply_required ? 'tick' : 'remove'); ?> small"></i></td>
                            </td>
                        </tr>
                <?php    }
            }
            ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?= CHtml::button(
                        'Add',
                        [
                            'data-uri' => 'MessageSubTypesSettings/create',
                            'class' => 'button large',
                            'id' => 'et_add',
                            'formmethod' => 'get',
                        ]
                    ); ?>

                    <?= CHtml::submitButton(
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
    </form>
</div>
