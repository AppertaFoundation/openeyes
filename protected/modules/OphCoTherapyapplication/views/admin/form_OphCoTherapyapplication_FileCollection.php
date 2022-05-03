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
<table class="row standard">
    <colgroup>
        <col class="cols-1">
        <col class="cols-2">
    </colgroup>
    <tbody>
        <tr>
            <td><?= $model->getAttributeLabel('institution') ?></td>
            <td><?= \CHtml::activeDropDownList(
                $model,
                'institution_id',
                Institution::model()->getList(true),
                ['class' => 'cols-full', 'empty' => '- Institution -']
            ) ?></td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('name')?></td>
            <td><?=\CHtml::activeTextField($model, 'name', [
                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                    'class' => 'cols-full'
                    ])?></td>
        </tr>
        <tr>
            <td><?=$model->getAttributeLabel('summary')?></td>
            <td><?=\CHtml::activeTextArea($model, 'summary', [
                    'autocomplete' => SettingMetadata::model()->getSetting('html_autocomplete'),
                    'maxlength' => 40,
                    'class' => 'cols-full',
                    ])?></td>
        </tr>
    </tbody>
</table>

<?php if ($model->files) : ?>
<div class="row divider">
    <h4>Uploaded files:</h4>
</div>

    <div class="cols-cols-full">
        <table class="row standard">
            <colgroup>
                <col class="cols-6">
                <col class="cols-6">
            </colgroup>
            <tbody>
             <?php foreach ($model->files as $file) : ?>
                <tr>
                    <td><?php echo $file->name ?></td>
                    <td>
                        <a href="<?php echo $file->getDownloadURL() ?>">download</a> | <a class="removeFile">delete</a>
                    </td>
                </tr>
             <?php endforeach; ?>
            </tbody>
            <tfoot class="pagination-container">
            <tr>
                <td>
                    <a href="<?=Yii::app()->createUrl(
                        '/OphCoTherapyapplication/Default/downloadFileCollection',
                        ['id' => $model->id]
                             ) ?>"
                       class="button small"
                    >Download zip file
                    </a>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
<?php endif; ?>
<hr class="divider">

<div class="row divider">
    <h4>Add Files:</h4>
</div>
    <table class="standard" id="div_OphCoTherapyapplication_FileCollection_file">
        <colgroup>
            <col class="cols-5">
        </colgroup>
        <tr>
            <td>
                <input
                        type="file"
                        id="OphCoTherapyapplication_FileCollection_files"
                        class="OphCoTherapyapplication_FileCollection_file"
                        name="OphCoTherapyapplication_FileCollection_files[]"
                        multiple="multiple"
                        data-count-limit="<?=$this->returnBytes(ini_get('max_file_uploads')); ?>"
                        data-max-filesize="<?=$this->returnBytes(ini_get('upload_max_filesize')); ?>"
                        data-total-max-size="<?=$this->returnBytes(ini_get('post_max_size')); ?>"/>
            </td>
        </tr>
        <tr><td><div class="alert-box info">Maximum file size is <?php echo ini_get('upload_max_filesize'); ?></div></td></tr>
        <tr><td><div class="alert-box info">Maximum number of files is <?php echo ini_get('max_file_uploads'); ?></div></td></tr>
        <tr><td><div class="alert-box info">Maximum total upload size is <?php echo ini_get('post_max_size'); ?></div></td></tr>
    </table>

<div id="confirm_remove_file_dialog" title="Confirm remove file" style="display: none;">
    <div id="delete_file">
        <div class="alert-box alert with-icon">
            <strong>WARNING: This will remove the file from the collection. The file will still be attached to any
                applications that have been processed.</strong>
        </div>
        <p>
            <strong>Are you sure you want to proceed?</strong>
        </p>
        <div class="buttons">
            <input type="hidden" id="remove_file_id" value=""/>
            <button type="submit" class="warning btn_remove_file">Remove file</button>
            <button type="submit" class="secondary btn_cancel_remove_file">Cancel</button>
            <img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif') ?>"
                 alt="loading..." style="display: none;"/>
        </div>
    </div>
</div>
