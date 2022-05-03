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
<style>
    .flash-success{
        border:1px solid #1DDD50;
        background: #C3FFD3;
        text-align: center;
        padding: 7px 15px ;
        color: #000000;
        margin-bottom: 20px;
    }
    .error{
        border:1px solid #ff6666;
        background: #ffe6e6;
        text-align: center;
        padding: 7px 15px ;
        color: #000000;
        margin-bottom: 20px;
    }
</style>
<?php if (Yii::app()->user->hasFlash('success')) : ?>
    <div class="flash-success">
        <?= Yii::app()->user->getFlash('success'); ?>
    </div>

<?php endif; ?>
<?php if (Yii::app()->user->hasFlash('error')) : ?>
    <div class="error">
        <?= Yii::app()->user->getFlash('error'); ?>
    </div>

<?php endif; ?>

<div class="cols-6">

    <?php if (!$sites) :?>
    <div class="row divider">
        <div class="alert-box issue"><b>No results found</b></div>
    </div>
    <?php endif; ?>

    <div class="row divider">
        <?php
        $form = $this->beginWidget(
            'CActiveForm',
            [
                'id' => 'searchform',
                'enableAjaxValidation' => false,
                'focus' => '#search',
                'action' => Yii::app()->createUrl('/admin/sites')
            ]
        )?>

        <input type="text"
           autocomplete="<?= SettingMetadata::model()->getSetting('html_autocomplete')?>"
           name="search" id="search" placeholder="Enter search query..."
           value="<?= strip_tags(Yii::app()->request->getParam('search', ''))?>" />
        <?php $this->endWidget()?>
    </div>

    <form id="admin_institution_sites">
        <table class="standard">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Remote ID</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Primary Logo</th>
                    <th>Secondary Logo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sites as $i => $site) {?>
                    <tr class="clickable"
                        data-id="<?= $site->id?>"
                        data-uri="admin/editsite?site_id=<?= $site->id?>">
                        <td><?= $site->id?></td>
                        <td><?= $site->remote_id?></td>
                        <td><?= $site->name?></td>
                        <td>
                            <?= $site->getLetterAddress(
                                ['delimiter' => ', ']
                            )?>
                        </td>
                        <td>
                            <?php
                            if (($site->logo) && ($site->logo->primary_logo)) {
                                echo 'Custom';
                            } else {
                                echo 'Default';
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if (($site->logo) && ($site->logo->secondary_logo)) {
                                echo 'Custom';
                            } else {
                                echo 'Default';
                            }
                            ?>
                        </td>
                    </tr>
                <?php }?>
            </tbody>
            <tfoot class="pagination-container">
                <tr>
                    <td colspan="3">
                        <?=\CHtml::button(
                            'Add Site',
                            [
                                'class' => 'button large',
                                'id' => 'et_add'
                            ]
                        ); ?>
                    </td>
                    <td colspan="3">
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
