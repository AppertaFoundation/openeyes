<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2019
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2019, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */
?>
<div class="row divider">
    <h2>Drug Set</h2>
</div>


<div class="row divider">
    <form id="drug_set_search" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>
        <table class="cols-8">
            <colgroup>
                <col class="cols-6">
                <col class="cols-2">
                <col class="cols-2">
                <col class="cols-2">
            </colgroup>

            <tbody>
            <tr class="col-gap">
                <td>
                    <?= \CHtml::textField(
                        'search[query]',
                        $search['query'],
                        ['class' => 'cols-full', 'placeholder' => "Id, Name"]
                    ); ?>
                </td>
                <td><?=\CHtml::dropDownList('search[subspecialty_id]', $search['subspecialty_id'],
                        \CHtml::listData(Subspecialty::model()->findAll(), 'id', 'name'),
                            ['empty' => '- Subspecialty -']
                        )?>
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
                    <button class="blue hint" type="submit" id="et_search">Search</button>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<div class="cols-5">
    <form id="admin_DrugSets">
    <table class="standard">
        <colgroup>
            <col class="cols-1">
            <col class="cols-1">
            <col class="cols-3">
            <col class="cols-2">
            <col class="cols-1">
        </colgroup>
        <thead>
        <tr>
            <th><?= \CHtml::checkBox('selectall'); ?></th>
            <th>Id</th>
            <th>Name</th>
            <th>Subspecialty</th>
            <th>Active</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($drug_sets as $set) : ?>
            <tr class="clickable js-clickable" data-id="<?php echo $set->id ?>"
                data-uri="OphDrPrescription/admin/DrugSet/edit/<?= $set->id ?>">
                <td>
                    <input type="checkbox" name="DrugSet[id][]" value="<?= $set->id ?>"/>
                </td>
                <td><?= $set->id; ?></td>
                <td><?= $set->name; ?></td>
                <td><?= $set->subspecialty->name; ?></td>
                <td><i class="oe-i <?=($set->active ? 'tick' : 'remove');?> small"></i></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::submitButton('Add', [
                            'id' => 'et_add',
                            'data-uri' => "/OphDrPrescription/admin/drugSet/edit",
                            'class' => 'button large'
                    ]);?>
                    <?=\CHtml::submitButton('Delete', [
                        'id' => 'et_delete',
                        'data-uri' => '/OphDrPrescription/admin/drugSet/delete',
                        'class' => 'button large',
                        'data-object' => 'DrugSet'
                    ]);?>
                </td>
            </tr>
        </tfoot>
    </table>
    </form>
</div>