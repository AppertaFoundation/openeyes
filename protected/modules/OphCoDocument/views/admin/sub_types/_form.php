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

<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/pdfjs-dist/build/pdf.min.js')?>"></script>
<script src="<?= Yii::app()->assetManager->createUrl('../../node_modules/pdfjs-dist/build/pdf.worker.min.js')?>"></script>
<?php
/**
 * @var $model OphCoDocument_Sub_Types
 */

$model_name = CHtml::modelName($model);
?>
<div class="cols-11">
    <table class="standard cols-full">
        <colgroup>
            <col class="cols-1">
            <col class="cols-4">
        </colgroup>
        <tbody>
        <tr class="hidden">
            <td>Id</td>
            <td>
                <?=\CHtml::activeTextField(
                    $model,
                    'id',
                    ['hidden' => true]
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Name</td>
            <td>
                <?=\CHtml::activeTextField(
                    $model,
                    'name',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Display Order</td>
            <td>
                <?=\CHtml::activeTextField(
                    $model,
                    'display_order',
                    ['class' => 'cols-full']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Is Active</td>
            <td>
                <?=\CHtml::activeRadioButtonList(
                    $model,
                    'is_active',
                    [1 => 'Yes', 0 => 'No'],
                    ['separator' => ' ', 'selected' => '1']
                ); ?>
            </td>
        </tr>
        <tr>
            <td>Sub type icon</td>
            <td>
                <fieldset>
                    <div class="cols-11">
                        <?php
                        $sub_type_event_icons = EventIcon::model()->findAll();
                        foreach ($sub_type_event_icons as $key => $icon) { ?>
                            <label class="inline highlight" for="<?= $model_name . '_sub_type_event_icon_id_' . $key?>">
                                <input type="radio" id="<?= $model_name . '_sub_type_event_icon_id_' . $key ?>" <?= $model->sub_type_event_icon_id === $icon->id ? 'checked="checked"' : '' ?>
                                       name="<?=$model_name?>[sub_type_event_icon_id]" value="<?= $icon->id ?>">
                                <i class="oe-i-e <?= $icon->name ?>"></i>
                            </label>
                        <?php } ?>
                    </div>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td>Template</td>
            <td>
                <div>
                    <span>
                        <?= CHtml::activeFileField($model, 'image', array('style' => 'width: 90%', 'accept' => implode(', ', $model->getAllowedFileMimeTypes()))); ?>
                        <i id="file-remove" class="oe-i remove-circle medium pro-theme pad js-close-hotlist-item"></i>
                    </span>
                </div>
                <div class="data-group fade">
                    The following file types are accepted: <?php echo implode(', ', $model->getAllowedFileTypes()); ?> (Maximum
                    size: <?= $model->getMaxDocumentSize(); ?> MB)
                </div>
            </td>
        </tr>
        <?php
        if ($model->templateImage) {
            $showTemplate = true;
        }
        ?>
        <tr id="ophco-template-row" <?= isset($showTemplate) ? '' : 'style="display: none;"';?>>
            <td colspan="2">
                <div id="ophco-template-container">
                    <img id="ophco-template" src="<?= isset($model->templateImage->id) && ($model->templateImage->mimetype !== 'application/pdf') ? '/file/view/' . $model->templateImage->id . '/image' . strrchr($model->templateImage->name, '.') : '' ?>"
                         border="0" style="max-width: 100%">
                    <div id="ophco-template-pdf" style="<?= (isset($model->templateImage) && ($model->templateImage->mimetype !== 'application/pdf') ?  'display:none;' : '') ?>" >
                        <iframe id="pdf-js-viewer" src="<?= isset($model->templateImage->id) && ($model->templateImage->mimetype === 'application/pdf') ? Yii::app()->assetManager->createUrl('components/pdfjs/web/viewer.html?file=/file/view/' . $model->templateImage->id . '/image' . strrchr($model->templateImage->name, '.') . '#zoom=70') : ''?>"
                                title="webviewer" style="width: 70%" height="800px" type="application/pdf"></iframe>
                    </div>
                </div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?=\CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large primary event-action',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?=\CHtml::submitButton(
                    'Cancel',
                    [
                        'data-uri' => '/' . $this->module->id . '/' . $this->id,
                        'class' => 'warning button large primary event-action',
                        'name' => 'cancel',
                        'id' => 'et_cancel',
                    ]
                ); ?>
            </td>
        </tr>
        </tfoot>
    </table>
</div>

<script>
    let elementIds = {
        previewContainer: 'ophco-template-row',
        templateContainer: 'ophco-template-container',
        image: 'ophco-template',
        pdfContainer: 'ophco-template-pdf',
    };

    $("#OphCoDocument_Sub_Types_image").on("change", function(e){
        displayPreview(e);
    });

    function displayPreview(event) {
        let $previewContainerElement = $('#' + elementIds.previewContainer);
        let file = event.target.files[0];
        if (file) {
            let size = file.size;
            let max_size = <?= $model->getMaxDocumentSize(false) ?>;
            if (size > max_size) {
                clearUploadFieldAndPreview();
                new OpenEyes.UI.Dialog.Alert({
                    content: 'The file you tried to upload exceeds the maximum allowed file size, which is ' + (max_size/1048576) + ' MB'
                }).open();
            } else {
                let fileReader = new FileReader();
                $previewContainerElement.hide();
                // Only show the previews for jpg, png and pdf's.
                if (file.type === "application/pdf") {
                    let url = URL.createObjectURL(file);
                    let source = `${OE_core_asset_path}/components/pdfjs/web/viewer.html?file=${url}`;
                    // Check if pdf preview image source exists
                    $.get(source).done(function () {
                        document.getElementById('pdf-js-viewer').src = source;
                        let $pdfContainer = $('#' + elementIds.pdfContainer);
                        let $imageElement = $('#' + elementIds.image);
                        $pdfContainer.show();
                        $previewContainerElement.show();
                        $imageElement.hide();
                    }).fail(function () {
                        clearUploadFieldAndPreview();
                        alert('Something went wrong trying to upload file. Please try again.')
                    });
                }
                if ((file.type === 'image/jpeg') || (file.type === 'image/png')) {
                    displayImage(file, fileReader);
                }
            }
        }
    }

    function displayImage(file, fileReader) {
        let $previewContainerElement = $('#' + elementIds.previewContainer);
        let $pdfContainer = $('#' + elementIds.pdfContainer);
        let $imageElement = $('#' + elementIds.image);
        fileReader.onload = function(e) {
            // hide the canvas before showing the image.
            $pdfContainer.hide();
            $previewContainerElement.show();
            $imageElement.show();
            $imageElement.attr('src', e.target.result);
        }
        fileReader.readAsDataURL(file); // convert to base64 string
    }

    function clearUploadFieldAndPreview()
    {
        document.getElementById('OphCoDocument_Sub_Types_image').value = null;
        document.getElementById('ophco-template').removeAttribute('src');
        document.getElementById('ophco-template-row').style.display = 'none';
    }

    document.getElementById('file-remove').addEventListener('click', clearUploadFieldAndPreview);
</script>
