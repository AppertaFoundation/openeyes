<?php
/**
 * (C) OpenEyes Foundation, 2023
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2023, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-7">

    <?php if (!$complications) : ?>
        <div class="row divider">
            <div class="alert-box issue"><b>No results found</b></div>
        </div>
    <?php endif; ?>

    <div class="row divider">
        <form id="procedures_search" method="post">
            <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
            <table class="cols-full">
                <colgroup>
                    <col class="cols-10">
                    <col class="cols-1">
                    <col class="cols-1">
                </colgroup>

                <tbody>
                <tr class="col-gap">
                    <td>
                        <?=\CHtml::textField(
                            'search[query]',
                            $search['query'],
                            [
                                'class' => 'cols-full',
                                'placeholder' => "Name",
                                'data-test' => 'post-op-complication-admin-search',
                            ]
                        ); ?>
                    </td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'search[active]',
                            $search['active'],
                            [
                                1 => 'Only Active',
                                0 => 'Exclude Active',
                            ],
                            ['empty' => 'All']
                        ); ?>
                    </td>
                    <td>
                        <button id="et_search" class="blue hint" type="submit" data-test="post-op-complication-admin-search-btn">
                            Search
                        </button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>

    <form id="admin_benefits" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>

        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>ID</th>
                <th>Name</th>
                <th>Active</th>
            </tr>
            </thead>

            <tbody>
            <?php
            foreach ($complications as $key => $complication) { ?>
                <tr id="$key" class="clickable" data-id="<?= $complication->id ?>"
                    data-uri="oeadmin/PostOpComplication/edit/<?= $complication->id ?>?returnUri="
                    data-test="post-op-complication-admin-row">
                    <td>
                        <?php if ($this->isComplicationDeletable($complication)) : ?>
                            <input type="checkbox" name="select[]" value="<?=$complication->id ?>" id="select[<?=$complication->id ?>]"/>
                        <?php endif; ?>
                    </td>
                    <td><?=$complication->id ?></td>
                    <td><?=$complication->name ?></td>
                    <td>
                        <?= ($complication->active ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>')); ?>
                    </td>
                </tr>
            <?php } ?>

            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?=\CHtml::submitButton(
                        'Add',
                        [
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/PostOpComplication/edit',
                            'data-test' => 'post-op-complication-admin-add-btn',
                            'name' => 'add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?=\CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete',
                            'data-object' => 'benefit',
                            'data-uri' => '/oeadmin/PostOpComplication/delete',
                            'data-test' => 'post-op-complication-admin-delete-btn',
                            'id' => 'et_delete'
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

