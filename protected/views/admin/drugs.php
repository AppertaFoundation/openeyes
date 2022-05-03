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
<div class="box admin">
    <div class="data-group">
        <div class="cols-8 column">
            <h2>Drugs</h2>
        </div>
        <div class="cols-4 column">
            <?php
            $form = $this->beginWidget('BaseEventTypeCActiveForm', array(
                    'id' => 'searchform',
                    'enableAjaxValidation' => false,
                    'focus' => '#search',
                    'action' => Yii::app()->createUrl('/admin/drugs'),
                ))?>
                <div class="cols-12 column">
                    <input type="text" autocomplete="<?php echo SettingMetadata::model()->getSetting('html_autocomplete')?>" name="search" id="search" placeholder="Enter search query..." value="<?php echo strip_tags(@$_POST['search'])?>" />
                </div>
            <?php $this->endWidget()?>
        </div>
    </div>
    <form id="admin_users">
        <input type="hidden" name="YII_CSRF_TOKEN" value="<?php echo Yii::app()->request->csrfToken?>" />
        <table class="standard">
            <thead>
            <tr>
                <th><input type="checkbox" name="selectall" id="selectall" /></th>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Default Dose</th>
                <th>Unit</th>
                <th>Route</th>
                <th>Frequency</th>
                <th>Duration</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($drugs as $i => $drug) {?>
                <tr class="clickable" data-id="<?php echo $drug->id?>" data-uri="admin/editdrug/<?php echo $drug->id?>">
                    <td><input type="checkbox" name="drugs[]" value="<?php echo $drug->id?>" /></td>
                    <td><?php echo $drug->id?></td>
                    <td><?php echo $drug->name?></td>
                    <td><?php echo $drug->type->name?></td>
                    <td><?php echo $drug->default_dose?></td>
                    <td><?php echo $drug->dose_unit?></td>
                    <td><?php if (isset($drug->default_route)) {
                            echo $drug->default_route->name;
                        }?>
                    </td>
                    <td><?php if (isset($drug->default_frequency)) {
                            echo $drug->default_frequency->name;
                        }?>
                    </td>
                    <td><?php if (isset($drug->default_duration)) {
                            echo $drug->default_duration->name;
                        }?>
                    </td>
                </tr>
            <?php }?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td colspan="9">
                    <?php echo EventAction::button('Add', 'add', null, array('class' => 'small'))->toHtml()?>
                    <?php // echo EventAction::button('Delete', 'delete', null, array('class' => 'small'))->toHtml()?>
                    <?php echo $this->renderPartial('_pagination', array(
                            'pagination' => $pagination,
                        ))?>
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
