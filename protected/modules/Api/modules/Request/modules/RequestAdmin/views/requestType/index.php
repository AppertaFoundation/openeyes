<?php
/**
 * (C) Copyright Apperta Foundation 2020
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.openeyes.org.uk
 *
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (C) 2020, Apperta Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
 */

/* @var $this RequestTypeController */
/* @var $dataProvider CActiveDataProvider */

$primary_key = $dataProvider->model->tableSchema->primaryKey; ?>

<div class="row divider">
    <h2><?= $title; ?></h2>
</div>
<?php if (!$dataProvider->getItemCount()) : ?>
    <div class="row divider">
        <div class="alert-box issue"><b>No results found</b></div>
    </div>
<?php endif; ?>

<div class="cols-12">
    <form id="admin_request" method="post">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?= Yii::app()->request->csrfToken ?>"/>

        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall"/></th>
                <?php foreach ($dataProvider->model->getAttributes() as $field => $value) : ?>
                    <th><?= $dataProvider->model->getAttributeLabel($field) ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($dataProvider->getData() as $key => $item) : ?>
                <tr id="<?= $key; ?>" class="clickable" data-id="<?= $item->{$primary_key} ?>"
                    data-uri="Api/Request/admin/<?= $this->id; ?>/edit?id=<?= urlencode($item->{$primary_key}); ?>&returnUri=">
                    <td>
                        <input type="checkbox" name="select[]" value="<?= $item->{$primary_key} ?>"
                               id="select[<?= $item->{$primary_key} ?>]"/>
                    </td>

                    <?php foreach ($dataProvider->model->getAttributes() as $field => $value) : ?>
                        <td><?= $item->{$field} ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="12"><?= \OEHtml::addButton('Add', [
                        'data-uri' => '/Api/Request/admin/' . $this->id . '/add',
                    ]) ?>
                </td>
            </tr>

            </tfoot>
        </table>
    </form>
</div>


