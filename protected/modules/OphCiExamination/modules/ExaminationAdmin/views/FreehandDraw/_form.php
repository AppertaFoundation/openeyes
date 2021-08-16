<?php
/**
 * OpenEyes
 *
 * (C) OpenEyes Foundation, 2021
 * This file is part of OpenEyes.
 * OpenEyes is free software: you can redistribute it and/or modify it under the terms of the GNU Affero General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
 * OpenEyes is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License along with OpenEyes in a file titled COPYING. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package OpenEyes
 * @link http://www.openeyes.org.uk
 * @author OpenEyes <info@openeyes.org.uk>
 * @copyright Copyright (c) 2021, OpenEyes Foundation
 * @license http://www.gnu.org/licenses/agpl-3.0.html The GNU Affero General Public License V3.0
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
            <td><?= \CHtml::activeTextField($model, 'id', ['hidden' => true]); ?></td>
        </tr>
        <tr>
            <td>Name</td>
            <td><?= \CHtml::activeTextField($model, 'name', ['class' => 'cols-full']); ?></td>
        </tr>
        <tr>
            <td>Display Order</td>
            <td><?= \CHtml::activeTextField($model, 'display_order', ['class' => 'cols-full']); ?></td>
        </tr>
        <tr>
            <td>Is Active</td>
            <td><?= \CHtml::activeRadioButtonList($model, 'active', [1 => 'Yes', 0 => 'No'], ['separator' => ' ', 'selected' => '1']); ?>
            </td>
        </tr>
        <tr>
            <td>Template</td>
            <td>
                <div>
                    <span>
                        <?= \CHtml::activeFileField($model, 'image', array('style' => 'width: 90%', 'accept' => implode(', ', $model->getAllowedFileMimeTypes()))); ?>
                        <i id="file-remove" class="oe-i remove-circle medium pro-theme pad js-close-hotlist-item"></i>
                    </span>
                </div>
                <div class="data-group fade">
                    The following file types are accepted: <?php echo implode(', ', $model->getAllowedFileTypes()); ?>
                    (Maximum size: <?= $model->getMaxDocumentSize(); ?> MB)
                </div>
            </td>
        </tr>
        <?php
        if ($model->protected_file) {
            $show_template = true;
        }
        ?>
        <tr id="ophco-template-row" <?= isset($show_template) ? '' : 'style="display: none;"'; ?>>
            <td colspan="2">
                <div id="ophco-template-container">
                    <img id="ophco-template"
                         src="<?= isset($model->protected_file->id) ? '/file/view/' . $model->protected_file->id . '/image' . strrchr($model->protected_file->name, '.') : '' ?>"
                         border="0" style="max-width: 100%">
                </div>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">
                <?= \CHtml::submitButton(
                    'Save',
                    [
                        'class' => 'button large primary event-action',
                        'name' => 'save',
                        'id' => 'et_save'
                    ]
                ); ?>
                <?= \CHtml::submitButton(
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
        image: 'ophco-template'
    };

    $("#DrawingTemplate_image").on("change", function (e) {
        displayPreview(e);
    });

    function displayPreview(event) {
        let $previewContainerElement = $('#' + elementIds.previewContainer);
        let file = event.target.files[0];
        if (file) {
            let fileReader = new FileReader();
            $previewContainerElement.hide();
            // Only show the previews for jpg, png and pdf's.
            if ((file.type === 'image/jpeg') || (file.type === 'image/png')) {
                displayImage(file, fileReader);
            }
        }
    }

    function displayImage(file, fileReader) {
        let $previewContainerElement = $('#' + elementIds.previewContainer);
        let $pdfContainer = $('#' + elementIds.pdfContainer);
        let $imageElement = $('#' + elementIds.image);
        fileReader.onload = function (e) {
            // hide the canvas before showing the image.
            $pdfContainer.hide();
            $previewContainerElement.show();
            $imageElement.show();
            $imageElement.attr('src', e.target.result);
        }
        fileReader.readAsDataURL(file); // convert to base64 string
    }

        document.getElementById('file-remove').addEventListener('click', function () {
        document.getElementById('OphCoDocument_Sub_Types_image').value = null;
        document.getElementById('ophco-template').removeAttribute('src');
        document.getElementById('ophco-template-row').style.display = 'none';
    });
</script>