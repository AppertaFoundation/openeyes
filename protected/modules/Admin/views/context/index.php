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
<div class="cols-12">
    <div class="row divider">
        <form id="context-search-form" action="/Admin/context/index" method="get">
            <table class="standard">
                <colgroup>
                    <col class="cols-4">
                    <col class="cols-3">
                    <col class="cols-1">
                    <col class="cols-4">
                </colgroup>
                <tr>
                    <td><?=CHtml::textField('query', $search['query'], [
                            'placeholder' => 'Search Id, PAS Code, Cost Code, Name - (all are case sensitive)',
                            'class' => 'cols-full',
                        ]) ?>
                    </td>
                    <td>
                        <?php if ($this->checkAccess('admin')) {
                            echo \CHtml::dropDownList(
                                'institution_id',
                                $search['institution_id'],
                                Institution::model()->getTenantedList(false),
                                ['empty' => 'All']
                            );
                        } else {
                            $institution = Institution::model()->findByPk($search['institution_id']);
                            echo $institution->name;
                            echo CHtml::hiddenField('institution_id', $search['institution_id']);
                        } ?>
                    </td>
                    <td>
                        <?= \CHtml::dropDownList(
                            'active',
                            $search['active'],
                            [
                                1 => 'Only Active',
                                0 => 'Exclude Active',
                            ],
                            ['empty' => 'All']
                        ) ?>
                    </td>
                    <td>
                        <button class="blue hint" id="search-button" type="submit">Search</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?=Yii::app()->request->csrfToken ?>"/>
        <table class="standard cols-full">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <th>Id</th>
                <th>PAS Code</th>
                <th>Name</th>
                <th>Institution</th>
                <th>Subspecialty</th>
                <th>Consultant</th>
                <th>Cost Code</th>
                <th>Service Enabled</th>
                <th>Context Enabled</th>
                <th>Active</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($firms as $firm) : ?>
                <tr class="clickable" data-id="<?=$firm->id ?>"
                    data-uri="Admin/context/edit/<?=$firm->id ?>">
                    <td><input type="checkbox" name="firms[]" value="<?=$firm->id ?>"/></td>
                    <td><?=$firm->id ?></td>
                    <td><?=$firm->pas_code ?></td>
                    <td data-test="firm-name"><?=$firm->name ?></td>
                    <td><?=$firm->institution->name ?? 'All Institutions' ?></td>
                    <td><?=($firm->serviceSubspecialtyAssignment) ?
                            $firm->serviceSubspecialtyAssignment->subspecialty->name : 'None' ?></td>
                    <td><?= $firm->consultant->fullName ?? 'None' ?></td>
                    <td><?=$firm->cost_code ?></td>

                    <td><?= OEHtml::icon($firm->can_own_an_episode ? 'tick' : 'remove') ?></td>
                    <td><?= OEHtml::icon($firm->runtime_selectable ? 'tick' : 'remove') ?></td>
                    <td><?= OEHtml::icon($firm->active ? 'tick' : 'remove') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="5">
                    <?=\CHtml::button(
                        'Add',
                        [
                            'data-uri' => '/Admin/context/add',
                            'class' => 'button large',
                            'name' => 'add',
                            'id' => 'et_add']
                    ); ?>
                </td>
                <td colspan="4">
                    <?php $this->widget('LinkPager', ['pages' => $pagination]); ?>
                </td>
            </tr>
            </tfoot>
        </table>
</div>