<?php
/**
 * (C) OpenEyes Foundation, 2018
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>

<div class="cols-9">

    <?php if (!$benefits) : ?>
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
                                'placeholder' => "Id, Name"
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
                        <button class="blue hint"
                                type="submit"
                                id="et_search">Search</button>
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
            foreach ($benefits as $key => $benefit) { ?>
                <tr id="$key" class="clickable" data-id="<?php echo $benefit->id ?>"
                    data-uri="oeadmin/benefit/edit/<?php echo $benefit->id ?>?returnUri=">
                    <td>
                        <?php if ($this->isBenefitDeletable($benefit)) : ?>
                            <input type="checkbox" name="select[]" value="<?php echo $benefit->id ?>" id="select[<?=$benefit->id ?>]"/>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $benefit->id ?></td>
                    <td><?php echo $benefit->name ?></td>
                    <td>
                        <?php echo ($benefit->active) ?
                            ('<i class="oe-i tick small"></i>') :
                            ('<i class="oe-i remove small"></i>'); ?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

            <tfoot class="pagination-container">
            <tr>
                <td colspan="2">
                    <?= \CHtml::submitButton(
                        'Add',
                        [
                            'class' => 'button large',
                            'data-uri' => '/oeadmin/benefit/edit',
                            'name' => 'add',
                            'id' => 'et_add'
                        ]
                    ); ?>
                    <?= \CHtml::submitButton(
                        'Delete',
                        [
                            'class' => 'button large',
                            'name' => 'delete',
                            'data-object' => 'benefit',
                            'data-uri' => '/oeadmin/benefit/delete',
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
