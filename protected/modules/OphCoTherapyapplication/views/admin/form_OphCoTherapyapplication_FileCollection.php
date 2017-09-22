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

<?php echo $form->textField($model, 'name', array('autocomplete' => Yii::app()->params['html_autocomplete'], 'maxlength' => 40))?>
<?php	echo $form->textArea($model, 'summary', array(), false, array(), array('field' => 10))?>

<?php	if ($model->files) {?>
	<div class="row field-row">
		<div class="<?php echo $form->columns('label');?>">
			<div class="field-label">
				Uploaded files:
			</div>
		</div>
		<div class="<?php echo $form->columns('field');?>">
			<div class="field-value field-row">
				<a href="<?php echo Yii::app()->createUrl('/OphCoTherapyapplication/Default/downloadFileCollection', array('id' => $model->id)) ?>">Download zip</a>
			</div>
			<ul id="currentFiles" class="current-files">
				<?php foreach ($model->files as $file) { ?>
					<li data-file-id="<?php echo $file->id ?>">
						<a href="<?php echo $file->getDownloadURL() ?>"><?php echo $file->name ?></a> | <a href="#" class="removeFile">delete</a>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
<?php }?>

<?php
/**
 * utility function that should probably sit somewhere else, but is only for this template at the moment
 * calculates the byte size of the passed in value.
 *
 * @param $size_str
 *
 * @return int
 */
function return_bytes($size_str)
{
    switch (substr($size_str, -1)) {
        case 'M': case 'm': return (int) $size_str * 1048576;
        case 'K': case 'k': return (int) $size_str * 1024;
        case 'G': case 'g': return (int) $size_str * 1073741824;
        default: return $size_str;
    }
}
?>


<div id="div_OphCoTherapyapplication_FileCollection_file" class="row field-row">
	<div class="<?php echo $form->columns('label');?>">
		<label for="OphCoTherapyapplication_FileCollection_files">File(s):</label>
	</div>
	<div class="<?php echo $form->columns('field');?>">
		<div class="field-row">
			<input
				type="file"
				id="OphCoTherapyapplication_FileCollection_files"
				class="OphCoTherapyapplication_FileCollection_file"
				name="OphCoTherapyapplication_FileCollection_files[]"
			  multiple="multiple"
			  data-count-limit="<?php echo return_bytes(ini_get('max_file_uploads')); ?>"
				data-max-filesize="<?php echo return_bytes(ini_get('upload_max_filesize')); ?>"
				data-total-max-size="<?php echo return_bytes(ini_get('post_max_size')); ?>" />
			<!--
			if non-html5 browser being used, this could be reinstated to add multiple files
			<button class="addFile classy green mini" type="button">
				<span class="button-span button-span-green">Add File</span>
			</button>
			-->
		</div>
		<div class="field-info">
			Maximum file size is <?php echo ini_get('upload_max_filesize'); ?><br />
			Maximum number of files is <?php echo ini_get('max_file_uploads'); ?><br />
			Maximum total upload size is <?php echo ini_get('post_max_size'); ?>
		</div>
	</div>
</div>

<div id="confirm_remove_file_dialog" title="Confirm remove file" style="display: none;">
	<div id="delete_file">
		<div class="alert-box alert with-icon">
			<strong>WARNING: This will remove the file from the collection. The file will still be attached to any applications that have been processed.</strong>
		</div>
		<p>
			<strong>Are you sure you want to proceed?</strong>
		</p>
		<div class="buttons">
			<input type="hidden" id="remove_file_id" value="" />
			<button type="submit" class="warning btn_remove_file">Remove file</button>
			<button type="submit" class="secondary btn_cancel_remove_file">Cancel</button>
			<img class="loader" src="<?php echo Yii::app()->assetManager->createUrl('img/ajax-loader.gif')?>" alt="loading..." style="display: none;" />
		</div>
	</div>
</div>
